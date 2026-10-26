<?php
/** Native, progressively enhanced administrative workflows. */
defined( 'ABSPATH' ) || exit;
class SRM_Admin {
    public static function init(): void {
        add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
        add_action( 'admin_post_gbk_manage', array( __CLASS__, 'handle' ) );
    }
    public static function menu(): void {
        add_menu_page( 'Guru Bhai Records', 'Guru Bhai Records', 'manage_scheduled_results', 'srm-results', array( __CLASS__, 'page' ), 'dashicons-calendar-alt', 26 );
        foreach ( array( 'srm-results' => 'Historical records', 'srm-events' => 'Schedule', 'srm-import-export' => 'Import / export', 'srm-settings' => 'Help & settings' ) as $slug => $title ) {
            add_submenu_page( 'srm-results', $title, $title, 'manage_scheduled_results', $slug, array( __CLASS__, 'page' ) );
        }
    }
    private static function guard(): void {
        if ( ! SRM_Access::current_user_has_full_access() ) { wp_die( 'Permission denied.', '', array( 'response' => 403 ) ); }
    }
    public static function handle(): void {
        self::guard();
        if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { wp_die( 'POST required.', '', array( 'response' => 405 ) ); }
        check_admin_referer( 'gbk_manage' );
        $data = wp_unslash( $_POST );
        $task = sanitize_key( $data['task'] ?? '' );
        $page = 'srm-results'; $result = true;
        switch ( $task ) {
            case 'game':
                $page = 'srm-events';
                $data['status'] = isset( $data['status'] ) ? 1 : 0;
                if ( empty( trim( $data['event_name'] ?? '' ) ) || mb_strlen( $data['event_name'] ) > 150 || mb_strlen( $data['event_name_secondary'] ?? '' ) > 150 ) { $result = new WP_Error( 'name', 'Names must contain 1–150 characters (secondary name is optional).' ); break; }
                $result = empty( $data['event_id'] ) ? SRM_Games::create_game( $data ) : SRM_Games::update_game( absint( $data['event_id'] ), $data );
                break;
            case 'record':
                $result = SRM_Results::save_result( absint( $data['event_id'] ?? 0 ), sanitize_text_field( $data['result_date'] ?? '' ), $data );
                break;
            case 'import':
                $page = 'srm-import-export';
                $file = $_FILES['csv'] ?? array();
                if ( UPLOAD_ERR_OK !== ( $file['error'] ?? -1 ) || ! is_uploaded_file( $file['tmp_name'] ?? '' ) || 'csv' !== strtolower( pathinfo( $file['name'] ?? '', PATHINFO_EXTENSION ) ) || ( $file['size'] ?? 0 ) > 2097152 ) {
                    $result = new WP_Error( 'file', 'Upload a .csv file of 2 MB or less.' ); break;
                }
                $summary = SRM_Import_Export::import_csv( $file['tmp_name'] );
                $result = $summary['success'] ? true : new WP_Error( 'csv', implode( ' ', $summary['errors'] ) );
                break;
            case 'export': case 'template':
                SRM_Import_Export::export_csv( sanitize_text_field( $data['start'] ?? '' ), sanitize_text_field( $data['end'] ?? '' ), 'template' === $task );
                break;
            default: $result = new WP_Error( 'task', 'Unknown action.' );
        }
        set_transient( 'gbk_notice_' . get_current_user_id(), array( 'error' => is_wp_error( $result ), 'message' => is_wp_error( $result ) ? $result->get_error_message() : ( 'import' === $task ? 'Import processed. Review the row counts and errors below. All new records are drafts.' : 'Saved. Public pages now reflect eligible records and active schedules.' ) ), 90 );
        wp_safe_redirect( admin_url( 'admin.php?page=' . $page ) ); exit;
    }
    private static function form( string $task ): void {
        echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gbk_manage"><input type="hidden" name="task" value="' . esc_attr( $task ) . '">';
        wp_nonce_field( 'gbk_manage' );
    }
    private static function field( string $label, string $name, string $value = '', string $type = 'text', bool $required = false ): void {
        echo '<p><label><strong>' . esc_html( $label ) . '</strong><br><input class="regular-text" type="' . esc_attr( $type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" ' . ( $required ? 'required' : '' ) . '></label></p>';
    }
    public static function page(): void {
        self::guard();
        $page = sanitize_key( $_GET['page'] ?? 'srm-results' );
        echo '<div class="wrap"><h1>Guru Bhai Khaiwal · Records desk</h1><p>Informational schedules and historical records. No payments, predictions or wagering.</p>';
        $notice = get_transient( 'gbk_notice_' . get_current_user_id() );
        if ( $notice ) { echo '<div class="notice notice-' . ( $notice['error'] ? 'error' : 'success' ) . '"><p>' . esc_html( $notice['message'] ) . '</p></div>'; delete_transient( 'gbk_notice_' . get_current_user_id() ); }
        echo '<nav class="nav-tab-wrapper" aria-label="Record management">';
        foreach ( array( 'srm-results' => 'Records & review', 'srm-events' => 'Daily schedule', 'srm-import-export' => 'CSV & import logs', 'srm-settings' => 'Help & settings' ) as $slug => $label ) {
            echo '<a class="nav-tab ' . ( $slug === $page ? 'nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'admin.php?page=' . $slug ) ) . '">' . esc_html( $label ) . '</a>';
        }
        echo '</nav>';
        switch ( $page ) {
            case 'srm-events': self::games(); break;
            case 'srm-import-export': self::csv(); break;
            case 'srm-settings': self::help(); break;
            default: self::records();
        }
        echo '</div>';
    }
    private static function games(): void {
        $edit = SRM_Games::get_game( absint( $_GET['edit'] ?? 0 ) );
        echo '<h2>' . ( $edit ? 'Edit game' : 'Add game' ) . '</h2><p>Use 24-hour local time. Lower order numbers appear first. Uncheck Active to hide a game without deleting its history.</p>';
        self::form( 'game' );
        echo '<input type="hidden" name="event_id" value="' . absint( $edit->id ?? 0 ) . '">';
        self::field( 'English / primary name', 'event_name', $edit->event_name ?? '', 'text', true );
        self::field( 'Hindi / secondary name', 'event_name_secondary', $edit->event_name_secondary ?? '' );
        self::field( 'Local time · ' . wp_timezone_string(), 'scheduled_time', $edit->scheduled_time ?? '', 'time', true );
        self::field( 'Display order', 'display_order', (string) ( $edit->display_order ?? 15 ), 'number', true );
        echo '<p><label><input type="checkbox" name="status" value="1" ' . checked( $edit ? $edit->status : 1, 1, false ) . '> Active / publicly visible</label></p>';
        submit_button( $edit ? 'Save schedule' : 'Add game' ); echo '</form>';
        echo '<h2>All schedule entries</h2><div style="overflow-x:auto"><table class="widefat striped"><thead><tr><th>Name</th><th>Hindi</th><th>CSV slug</th><th>Time</th><th>Order</th><th>Visibility</th><th>Action</th></tr></thead><tbody>';
        foreach ( SRM_Games::get_all_games() as $g ) {
            echo '<tr><td>' . esc_html( $g->event_name ) . '</td><td lang="hi">' . esc_html( $g->event_name_secondary ?? '' ) . '</td><td><code>' . esc_html( $g->slug ) . '</code></td><td>' . esc_html( SRM_Games::format_time( $g->scheduled_time ) ) . '</td><td>' . absint( $g->display_order ) . '</td><td>' . ( $g->status ? 'Active' : 'Inactive' ) . '</td><td><a href="' . esc_url( admin_url( 'admin.php?page=srm-events&edit=' . $g->id ) ) . '">Edit</a></td></tr>';
        }
        echo '</tbody></table></div>';
    }
    private static function records(): void {
        $game_id = absint( $_GET['edit_game'] ?? 0 );
        $edit_date = sanitize_text_field( wp_unslash( $_GET['edit_date'] ?? '' ) );
        $edit = $game_id ? SRM_Results::get_result( $game_id, $edit_date, true ) : null;
        echo '<h2>' . ( $edit ? 'Review / edit record' : 'Add a historical record' ) . '</h2><p>Publishing requires a source URL, Verified status and your explicit review confirmation. Unverified legacy records stay private.</p>';
        self::form( 'record' );
        if ( $edit ) {
            echo '<input type="hidden" name="expected_hash" value="' . esc_attr( SRM_Results::record_hash( $edit ) ) . '"><input type="hidden" name="event_id" value="' . absint( $edit->event_id ) . '"><input type="hidden" name="result_date" value="' . esc_attr( $edit->result_date ) . '"><p><strong>' . esc_html( ( SRM_Games::get_game( $game_id )->event_name ?? '' ) . ' · ' . $edit->result_date ) . '</strong></p>';
        } else {
            echo '<p><label><strong>Game</strong><br><select name="event_id" required><option value="">Choose a game</option>';
            foreach ( SRM_Games::get_all_games() as $g ) { echo '<option value="' . absint( $g->id ) . '">' . esc_html( $g->event_name ) . '</option>'; }
            echo '</select></label></p>';
            self::field( 'Record date (source calendar date)', 'result_date', wp_date( 'Y-m-d' ), 'date', true );
        }
        self::field( 'Historical value (leading zeroes are kept)', 'result_value', $edit->result_value ?? '', 'text', true );
        self::field( 'Original game name on source', 'original_game_name', $edit->original_game_name ?? '', 'text', true );
        self::field( 'Source URL', 'source_url', $edit->source_url ?? '', 'url', true );
        if ( $edit && SRM_Results::valid_source( $edit->source_url ?? '' ) ) { echo '<p><a target="_blank" rel="noopener noreferrer" href="' . esc_url( $edit->source_url ) . '">Open source to verify (new tab) ↗</a></p>'; }
        echo '<p><label><strong>Verification</strong><br><select name="verification_status">';
        foreach ( array( 'unverified' => 'Unverified', 'verified' => 'Verified against source', 'disputed' => 'Disputed — do not publish' ) as $value => $label ) { echo '<option value="' . esc_attr( $value ) . '" ' . selected( $edit->verification_status ?? 'unverified', $value, false ) . '>' . esc_html( $label ) . '</option>'; }
        echo '</select></label></p><p><label><strong>Publication</strong><br><select name="result_status"><option value="pending" ' . selected( $edit->result_status ?? 'pending', 'pending', false ) . '>Draft / pending review</option><option value="published" ' . selected( $edit->result_status ?? '', 'published', false ) . '>Published</option></select></label></p><p><label><strong>Verification notes (private)</strong><br><textarea class="large-text" name="verification_notes" rows="3" maxlength="4000">' . esc_textarea( $edit->verification_notes ?? '' ) . '</textarea></label></p><p><label><input type="checkbox" name="confirm_review" value="1"> I checked this game, calendar date and exact value against the source. I approve this version.</label></p>';
        if ( $edit && $edit->reviewed_by ) { echo '<p>Last reviewer ID: ' . absint( $edit->reviewed_by ) . ' · ' . esc_html( $edit->verified_at ) . ' UTC</p>'; }
        submit_button( 'Save record' ); echo '</form><hr><h2>Browse records</h2>';
        $args = SRM_Shortcodes::filters();
        echo '<form method="get"><input type="hidden" name="page" value="srm-results"><p><label>Game <select name="gbk_game"><option value="0">All games</option>';
        foreach ( SRM_Games::get_all_games() as $g ) { echo '<option value="' . absint( $g->id ) . '" ' . selected( $args['game_id'] ?? 0, $g->id, false ) . '>' . esc_html( $g->event_name ) . '</option>'; }
        echo '</select></label> <label>Month / year <input name="gbk_month" type="month" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET['gbk_month'] ?? '' ) ) ) . '"></label> <label>Date <input name="gbk_date" type="date" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET['gbk_date'] ?? '' ) ) ) . '"></label> <label>Status <select name="status"><option value="">All</option>';
        foreach ( array( 'pending' => 'Draft', 'published' => 'Published' ) as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '" ' . selected( sanitize_key( $_GET['status'] ?? '' ), $key, false ) . '>' . esc_html( $label ) . '</option>'; }
        echo '</select></label> <button class="button">Filter</button> <a href="' . esc_url( admin_url( 'admin.php?page=srm-results' ) ) . '">Reset</a></p></form>';
        if ( isset( $args['error'] ) ) { echo '<p>' . esc_html( $args['error'] ) . '</p>'; return; }
        $args['status'] = sanitize_key( $_GET['status'] ?? '' );
        $data = SRM_Results::query( $args, true );
        echo '<p>' . absint( $data['total'] ) . ' records. Exact date overrides month. Draft and unverified records are never public.</p><div style="overflow-x:auto"><table class="widefat striped"><thead><tr><th>Date</th><th>Game</th><th>Value</th><th>Status</th><th>Verification</th><th>Source</th><th>Action</th></tr></thead><tbody>';
        foreach ( $data['rows'] as $r ) {
            echo '<tr><td>' . esc_html( $r->result_date ) . '</td><td>' . esc_html( $r->event_name ) . '</td><td>' . esc_html( $r->result_value ?? '' ) . '</td><td>' . esc_html( $r->result_status ) . '</td><td>' . esc_html( $r->verification_status ) . '</td><td>' . esc_html( wp_parse_url( $r->source_url ?? '', PHP_URL_HOST ) ?? 'No source' ) . '</td><td><a href="' . esc_url( add_query_arg( array( 'page' => 'srm-results', 'edit_game' => $r->event_id, 'edit_date' => $r->result_date ), admin_url( 'admin.php' ) ) ) . '">Review / edit</a></td></tr>';
        }
        echo '</tbody></table></div>';
        if ( $data['pages'] > 1 ) { echo '<p>' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'gbk_page', '%#%' ), 'format' => '', 'total' => $data['pages'], 'current' => $data['page'] ) ) ) . '</p>'; }
    }
    private static function csv(): void {
        echo '<h2>Import historical records</h2><p>UTF-8 CSV · maximum 2 MB / 5,000 rows. ISO dates YYYY-MM-DD only. Use an exact primary name, Hindi name or schedule slug. Unknown/ambiguous games are rejected. Existing game/date records are never overwritten. Imports always enter the review queue as drafts.</p><p>Required header: <code>' . esc_html( implode( ',', SRM_Import_Export::COLUMNS ) ) . '</code></p>';
        self::form( 'template' ); submit_button( 'Download empty CSV template', 'secondary' ); echo '</form>';
        self::form( 'import' ); echo '<p><label>CSV file <input type="file" name="csv" accept=".csv,text/csv" required></label></p>'; submit_button( 'Import as drafts' ); echo '</form>';
        echo '<h2>Export records</h2><p>Exports include drafts and source information. Spreadsheet programs may strip leading zeroes: import the result column as text. Spreadsheet formula prefixes are escaped.</p>';
        self::form( 'export' ); self::field( 'From', 'start', wp_date( 'Y-m-01' ), 'date', true ); self::field( 'Through', 'end', wp_date( 'Y-m-d' ), 'date', true ); submit_button( 'Export CSV', 'secondary' ); echo '</form><h2>Import log</h2><p>Counts are per file. Skipped = blank rows; duplicate = existing game/date; invalid = rejected row. Error details are capped at 100 per import. Times are UTC.</p>';
        global $wpdb;
        $table = $wpdb->prefix . 'srm_import_logs';
        $page = max( 1, absint( $_GET['log_page'] ?? 1 ) );
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 20 OFFSET %d", ( $page - 1 ) * 20 ) );
        foreach ( $rows as $row ) {
            $log = json_decode( $row->summary, true );
            echo '<details style="background:white;padding:12px;margin:12px 0"><summary>' . esc_html( $row->created_at . ' UTC · imported ' . ( $log['imported'] ?? 0 ) . ' · skipped ' . ( $log['skipped'] ?? 0 ) . ' · duplicate ' . ( $log['duplicate'] ?? 0 ) . ' · invalid ' . ( $log['invalid'] ?? 0 ) ) . '</summary><p>User ID ' . absint( $row->user_id ) . ' · SHA256 <code>' . esc_html( $row->file_hash ) . '</code></p><ul>';
            foreach ( $log['errors'] ?? array() as $error ) { echo '<li>' . esc_html( $error ) . '</li>'; }
            echo '</ul></details>';
        }
        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
        if ( $total > 20 ) { echo wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'log_page', '%#%' ), 'format' => '', 'total' => ceil( $total / 20 ), 'current' => $page ) ) ); }
    }
    private static function help(): void {
        echo '<h2>Configuration</h2><ul><li><a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">Site title, tagline, timezone and date/time formats → Settings / General</a></li><li><a href="' . esc_url( admin_url( 'customize.php' ) ) . '">Logo, favicon, colors, contact, SEO and homepage sections → Appearance / Customize</a></li><li>FAQs use the FAQ content type; informational articles use Posts. Create an Archives page containing <code>[gbk_archive]</code>, then assign it in the Customizer.</li></ul><h2>Safety & migration</h2><p>Back up the entire database and wp-content before updating. Existing game IDs, times, result values and publication statuses are retained. New provenance columns default to unverified, so legacy records require review before public display. No results are seeded. Uninstall retains all data. Deactivate games instead of deleting them.</p><p>Existing limited game operators are not granted access to this new review desk. Only administrators or explicitly trusted roles with manage_scheduled_results can manage/publish/import/export. No permission is inferred from frontend context.</p><h2>Shortcodes</h2><p><code>[srm_active_games]</code> · <code>[gbk_latest_records]</code> · <code>[srm_today_yesterday_results]</code> · <code>[srm_monthly_results month="10" year="2026"]</code> · <code>[srm_previous_month_results]</code> · <code>[gbk_archive]</code> · <code>[srm_game_history game_id="1"]</code></p><p>Numeric archive filters and schedule ordering are keyboard accessible. Calendar dates are never shifted to a previous “business day”. No predictions or financial functions are included.</p>';
    }
}
