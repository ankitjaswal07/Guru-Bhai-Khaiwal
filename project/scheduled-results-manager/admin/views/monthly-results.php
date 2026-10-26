<?php
/**
 * Admin Monthly Results View
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! SRM_Access::current_user_can_access_plugin() ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'scheduled-results-manager' ) );
}

$tz  = wp_timezone();
$now = new DateTime( 'now', $tz );

$month = isset( $_GET['month'] ) ? (int) $_GET['month'] : (int) $now->format( 'n' );
$year  = isset( $_GET['year'] )  ? (int) $_GET['year']  : (int) $now->format( 'Y' );

// Clamp
$month = max( 1, min( 12, $month ) );
$year  = max( 2000, min( 2100, $year ) );

$events      = SRM_Games::get_active_games();
$data        = SRM_Results::get_admin_month( $year, $month );
$days        = (int) date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
$date_format = get_option( 'srm_date_format', 'd/m/Y' );
$month_label = date( 'F Y', mktime( 0, 0, 0, $month, 1, $year ) );

// Navigation: prev/next
$prev_month  = $month - 1;
$prev_year   = $year;
if ( $prev_month < 1 ) { $prev_month = 12; $prev_year--; }
$next_month  = $month + 1;
$next_year   = $year;
if ( $next_month > 12 ) { $next_month = 1; $next_year++; }
?>
<div class="wrap srm-admin-wrap">
    <h1 class="srm-page-title">
        <span class="dashicons dashicons-calendar"></span>
        <?php esc_html_e( 'Monthly Results', 'scheduled-results-manager' ); ?>
    </h1>

    <!-- Month Selector -->
    <div class="srm-month-selector">
        <form method="get" action="">
            <input type="hidden" name="page" value="srm-monthly">
            <label for="month"><strong><?php esc_html_e( 'Month:', 'scheduled-results-manager' ); ?></strong></label>
            <select id="month" name="month">
                <?php for ( $m = 1; $m <= 12; $m++ ) : ?>
                    <option value="<?php echo esc_attr( (string) $m ); ?>" <?php selected( $m, $month ); ?>>
                        <?php echo esc_html( date( 'F', mktime( 0, 0, 0, $m, 1 ) ) ); ?>
                    </option>
                <?php endfor; ?>
            </select>
            <label for="year"><strong><?php esc_html_e( 'Year:', 'scheduled-results-manager' ); ?></strong></label>
            <input type="number" id="year" name="year" value="<?php echo esc_attr( (string) $year ); ?>" min="2000" max="2100" style="width:80px;">
            <button type="submit" class="button button-primary"><?php esc_html_e( 'View', 'scheduled-results-manager' ); ?></button>
        </form>

        <!-- Navigation -->
        <div class="srm-month-nav">
            <a href="<?php echo esc_url( admin_url( "admin.php?page=srm-monthly&month={$prev_month}&year={$prev_year}" ) ); ?>" class="button button-secondary">
                &laquo; <?php echo esc_html( date( 'M Y', mktime( 0, 0, 0, $prev_month, 1, $prev_year ) ) ); ?>
            </a>
            <span class="srm-month-label"><?php echo esc_html( $month_label ); ?></span>
            <a href="<?php echo esc_url( admin_url( "admin.php?page=srm-monthly&month={$next_month}&year={$next_year}" ) ); ?>" class="button button-secondary">
                <?php echo esc_html( date( 'M Y', mktime( 0, 0, 0, $next_month, 1, $next_year ) ) ); ?> &raquo;
            </a>
        </div>
    </div>

    <!-- Print / Export Controls -->
    <div class="srm-monthly-controls">
        <button type="button" onclick="window.print();" class="button button-secondary">
            <span class="dashicons dashicons-printer"></span>
            <?php esc_html_e( 'Print', 'scheduled-results-manager' ); ?>
        </button>
        <form method="post" action="" style="display:inline;">
            <?php wp_nonce_field( 'srm_export_csv' ); ?>
            <input type="hidden" name="srm_action" value="export_csv">
            <input type="hidden" name="export_start" value="<?php echo esc_attr( sprintf( '%04d-%02d-01', $year, $month ) ); ?>">
            <input type="hidden" name="export_end" value="<?php echo esc_attr( date( 'Y-m-t', mktime( 0, 0, 0, $month, 1, $year ) ) ); ?>">
            <button type="submit" class="button button-secondary">
                <span class="dashicons dashicons-download"></span>
                <?php esc_html_e( 'Export CSV', 'scheduled-results-manager' ); ?>
            </button>
        </form>
        <a href="<?php echo esc_url( add_query_arg( array( 'shortcode' => '[srm_monthly_results month="' . $month . '" year="' . $year . '"]' ) ) ); ?>" class="srm-shortcode-hint">
            Shortcode: <code>[srm_monthly_results month="<?php echo esc_html( (string) $month ); ?>" year="<?php echo esc_html( (string) $year ); ?>"]</code>
        </a>
    </div>

    <?php if ( empty( $events ) ) : ?>
        <p><?php esc_html_e( 'No active games found.', 'scheduled-results-manager' ); ?></p>
    <?php else : ?>

    <!-- Monthly Results Matrix -->
    <div class="srm-monthly-table-wrap">
        <table class="wp-list-table widefat srm-monthly-table" id="srm-monthly-print-table">
            <thead>
                <tr>
                    <th class="srm-date-col"><?php esc_html_e( 'Date', 'scheduled-results-manager' ); ?></th>
                    <?php foreach ( $events as $event ) : ?>
                        <th>
                            <?php echo esc_html( $event->event_name ); ?>
                            <small><?php echo esc_html( SRM_Games::format_time( $event->scheduled_time ) ); ?></small>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ( $d = 1; $d <= $days; $d++ ) :
                    $date    = sprintf( '%04d-%02d-%02d', $year, $month, $d );
                    $display = date( $date_format, strtotime( $date ) );
                    $is_today = ( $date === SRM_Results::get_site_date() );
                ?>
                <tr class="<?php echo $is_today ? 'srm-today-row' : ''; ?>">
                    <td class="srm-date-cell">
                        <?php echo esc_html( $display ); ?>
                        <?php if ( $is_today ) : ?>
                            <span class="srm-today-badge"><?php esc_html_e( 'Today', 'scheduled-results-manager' ); ?></span>
                        <?php endif; ?>
                    </td>
                    <?php foreach ( $events as $event ) :
                        $result = $data[ $date ][ $event->id ] ?? null;
                    ?>
                        <td class="srm-result-cell">
                            <?php if ( $result && $result->result_value !== null ) : ?>
                                <span class="srm-result-value"><?php echo esc_html( $result->result_value ); ?></span><small><?php echo esc_html($result->result_status.' / '.$result->verification_status); ?></small><a href="<?php echo esc_url(admin_url('admin.php?page=srm-results&date='.$date)); ?>">Review day</a>
                            <?php elseif ( $result && $result->result_status === 'pending' ) : ?>
                                <span class="srm-pending-indicator">—</span>
                            <?php else : ?>
                                <span class="srm-empty-indicator">—</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
