<?php
/** Shared record repository. Public reads always apply the publication gate. */
defined( 'ABSPATH' ) || exit;
class SRM_Results {
    public static function valid_date( string $date ): bool {
        return (bool) preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/D', $date, $m ) && checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) && (int) $m[1] >= 1900 && (int) $m[1] <= 2100;
    }
    public static function valid_source( string $url ): bool {
        $parts = wp_parse_url( $url );
        return is_array( $parts ) && in_array( strtolower( $parts['scheme'] ?? '' ), array( 'http', 'https' ), true ) && ! empty( $parts['host'] ) && empty( $parts['user'] ) && empty( $parts['pass'] ) && strlen( $url ) <= 2000;
    }
    public static function public_clause(): string {
        return "r.result_status = 'published' AND r.verification_status = 'verified' AND r.source_url <> '' AND r.reviewed_by > 0 AND r.verified_at IS NOT NULL AND r.result_value <> ''";
    }
    public static function get_site_date(): string { return wp_date( 'Y-m-d' ); }
    public static function sanitize_result_value( string $value ): string { return trim( sanitize_text_field( $value ) ); }
    public static function get_result( int $event_id, string $date, bool $admin = false ): ?object {
        global $wpdb;
        if ( ! self::valid_date( $date ) ) { return null; }
        $table = SRM_Database::results_table();
        $events = SRM_Database::events_table();
        $private = $admin && SRM_Access::can_edit_game($event_id);
        $gate = $private ? '1=1' : self::public_clause() . $wpdb->prepare( ' AND e.status = 1 AND r.result_date <= %s', self::get_site_date() );
        $join = $private ? '' : " JOIN {$events} e ON e.id = r.event_id";
        return $wpdb->get_row( $wpdb->prepare( "SELECT r.* FROM {$table} r {$join} WHERE r.event_id = %d AND r.result_date = %s AND {$gate}", $event_id, $date ) );
    }
    public static function query( array $args = array(), bool $admin = false ): array {
        global $wpdb;
        $r = SRM_Database::results_table();
        $e = SRM_Database::events_table();
        $private = $admin && SRM_Access::current_user_can_access_plugin();
        $where = $private ? '1=1' : 'e.status = 1 AND ' . self::public_clause();
        $values = array();
        if($private && !SRM_Access::current_user_has_full_access()){
            $ids=SRM_Access::get_user_allowed_game_ids();
            $where.=$ids?' AND r.event_id IN ('.implode(',',array_map('absint',$ids)).')':' AND 1=0';
        }
        foreach ( array( 'start' => '>=', 'end' => '<=' ) as $key => $op ) {
            if ( ! empty( $args[ $key ] ) && self::valid_date( $args[ $key ] ) ) {
                $where .= " AND r.result_date {$op} %s";
                $values[] = $args[ $key ];
            }
        }
        if ( ! empty( $args['game_id'] ) ) { $where .= ' AND r.event_id = %d'; $values[] = absint( $args['game_id'] ); }
        if ( $private && in_array( $args['status'] ?? '', array( 'pending', 'published' ), true ) ) {
            $where .= ' AND r.result_status = %s'; $values[] = $args['status'];
        }
        if ( ! $private ) { $where .= ' AND r.result_date <= %s'; $values[] = self::get_site_date(); }
        $base = "FROM {$r} r JOIN {$e} e ON e.id = r.event_id WHERE {$where}";
        $count_sql = "SELECT COUNT(*) {$base}";
        $total = (int) $wpdb->get_var( $values ? $wpdb->prepare( $count_sql, $values ) : $count_sql );
        $limit = max( 1, min( 1000, absint( $args['limit'] ?? 30 ) ) );
        $page = max( 1, min( 1000000, absint( $args['page'] ?? 1 ) ) );
        $sql = "SELECT r.*, e.event_name, e.event_name_secondary, e.scheduled_time, e.slug {$base} ORDER BY r.result_date DESC, e.display_order ASC, r.id DESC LIMIT %d OFFSET %d";
        $rows = $wpdb->get_results( $wpdb->prepare( $sql, array_merge( $values, array( $limit, ( $page - 1 ) * $limit ) ) ) ) ?: array();
        return array( 'rows' => $rows, 'total' => $total, 'pages' => (int) ceil( $total / $limit ), 'page' => $page );
    }
    public static function get_results_for_date( string $date ): array {
        if ( ! self::valid_date( $date ) ) { return array(); }
        return self::query( array( 'start' => $date, 'end' => $date, 'limit' => 1000 ) )['rows'];
    }
    public static function get_today_results(): array { return self::get_results_for_date( self::get_site_date() ); }
    public static function get_yesterday_results(): array { return self::get_results_for_date( current_datetime()->modify( '-1 day' )->format( 'Y-m-d' ) ); }
    public static function get_monthly_results( int $year, int $month ): array {
        if ( $year < 1900 || $year > 2100 || $month < 1 || $month > 12 ) { return array(); }
        $start = sprintf( '%04d-%02d-01', $year, $month );
        $end = ( new DateTimeImmutable( $start, wp_timezone() ) )->format( 'Y-m-t' );
        $indexed = array();
        // Page through bounded batches; supports installations with more than 32 games.
        $page = 1;
        do {
            $data = self::query( array( 'start' => $start, 'end' => $end, 'limit' => 1000, 'page' => $page ) );
            foreach ( $data['rows'] as $row ) { $indexed[ $row->result_date ][ $row->event_id ] = $row; }
            ++$page;
        } while ( $page <= $data['pages'] );
        return $indexed;
    }
    public static function save_result( int $event_id, string $date, array $data, bool $insert_only = false ): bool|WP_Error {
        global $wpdb;
        foreach(array('result_value','original_game_name','source_url','verification_notes','expected_hash') as $field){if(isset($data[$field])&&!is_scalar($data[$field]))return new WP_Error('input','Record fields must be scalar text.');}
        if ( ! SRM_Access::can_edit_game($event_id) ) { return new WP_Error( 'forbidden', 'Permission denied.' ); }
        if ( ! SRM_Games::get_game( $event_id ) ) { return new WP_Error( 'game', 'Choose an existing game.' ); }
        if ( ! self::valid_date( $date ) || $date > self::get_site_date() ) { return new WP_Error( 'date', 'Use a real ISO date, not a future date.' ); }
        $value = self::sanitize_result_value( (string) ( $data['result_value'] ?? '' ) );
        if ( strlen( $value ) > 50 || ! preg_match( 'numeric' === get_option('srm_result_format','numeric') ? '/^[0-9]+(?:[- \/][0-9]+)*$/D' : '/^[\pL\pN][\pL\pN .\/_-]*$/uD', $value ) ) { return new WP_Error( 'value', 'Enter a numeric historical value (up to 50 characters); preserve leading zeroes.' ); }
        $status = $data['result_status'] ?? 'pending';
        $verification = $data['verification_status'] ?? 'unverified';
        if(!SRM_Access::current_user_has_full_access() && ('published'===$status || 'verified'===$verification || !empty($data['confirm_review'])))return new WP_Error('review','Only full results managers may review or publish.');
        if ( ! in_array( $status, array( 'pending', 'published' ), true ) || ! in_array( $verification, array( 'unverified', 'verified', 'disputed' ), true ) ) { return new WP_Error( 'status', 'Invalid status.' ); }
        $source = esc_url_raw( (string) ( $data['source_url'] ?? '' ), array( 'https', 'http' ) );
        if ( ! self::valid_source( $source ) ) { return new WP_Error( 'source', 'A valid public HTTP(S) source URL is required.' ); }
        $original = sanitize_text_field( (string) ( $data['original_game_name'] ?? '' ) );
        if ( ! $original || mb_strlen( $original ) > 191 ) { return new WP_Error( 'original', 'Enter the original source game name (up to 191 characters).' ); }
        $reviewed = ! $insert_only && ! empty( $data['confirm_review'] ) && 'verified' === $verification;
        if ( 'published' === $status && ! $reviewed ) { return new WP_Error( 'review', 'Verify the source and explicitly confirm your review before publishing.' ); }
        $existing = self::get_result( $event_id, $date, true );
        if($existing && !$insert_only && !get_option('srm_allow_editing','1'))return new WP_Error('locked','Editing is disabled in Results Manager settings.');
        if($existing && 'published'===$existing->result_status && !SRM_Access::current_user_has_full_access())return new WP_Error('review','Only full results managers may change published records.');
        if ( $insert_only && $existing ) { return new WP_Error( 'duplicate', 'Existing game/date retained; no overwrite.' ); }
        if ( $existing && isset( $data['expected_hash'] ) && ! hash_equals( self::record_hash( $existing ), $data['expected_hash'] ) ) { return new WP_Error( 'conflict', 'This record changed. Reload before editing.' ); }
        $row = array(
            'event_id' => $event_id, 'result_date' => $date, 'result_value' => $value,
            'result_status' => $insert_only ? 'pending' : $status, 'source_url' => $source,
            'original_game_name' => $original, 'verification_status' => $verification,
            'verification_notes' => mb_substr( sanitize_textarea_field( (string) ( $data['verification_notes'] ?? '' ) ), 0, 4000 ),
            'reviewed_by' => $reviewed ? get_current_user_id() : 0,
            'verified_at' => $reviewed ? current_time( 'mysql', true ) : null,
            'updated_at' => current_time( 'mysql', true ),
        );
        $table = SRM_Database::results_table();
        if ( $existing ) {
            $saved = $wpdb->update( $table, $row, array( 'id' => $existing->id ) );
        } else {
            $row['created_at'] = current_time( 'mysql', true );
            $saved = $wpdb->insert( $table, $row );
        }
        if ( false === $saved ) {
            return new WP_Error( self::get_result( $event_id, $date, true ) && $insert_only ? 'duplicate' : 'database', 'Save failed; reload and check the database log.' );
        }
        return true;
    }
    public static function get_admin_month(int $year,int $month): array {
        if(!SRM_Access::current_user_can_access_plugin()||$month<1||$month>12||$year<1900||$year>2100)return array();
        $start=sprintf('%04d-%02d-01',$year,$month);$out=array();$page=1;
        do{$data=self::query(array('start'=>$start,'end'=>(new DateTimeImmutable($start))->format('Y-m-t'),'limit'=>1000,'page'=>$page),true);foreach($data['rows'] as $r){$out[$r->result_date][$r->event_id]=$r;}$page++;}while($page<=$data['pages']);return $out;
    }
    public static function get_stats(): array {
        $today=array('start'=>self::get_site_date(),'end'=>self::get_site_date());
        return array('total_events'=>count(SRM_Games::get_all_games()),'active_events'=>count(SRM_Games::get_active_games()),'today_results'=>self::query($today,true)['total'],'pending'=>self::query($today+array('status'=>'pending'),true)['total'],'published'=>self::query($today+array('status'=>'published'),true)['total']);
    }
    public static function record_hash( object $row ): string { return hash( 'sha256', wp_json_encode( $row ) ); }
}
