<?php
/** Bounded, non-destructive CSV exchange; imported rows always await review. */
defined( 'ABSPATH' ) || exit;
class SRM_Import_Export {
    const COLUMNS = array( 'date', 'game', 'result', 'status', 'original_game_name', 'source_url', 'verification_status', 'verification_notes' );
    public static function normalize( string $name ): string {
        return mb_strtolower( preg_replace( '/\s+/u', ' ', trim( $name ) ) );
    }
    public static function import_csv( string $path ): array {
        $summary = array( 'success' => false, 'total' => 0, 'imported' => 0, 'skipped' => 0, 'duplicate' => 0, 'invalid' => 0, 'errors' => array() );
        if ( ! SRM_Access::current_user_has_full_access() ) { $summary['errors'][] = 'Permission denied.'; return $summary; }
        if ( ! is_readable( $path ) || filesize( $path ) > 2 * 1024 * 1024 ) { $summary['errors'][] = 'Use a readable UTF-8 CSV no larger than 2 MB.'; return $summary; }
        $handle = fopen( $path, 'r' );
        if ( ! $handle ) { return $summary; }
        $header = fgetcsv( $handle, 0, ',', '"', '' );
        if ( is_array( $header ) ) {
            $header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $header[0] );
            $header = array_map( static fn( $v ) => strtolower( trim( (string) $v ) ), $header );
            // Original CSV event alias remains accepted; provenance is now mandatory.
            $header = array_map( static fn( $v ) => 'event' === $v ? 'game' : $v, $header );
        }
        if ( ! is_array( $header ) || count( array_unique( $header ) ) !== count( $header ) || array_diff( self::COLUMNS, $header ) ) {
            fclose( $handle );
            $summary['errors'][] = 'Missing or duplicate columns. Download the current template.';
            self::log( $path, $summary ); return $summary;
        }
        // Preflight row count: oversized files are rejected before any write.
        $count = 0;
        while ( false !== fgetcsv( $handle, 0, ',', '"', '' ) ) { ++$count; }
        if ( $count > 5000 ) { fclose( $handle ); $summary['errors'][] = 'Maximum 5,000 rows per file; split your file.'; self::log( $path, $summary ); return $summary; }
        rewind( $handle ); fgetcsv( $handle, 0, ',', '"', '' );
        $map = array();
        foreach ( SRM_Games::get_all_games() as $game ) {
            foreach ( array( $game->slug, $game->event_name, $game->event_name_secondary ) as $name ) {
                if ( ! $name ) { continue; }
                $key = self::normalize( $name );
                $map[ $key ][] = (int) $game->id;
            }
        }
        while ( false !== ( $row = fgetcsv( $handle, 0, ',', '"', '' ) ) ) {
            ++$summary['total']; $line = $summary['total'] + 1;
            if ( array( null ) === $row || ! array_filter( $row, static fn( $v ) => '' !== trim( (string) $v ) ) ) { ++$summary['skipped']; continue; }
            $error = null;
            if ( count( $row ) !== count( $header ) || ! mb_check_encoding( implode( '', $row ), 'UTF-8' ) ) {
                $error = new WP_Error( 'invalid', 'Wrong column count or invalid UTF-8.' );
            } else {
                $data = array_combine( $header, array_map( 'trim', $row ) );
                $ids = array_unique( $map[ self::normalize( $data['game'] ) ] ?? array() );
                if ( count( $ids ) !== 1 ) { $error = new WP_Error( 'game', 'Unknown or ambiguous game. Use the stored slug.' ); }
                elseif ( ! in_array( $data['status'], array( 'draft', 'pending', 'published' ), true ) ) { $error = new WP_Error( 'status', 'Invalid publication status.' ); }
                else {
                    $error = SRM_Results::save_result( (int) reset( $ids ), $data['date'], array(
                        'result_value' => $data['result'], 'result_status' => 'pending',
                        'original_game_name' => $data['original_game_name'], 'source_url' => $data['source_url'],
                        'verification_status' => $data['verification_status'], 'verification_notes' => $data['verification_notes'],
                    ), true );
                }
            }
            if ( is_wp_error( $error ) ) {
                ++$summary[ 'duplicate' === $error->get_error_code() ? 'duplicate' : 'invalid' ];
                if ( count( $summary['errors'] ) < 100 ) { $summary['errors'][] = 'Row ' . $line . ': ' . $error->get_error_message(); }
            } else { ++$summary['imported']; }
        }
        fclose( $handle );
        $summary['success'] = true;
        self::log( $path, $summary );
        return $summary;
    }
    private static function log( string $path, array $summary ): void {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'srm_import_logs', array( 'user_id' => get_current_user_id(), 'created_at' => current_time( 'mysql', true ), 'file_hash' => hash_file( 'sha256', $path ), 'summary' => wp_json_encode( $summary ) ) );
    }
    public static function safe_cell( string $value ): string {
        return preg_match( '/^[\s]*[=+@\-\t\r\n]/u', $value ) ? "'" . $value : $value;
    }
    public static function export_csv( ?string $start = null, ?string $end = null, bool $template = false ): void {
        if ( ! SRM_Access::current_user_has_full_access() ) { wp_die( 'Permission denied.', '', array( 'response' => 403 ) ); }
        if ( ! $template && ( ! $start || ! $end || ! SRM_Results::valid_date( $start ) || ! SRM_Results::valid_date( $end ) || $start > $end ) ) { wp_die( 'Choose valid ISO start and end dates.' ); }
        nocache_headers();
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="guru-bhai-historical-records.csv"' );
        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, self::COLUMNS, ',', '"', '' );
        if ( ! $template ) {
            $page = 1;
            do {
                $data = SRM_Results::query( array( 'start' => $start, 'end' => $end, 'page' => $page, 'limit' => 500 ), true );
                foreach ( $data['rows'] as $r ) {
                    $row = array( $r->result_date, $r->slug, $r->result_value ?? '', $r->result_status, $r->original_game_name, $r->source_url ?? '', $r->verification_status, $r->verification_notes ?? '' );
                    fputcsv( $out, array_map( array( __CLASS__, 'safe_cell' ), $row ), ',', '"', '' );
                }
                ++$page;
            } while ( $page <= $data['pages'] );
        }
        fclose( $out ); exit;
    }
}
