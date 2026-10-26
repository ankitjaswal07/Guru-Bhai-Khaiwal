<?php
/**
 * Admin Class
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SRM_Admin
 */
class SRM_Admin {

    /**
     * Current admin page slug.
     *
     * @var string
     */
    private string $current_page = '';

    /**
     * Init admin hooks.
     */
    public function init(): void {
        add_action( 'admin_menu',            array( $this, 'register_menus' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_notices',         array( $this, 'admin_notices' ) );
        add_action( 'admin_init',            array( $this, 'handle_actions' ) );
        foreach(array('srm_save_result','srm_toggle_game','srm_save_event_order') as $action){add_action('wp_ajax_'.$action,array('SRM_Admin_Actions','ajax'));}
        add_action( 'show_user_profile',     array( $this, 'render_user_access_fields' ) );
        add_action( 'edit_user_profile',     array( $this, 'render_user_access_fields' ) );
        add_action( 'personal_options_update', array( $this, 'save_user_access_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_user_access_fields' ) );
    }

    /**
     * Register admin menus.
     */
    public function register_menus(): void {
        if (!SRM_Access::current_user_can_access_plugin()) return;
        $cap=SRM_Access::current_user_has_full_access() ? (current_user_can('manage_options')?'manage_options':'manage_scheduled_results') : SRM_Access::capability();
        add_menu_page('Scheduled Results',sanitize_text_field(get_option('srm_plugin_name','Scheduled Results')),$cap,'srm-dashboard',array($this,'page_dashboard'),'dashicons-calendar-alt',30);
        foreach(array('dashboard'=>'Dashboard','events'=>'Games','results'=>'Daily Results','monthly'=>'Monthly Results','settings'=>'Settings','import-export'=>'Import / Export') as $slug=>$label){
            if(!SRM_Access::current_user_has_full_access() && in_array($slug,array('events','settings','import-export'),true))continue;
            add_submenu_page('srm-dashboard',$label,$label,$cap,'srm-'.$slug,array($this,'page_'.str_replace('-','_',$slug)));
        }
        if(SRM_Access::current_user_has_full_access())add_submenu_page('srm-dashboard','Records & source review','Records & source review',$cap,'srm-review',array('SRM_Review_Desk','page'));
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( string $hook ): void {
        // Only on our pages
        if ( strpos( $hook, 'srm-' ) === false && strpos( $hook, 'srm_' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'srm-admin',
            SRM_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            SRM_VERSION
        );

        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_style('wp-color-picker');wp_enqueue_script('wp-color-picker');

        wp_enqueue_script(
            'srm-admin',
            SRM_PLUGIN_URL . 'admin/js/admin.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            SRM_VERSION,
            true
        );

        wp_localize_script( 'srm-admin', 'srmAdmin', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'srm_admin_nonce' ),
            'strings'     => array(
                'confirmDelete'  => __( 'Are you sure you want to delete this?', 'scheduled-results-manager' ),
                'saving'         => __( 'Saving…', 'scheduled-results-manager' ),
                'saved'          => __( 'Saved!', 'scheduled-results-manager' ),
                'error'          => __( 'An error occurred. Please try again.', 'scheduled-results-manager' ),
            ),
        ) );
    }

    /**
     * Display admin notices from redirects.
     */
    public function admin_notices(): void {
        if(!SRM_Access::current_user_can_access_plugin())return;
        $notice=get_transient('srm_notice_'.get_current_user_id())?:get_transient('gbk_notice_'.get_current_user_id());if(!$notice)return;
        echo '<div class="notice notice-'.($notice['error']?'error':'success').'"><p>'.esc_html($notice['message']).'</p></div>';delete_transient('srm_notice_'.get_current_user_id());delete_transient('gbk_notice_'.get_current_user_id());
    }

    /**
     * Handle non-AJAX admin actions (form submissions).
     */
    public function handle_actions(): void {
        $action=sanitize_key($_REQUEST['srm_action']??'');if(!$action)return;
        if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.','',array('response'=>403));
        if('POST'!==($_SERVER['REQUEST_METHOD']??''))wp_die('POST required.','',array('response'=>405));
        $data=wp_unslash($_POST);$id=absint($data['event_id']??0);
        $nonce='srm_'.$action.(in_array($action,array('duplicate_game','delete_game'),true)?'_'.$id:'');
        check_admin_referer($nonce);$result=true;$page='srm-events';
        switch($action){
            case 'create_game': case 'update_game':
                $data['status']=isset($data['status'])?1:0;
                $result='create_game'===$action?SRM_Games::create_game($data):SRM_Games::update_game($id,$data);break;
            case 'duplicate_game':$result=SRM_Games::duplicate_game($id);break;
            case 'delete_game':$result=new WP_Error('retention','Deletion is disabled to preserve historical references. Disable the game instead.');break;
            case 'save_settings':SRM_Settings::save($data);$page='srm-settings';break;
            case 'export_csv':SRM_Import_Export::export_csv(sanitize_text_field($data['export_start']??''),sanitize_text_field($data['export_end']??''));break;
            default:$result=new WP_Error('action','Unknown action. Use Import / Export for secure CSV uploads.');
        }
        set_transient('srm_notice_'.get_current_user_id(),array('error'=>is_wp_error($result),'message'=>is_wp_error($result)?$result->get_error_message():'Saved.'),60);
        wp_safe_redirect(admin_url('admin.php?page='.$page));exit;
    }

    /**
     * Render allowed games selection in a user profile.
     *
     * @param WP_User $user User being edited.
     */
    public function render_user_access_fields( WP_User $user ): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        wp_nonce_field('srm_access_'.(int)$user->ID,'srm_access_nonce');
        $games    = SRM_Games::get_all_games();
        $selected = SRM_Access::get_user_allowed_game_ids( (int) $user->ID );
        $selected_map = array_fill_keys( $selected, true );
        ?>
        <h3><?php esc_html_e( 'Scheduled Results Manager', 'scheduled-results-manager' ); ?></h3>
        <table class="form-table">
            <tr>
                <th><label><?php esc_html_e( 'Allowed Games', 'scheduled-results-manager' ); ?></label></th>
                <td>
                    <?php if ( empty( $games ) ) : ?>
                        <p><?php esc_html_e( 'No games yet. Add a game first to assign access.', 'scheduled-results-manager' ); ?></p>
                    <?php else : ?>
                        <fieldset>
                            <?php foreach ( $games as $game ) : ?>
                                <label style="display:block;margin:0 0 8px;">
                                    <input type="checkbox" name="srm_allowed_games[]" value="<?php echo esc_attr( (string) $game->id ); ?>" <?php checked( ! empty( $selected_map[ (int) $game->id ] ) ); ?>>
                                    <?php echo esc_html( $game->event_name ); ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php endif; ?>
                    <p class="description"><?php esc_html_e( 'Assigned operators may edit drafts for selected games. Only full results managers may review and publish.', 'scheduled-results-manager' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save allowed games from user profile.
     *
     * @param int $user_id User being saved.
     */
    public function save_user_access_fields(int $user_id): void {
        if(!current_user_can('manage_options') || !current_user_can('edit_user',$user_id) || empty($_POST['srm_access_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['srm_access_nonce'])),'srm_access_'.$user_id))return;
        $ids=array_map('absint',(array)($_POST['srm_allowed_games']??array()));
        $valid=array_map(static fn($g)=>(int)$g->id,SRM_Games::get_all_games());
        SRM_Access::save_user_allowed_games($user_id,array_values(array_intersect($ids,$valid)));
    }

    // -----------------------------------------------------------------------
    // Page renderers
    // -----------------------------------------------------------------------

    public function page_dashboard(): void {
        require SRM_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function page_events(): void {
        if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.','',array('response'=>403));
        require SRM_PLUGIN_DIR . 'admin/views/events.php';
    }

    public function page_results(): void {
        require SRM_PLUGIN_DIR . 'admin/views/results.php';
    }

    public function page_monthly(): void {
        require SRM_PLUGIN_DIR . 'admin/views/monthly-results.php';
    }

    public function page_settings(): void {
        if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.','',array('response'=>403));
        require SRM_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public function page_import_export(): void {
        if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.','',array('response'=>403));
        require SRM_PLUGIN_DIR . 'admin/views/import-export.php';
    }

}
