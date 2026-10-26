<?php
/** Explicit historical import and bounded, signed-snapshot editorial approvals. */
defined('ABSPATH')||exit;
class SRM_History_Setup {
    public static function init(): void {
        add_action('admin_menu',function(){if(SRM_Access::current_user_has_full_access())add_submenu_page('srm-dashboard','Historical setup','Historical setup',current_user_can('manage_options')?'manage_options':'manage_scheduled_results','srm-history-setup',array(__CLASS__,'page'));});
        add_action('admin_post_srm_history_setup',array(__CLASS__,'handle'));
    }
    public static function token(object $row): string {
        $record=SRM_Results::get_result((int)$row->event_id,$row->result_date,true);
        $base=$row->event_id.'|'.$row->result_date.'|'.SRM_Results::record_hash($record);
        return $base.'|'.wp_hash($base.'|'.get_current_user_id());
    }
    public static function approve(array $tokens,bool $confirmed): array|WP_Error {
        if(!SRM_Access::current_user_has_full_access())return new WP_Error('permission','Permission denied.');
        if(!$confirmed||!count($tokens)||count($tokens)>40)return new WP_Error('confirmation','Explicit review and 1–40 selected records required.');
        $records=array();
        foreach($tokens as $token)if(!is_string($token))return new WP_Error("token","Invalid selection.");
        foreach(array_unique($tokens) as $token){
            if(!is_string($token)||strlen($token)>200)return new WP_Error('token','Invalid selection.');
            $v=explode('|',$token);if(count($v)!==4||!hash_equals(wp_hash(implode('|',array_slice($v,0,3)).'|'.get_current_user_id()),$v[3]))return new WP_Error('token','Invalid selection. Reload this page.');
            $r=SRM_Results::get_result(absint($v[0]),$v[1],true);
            if(!$r||'pending'!==$r->result_status||!hash_equals(SRM_Results::record_hash($r),$v[2]))return new WP_Error('stale','A selected record changed. Nothing was published; reload and review again.');
            $records[]=array($r,$v[2]);
        }
        $summary=array('published'=>0,'failed'=>0);
        foreach($records as [$r,$hash]){$result=SRM_Results::save_result((int)$r->event_id,$r->result_date,array('result_value'=>$r->result_value,'original_game_name'=>$r->original_game_name,'source_url'=>$r->source_url,'verification_notes'=>$r->verification_notes,'verification_status'=>'verified','result_status'=>'published','confirm_review'=>1,'expected_hash'=>$hash));$summary[is_wp_error($result)?'failed':'published']++;}
        return $summary;
    }
    public static function import_bundled(): array {
        if(!SRM_Access::current_user_has_full_access())return array('imported'=>0,'skipped'=>0,'duplicate'=>0,'invalid'=>0,'errors'=>array('Permission denied.'));
        if(!function_exists('wp_tempnam'))require_once ABSPATH.'wp-admin/includes/file.php';
        $tmp=wp_tempnam('gbk-history.csv');
        if(!$tmp)return array('imported'=>0,'skipped'=>0,'duplicate'=>0,'invalid'=>0,'errors'=>array('Cannot create temporary CSV.'));
        try{chmod($tmp,0600);file_put_contents($tmp,require SRM_PLUGIN_DIR.'data/history.php');return SRM_Import_Export::import_csv($tmp);}
        finally{if(is_file($tmp))unlink($tmp);}
    }
    public static function handle(): void {
        if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.','',array('response'=>403));
        if('POST'!==($_SERVER['REQUEST_METHOD']??''))wp_die('POST required.','',array('response'=>405));
        check_admin_referer('srm_history_setup');$task=sanitize_key($_POST['task']??'');
        if('import'===$task){$r=self::import_bundled();$message=sprintf('Import: %d imported; %d skipped; %d duplicate; %d invalid. All new records are drafts. %s',$r['imported'],$r['skipped'],$r['duplicate'],$r['invalid'],implode(' ',$r['errors']));}
        elseif('approve'===$task){$r=self::approve(is_array($_POST['records']??null)?wp_unslash($_POST['records']):array(),'1'===($_POST['confirm_review']??''));$message=is_wp_error($r)?$r->get_error_message():sprintf('Published %d; failed %d. Failed records remain unchanged; check editing lock and record fields.',$r['published'],$r['failed']);}
        else wp_die('Unknown action.');
        set_transient('srm_history_'.get_current_user_id(),$message,90);wp_safe_redirect(admin_url('admin.php?page=srm-history-setup'));exit;
    }
    private static function form(string $task): void {
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="srm_history_setup"><input type="hidden" name="task" value="'.esc_attr($task).'">';wp_nonce_field('srm_history_setup');
    }
    public static function page(): void {
        if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.');
        echo '<div class="wrap"><h1>Historical data — import and editorial approval</h1><p>523 existing records, 26 August–26 October 2026, covering 11 of 14 games. Coverage is incomplete. No synthetic numbers. Source transcription is not independent certification.</p>';
        if($notice=get_transient('srm_history_'.get_current_user_id())){echo '<div class="notice notice-info"><p>'.esc_html($notice).'</p></div>';delete_transient('srm_history_'.get_current_user_id());}
        self::form('import');submit_button('Import bundled historical CSV as drafts');echo '</form><p>Repeat imports preserve existing rows and report duplicates. Full audit log: <a href="'.esc_url(admin_url('admin.php?page=srm-import-export')).'">Import / Export</a>.</p><hr><h2>Review pending historical records</h2><p>Open each source, check the original game name, date and exact value. Select only records you personally approve. Nothing is selected by default. Sources and verification notes below are administrative, not table columns on the public site.</p>';
        $page=max(1,absint($_GET['review_page']??1));$rows=SRM_Results::query(array('start'=>'2026-08-26','end'=>'2026-10-26','status'=>'pending','page'=>$page,'limit'=>40),true);
        self::form('approve');echo '<table class="widefat striped"><thead><tr><th>Select</th><th>Game / original name</th><th>Date</th><th>Value</th><th>Source and private notes</th></tr></thead><tbody>';
        foreach($rows['rows'] as $r)echo '<tr><td><input type="checkbox" name="records[]" aria-label="'.esc_attr($r->event_name.' '.$r->result_date).'" value="'.esc_attr(self::token($r)).'"></td><td>'.esc_html($r->event_name.' / '.$r->original_game_name).'</td><td>'.esc_html($r->result_date).'</td><td>'.esc_html($r->result_value).'</td><td><a target="_blank" rel="noopener noreferrer" href="'.esc_url($r->source_url).'">'.esc_html($r->source_url).'</a><br>'.esc_html($r->verification_notes).'</td></tr>';
        echo '</tbody></table><p><label><input type="checkbox" required name="confirm_review" value="1"> I personally checked each selected game, date and exact value against the source, and approve publication of these versions.</label></p>';submit_button('Publish selected, reviewed records');echo '</form>';
        echo wp_kses_post(paginate_links(array('base'=>add_query_arg('review_page','%#%'),'format'=>'','current'=>$page,'total'=>$rows['pages'])));echo '</div>';
    }
}
