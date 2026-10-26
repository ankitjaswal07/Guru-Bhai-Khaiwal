<?php
/** Run ONLY inside tools/runtime.mjs's disposable database, never on a live site. */
if ( ! defined( 'ABSPATH' ) || ! str_contains( ABSPATH, '/wordpress/' ) ) { exit; }
$passed = 0;
function gbk_assert( $condition, string $message ): void {
    global $passed;
    if ( ! $condition ) { throw new RuntimeException( 'FAIL: ' . $message ); }
    ++$passed; echo "PASS: {$message}\n";
}
wp_set_current_user( 1 );
gbk_assert( count( SRM_Games::get_all_games() ) === 14, 'clean activation creates exactly 14 schedules' );
gbk_assert( SRM_Results::query( array(), true )['total'] === 0, 'clean activation seeds no results' );
$expected = array( '12:40:00','13:25:00','14:25:00','14:50:00','15:40:00','16:25:00','17:20:00','17:55:00','19:25:00','20:15:00','21:20:00','22:40:00','23:25:00','01:00:00' );
gbk_assert( array_column( SRM_Games::get_all_games(), 'scheduled_time' ) === $expected, 'all 14 client times and display order match' );
$games = SRM_Games::get_all_games(); $id = (int) $games[0]->id;
gbk_assert( $games[0]->event_name_secondary === 'मॉडल टाउन', 'Hindi stored and retrieved without corruption' );
foreach ( array( '2026-10-26 23:50:00' => '2026-10-27 01:00:00', '2026-10-26 00:30:00' => '2026-10-26 01:00:00', '2026-10-26 01:00:00' => '2026-10-26 12:40:00' ) as $input => $expected_at ) {
    $next = SRM_Current_Event::occurrences( new DateTimeImmutable( $input, wp_timezone() ) )['next'];
    gbk_assert( $next['at']->format( 'Y-m-d H:i:s' ) === $expected_at, 'midnight boundary ' . $input );
}
gbk_assert( SRM_Games::sanitize_time( '12:00 AM' ) === '00:00:00' && SRM_Games::sanitize_time( '12:00 PM' ) === '12:00:00', '12-hour midnight/noon conversion' );
gbk_assert( false === SRM_Games::sanitize_time( '13:90 PM' ) && false === SRM_Games::sanitize_time( '24:01' ), 'invalid times rejected' );
$date = '2026-08-26';
$record = array( 'result_value' => '07', 'result_status' => 'pending', 'original_game_name' => 'TEST FIXTURE', 'source_url' => 'https://example.com/test-fixture', 'verification_status' => 'unverified' );
gbk_assert( true === SRM_Results::save_result( $id, $date, $record ), 'save draft record' );
gbk_assert( SRM_Results::get_result( $id, $date ) === null, 'draft hidden even from admin public getter' );
gbk_assert( SRM_Results::get_result( $id, $date, true )->result_value === '07', 'admin can review draft; leading zero preserved' );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, '2026-02-30', $record ) ), 'impossible date rejected' );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, '2099-01-01', $record ) ), 'future historical date rejected' );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, $date, array_merge( $record, array( 'source_url' => 'javascript:alert(1)' ) ) ) ), 'dangerous source scheme rejected' );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, $date, array_merge( $record, array( 'result_value' => '=1+1' ) ) ) ), 'formula value rejected' );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, $date, array_merge( $record, array( 'result_status' => 'published', 'verification_status' => 'verified' ) ) ) ), 'publication requires explicit review' );
$published = array_merge( $record, array( 'result_status' => 'published', 'verification_status' => 'verified', 'confirm_review' => 1 ) );
gbk_assert( true === SRM_Results::save_result( $id, $date, $published ), 'reviewed record can be published' );
gbk_assert( SRM_Results::get_result( $id, $date )->result_value === '07', 'published source-verified record publicly readable' );
gbk_assert( isset( SRM_Results::get_monthly_results( 2026, 8 )[ $date ][ $id ] ), 'monthly repository includes approved record' );
$duplicate = SRM_Results::save_result( $id, $date, array_merge( $record, array( 'result_value' => '99' ) ), true );
gbk_assert( is_wp_error( $duplicate ) && 'duplicate' === $duplicate->get_error_code() && SRM_Results::get_result( $id, $date )->result_value === '07', 'duplicate import cannot overwrite approved value' );
$old_hash = SRM_Results::record_hash( SRM_Results::get_result( $id, $date, true ) );
SRM_Results::save_result( $id, $date, array_merge( $published, array( 'result_value' => '08' ) ) );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, $date, array_merge( $published, array( 'expected_hash' => $old_hash ) ) ) ), 'stale form conflict detected' );
SRM_Results::save_result( $id, $date, $record );
gbk_assert( ! SRM_Results::get_monthly_results( 2026, 8 ), 'unpublishing immediately removes record without stale cache' );
$path = '/tmp/gbk-test-import.csv'; $fh = fopen( $path, 'w' ); fputcsv( $fh, SRM_Import_Export::COLUMNS, ',', '"', '' );
foreach ( array(
    array( '2026-08-27', 'model-town', '00', 'published', 'TEST FIXTURE', 'https://example.com/test', 'verified', 'Test only' ),
    array( '2026-08-27', 'model-town', '99', 'pending', 'TEST FIXTURE', 'https://example.com/test', 'verified', '' ),
    array( '2026-02-30', 'model-town', '09', 'draft', 'TEST FIXTURE', 'https://example.com/test', 'unverified', '' ),
    array( '2026-08-28', 'UNKNOWN', '03', 'pending', 'TEST FIXTURE', 'https://example.com/test', 'verified', '' ),
    array( '2026-08-28', 'मॉडल टाउन', '05', 'draft', 'TEST FIXTURE', 'https://example.com/test', 'verified', 'Hindi resolution' ),
) as $row ) { fputcsv( $fh, $row, ',', '"', '' ); }
fwrite( $fh, "\n" ); fclose( $fh );
$summary = SRM_Import_Export::import_csv( $path );
gbk_assert( $summary['imported'] === 2 && $summary['duplicate'] === 1 && $summary['invalid'] === 2 && $summary['skipped'] === 1, 'CSV success/duplicate/invalid/blank counts correct' );
gbk_assert( SRM_Results::get_result( $id, '2026-08-27', true )->result_status === 'pending', 'CSV cannot publish even when status says published' );
gbk_assert( SRM_Results::get_result( $id, '2026-08-27' ) === null, 'imported verified assertion remains private until editor approval' );
$again = SRM_Import_Export::import_csv( $path );
gbk_assert( 0 === $again['imported'] && 3 === $again['duplicate'], 'repeated import idempotent' );
gbk_assert( SRM_Import_Export::safe_cell( '=HYPERLINK("x")' )[0] === "'" && SRM_Import_Export::safe_cell( '00' ) === '00', 'export neutralizes formulas and preserves zeroes' );
$fh = fopen( '/tmp/gbk-test-bad.csv', 'w' ); fwrite( $fh, "date,game\n2026-08-26,model-town\n" ); fclose( $fh );
gbk_assert( ! SRM_Import_Export::import_csv( '/tmp/gbk-test-bad.csv' )['success'], 'legacy CSV without provenance rejected with instructions' );
$restricted = wp_insert_user( array( 'user_login' => 'test-limited', 'user_pass' => wp_generate_password(), 'role' => 'subscriber' ) );
$user = get_user_by( 'id', $restricted ); $user->add_cap( 'srm_manage_game_results' ); update_user_meta( $restricted, 'srm_allowed_games', array( $id ) );
wp_set_current_user( $restricted );
gbk_assert( is_wp_error( SRM_Results::save_result( $id, $date, $published ) ), 'legacy restricted operator cannot bypass review capability' );
gbk_assert( is_wp_error( SRM_Games::create_game( array( 'event_name' => 'Unauthorized', 'scheduled_time' => '12:00' ) ) ), 'unauthorized schedule mutation rejected in repository' );
gbk_assert( ! SRM_Import_Export::import_csv( $path )['success'], 'unauthorized import rejected' );
gbk_assert( SRM_Results::get_result( $id, $date, true ) === null, 'admin flag cannot expose draft to restricted user' );
wp_set_current_user( 1 );
$occurrence = SRM_Current_Event::occurrences()['previous'];
SRM_Results::save_result( (int) $occurrence['game']->id, $occurrence['at']->format( 'Y-m-d' ), array_merge( $record, array( 'result_value' => '918273645' ) ) );
wp_set_current_user( 0 );
$privacy_response = rest_do_request( new WP_REST_Request( 'GET', '/srm/v1/current' ) );
gbk_assert( ! str_contains( wp_json_encode( $privacy_response->get_data() ), '918273645' ), 'anonymous current API hides actual current-game draft' );
$privacy_response = rest_do_request( new WP_REST_Request( 'GET', '/srm/v1/today' ) );
gbk_assert( ! str_contains( wp_json_encode( $privacy_response->get_data() ), '918273645' ), 'anonymous today API hides actual current-game draft' );
foreach ( array( 'srm_today_results', 'srm_yesterday_results', 'srm_today_yesterday_results', 'srm_monthly_results', 'srm_previous_month_results', 'srm_active_games', 'srm_next_game', 'srm_live_status', 'gbk_archive', 'gbk_latest_records' ) as $tag ) {
    gbk_assert( shortcode_exists( $tag ) && is_string( do_shortcode( '[' . $tag . ']' ) ) && ! str_contains( do_shortcode( '[' . $tag . ']' ), '918273645' ), 'renders shortcode ' . $tag );
}
$request = new WP_REST_Request( 'GET', '/srm/v1/monthly' ); $request->set_param( 'year', 2026 ); $request->set_param( 'month', 8 );
$response = rest_do_request( $request );
gbk_assert( 200 === $response->get_status() && ! $response->get_data()['results'], 'anonymous monthly API never returns pending values' );
$request->set_param( 'month', 13 ); gbk_assert( 400 === rest_do_request( $request )->get_status(), 'REST rejects out-of-range month' );
$_GET['gbk_date'] = '2026-02-30'; gbk_assert( isset( SRM_Shortcodes::filters()['error'] ), 'archive does not widen invalid date to all records' ); unset( $_GET['gbk_date'] );
wp_set_current_user( 1 );
$before = SRM_Results::get_result( $id, $date, true ); SRM_Database::install(); $after = SRM_Results::get_result( $id, $date, true );
gbk_assert( $before == $after && count( SRM_Games::get_all_games() ) === 14, 'repeat activation preserves IDs, schedules and complete row content' );
// Additive migration from the actual old schema in isolated test-prefixed tables.
global $wpdb;
$original_prefix = $wpdb->prefix; $wpdb->prefix = $original_prefix . 'upgrade_test_';
$e = SRM_Database::events_table(); $r = SRM_Database::results_table();
$wpdb->query( "CREATE TABLE {$e} (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, event_name VARCHAR(191) NOT NULL, event_name_secondary VARCHAR(191) NULL DEFAULT NULL, slug VARCHAR(191) NOT NULL, scheduled_time TIME NOT NULL, display_order INT NOT NULL DEFAULT 0, status TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id), UNIQUE KEY slug(slug))" );
$wpdb->query( "CREATE TABLE {$r} (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, event_id BIGINT UNSIGNED NOT NULL, result_date DATE NOT NULL, result_value VARCHAR(50) NULL DEFAULT NULL, result_status VARCHAR(30) NOT NULL DEFAULT 'pending', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id), UNIQUE KEY event_date(event_id,result_date))" );
$wpdb->insert( $e, array( 'id' => 99, 'event_name' => 'Legacy test', 'slug' => 'legacy-test', 'scheduled_time' => '02:00:00', 'created_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ) ) );
$wpdb->insert( $r, array( 'id' => 88, 'event_id' => 99, 'result_date' => $date, 'result_value' => '01', 'result_status' => 'published', 'created_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ) ) );
SRM_Database::install();
$legacy = SRM_Results::get_result( 99, $date, true );
gbk_assert( $legacy && (int) $legacy->id === 88 && $legacy->result_value === '01' && $legacy->result_status === 'published' && $legacy->verification_status === 'unverified', '1.1.0 schema migration preserves legacy values/status/IDs and adds unverified provenance' );
gbk_assert( SRM_Results::get_result( 99, $date ) === null && count( SRM_Games::get_all_games() ) === 1, 'legacy unverified published data hidden; existing schedule not reseeded' );
foreach ( array( $r, $e, $wpdb->prefix . 'srm_import_logs' ) as $table ) { $wpdb->query( "DROP TABLE {$table}" ); }
$wpdb->prefix = $original_prefix;
// Real delivery CSV validates end-to-end without publishing any row.
if ( file_exists( '/historical-data-import.csv' ) ) {
    $wpdb->query( 'DELETE FROM ' . SRM_Database::results_table() );
    $import = SRM_Import_Export::import_csv( '/historical-data-import.csv' );
    gbk_assert( $import['imported'] === 523 && $import['invalid'] === 0 && $import['duplicate'] === 0, 'all 523 source-checked delivery rows import successfully' );
    gbk_assert( SRM_Results::query()['total'] === 0, 'delivery CSV does not automatically publish data' );
    $repeat = SRM_Import_Export::import_csv( '/historical-data-import.csv' );
    gbk_assert( $repeat['duplicate'] === 523 && $repeat['imported'] === 0, 'delivery CSV repeated import skips all 523 duplicates' );
    $paginated = SRM_Results::query( array( 'limit' => 30, 'page' => 2 ), true );
    gbk_assert( count( $paginated['rows'] ) === 30 && $paginated['pages'] === 18 && $paginated['total'] === 523, 'large archive pagination correct' );
    $monthly = SRM_Results::query( array( 'start' => '2026-09-01', 'end' => '2026-09-30' ), true );
    gbk_assert( $monthly['total'] === 203, 'month filter returns exactly 203 September rows' );
}
// Remove test-only records/logs. Preview returns to a genuine empty archive.
$wpdb->query( 'DELETE FROM ' . SRM_Database::results_table() ); $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'srm_import_logs' );
require_once ABSPATH . 'wp-admin/includes/user.php'; wp_delete_user( $restricted );
echo "\n{$passed} assertions passed. Fixture records removed.\n";
