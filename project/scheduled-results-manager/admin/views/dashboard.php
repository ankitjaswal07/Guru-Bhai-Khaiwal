<?php
/**
 * Admin Dashboard View
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! SRM_Access::current_user_can_access_plugin() ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'scheduled-results-manager' ) );
}

$stats         = SRM_Results::get_stats();
$current_game = SRM_Current_Event::get_current_game();
$next_game    = SRM_Current_Event::get_next_game();
$is_tomorrow   = SRM_Current_Event::is_next_game_tomorrow();
$next_ts       = SRM_Current_Event::get_next_game_timestamp();

$current_result = null;
$current_status = 'none';

if ( $current_game ) {
    $occurrence=SRM_Current_Event::occurrences()['previous'];
    $current_result=SRM_Results::get_result((int)$current_game->id,$occurrence['at']->format('Y-m-d'),true);
    $current_status=$current_result?$current_result->result_status:'pending';
}
?>
<div class="wrap srm-admin-wrap">
    <h1 class="srm-page-title">
        <span class="dashicons dashicons-calendar-alt"></span>
        <?php esc_html_e( 'Scheduled Results — Dashboard', 'scheduled-results-manager' ); ?>
    </h1>

    <!-- Stats Row -->
    <div class="srm-stats-grid">
        <div class="srm-stat-card">
            <span class="srm-stat-number"><?php echo esc_html( (string) $stats['total_events'] ); ?></span>
            <span class="srm-stat-label"><?php esc_html_e( 'Total Games', 'scheduled-results-manager' ); ?></span>
        </div>
        <div class="srm-stat-card">
            <span class="srm-stat-number srm-green"><?php echo esc_html( (string) $stats['active_events'] ); ?></span>
            <span class="srm-stat-label"><?php esc_html_e( 'Active Games', 'scheduled-results-manager' ); ?></span>
        </div>
        <div class="srm-stat-card">
            <span class="srm-stat-number"><?php echo esc_html( (string) $stats['today_results'] ); ?></span>
            <span class="srm-stat-label"><?php esc_html_e( "Today's Results", 'scheduled-results-manager' ); ?></span>
        </div>
        <div class="srm-stat-card">
            <span class="srm-stat-number srm-amber"><?php echo esc_html( (string) $stats['pending'] ); ?></span>
            <span class="srm-stat-label"><?php esc_html_e( 'Pending', 'scheduled-results-manager' ); ?></span>
        </div>
        <div class="srm-stat-card">
            <span class="srm-stat-number srm-green"><?php echo esc_html( (string) $stats['published'] ); ?></span>
            <span class="srm-stat-label"><?php esc_html_e( 'Published', 'scheduled-results-manager' ); ?></span>
        </div>
    </div>

    <!-- Current / Next Game Cards -->
    <div class="srm-event-cards">

        <!-- Current Game -->
        <div class="srm-event-card">
            <h2><?php esc_html_e( 'Current Game', 'scheduled-results-manager' ); ?></h2>
            <?php if ( $current_game ) : ?>
                <div class="srm-event-name"><?php echo esc_html( $current_game->event_name ); ?></div>
                <?php if ( $current_game->event_name_secondary ) : ?>
                    <div class="srm-event-secondary"><?php echo esc_html( $current_game->event_name_secondary ); ?></div>
                <?php endif; ?>
                <div class="srm-event-time"><?php echo esc_html( SRM_Games::format_time( $current_game->scheduled_time ) ); ?></div>

                <div class="srm-status-label">
                    <?php
                    if ( 'published' === $current_status ) {
                        echo '<span class="srm-badge srm-badge-success">' . esc_html__( 'Result Published', 'scheduled-results-manager' ) . '</span>';
                    } else {
                        echo '<span class="srm-badge srm-badge-warning">' . esc_html__( 'Waiting for Result', 'scheduled-results-manager' ) . '</span>';
                    }
                    ?>
                </div>

                <?php if ( $current_result && 'published' === $current_status ) : ?>
                    <div class="srm-result-value"><?php echo esc_html( $current_result->result_value ); ?></div>
                <?php endif; ?>
            <?php else : ?>
                <p class="srm-no-event"><?php esc_html_e( 'No active game at this time.', 'scheduled-results-manager' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Next Game -->
        <div class="srm-event-card srm-next-card">
            <h2><?php esc_html_e( 'Next Game', 'scheduled-results-manager' ); ?></h2>
            <?php if ( $next_game ) : ?>
                <div class="srm-event-name"><?php echo esc_html( $next_game->event_name ); ?></div>
                <?php if ( $next_game->event_name_secondary ) : ?>
                    <div class="srm-event-secondary"><?php echo esc_html( $next_game->event_name_secondary ); ?></div>
                <?php endif; ?>
                <div class="srm-event-time"><?php echo esc_html( SRM_Games::format_time( $next_game->scheduled_time ) ); ?></div>
                <?php if ( $is_tomorrow ) : ?>
                    <div class="srm-event-meta"><?php esc_html_e( 'Tomorrow', 'scheduled-results-manager' ); ?></div>
                <?php endif; ?>
                <div class="srm-countdown-label"><?php esc_html_e( 'Time Remaining', 'scheduled-results-manager' ); ?></div>
                <div class="srm-countdown" id="srm-admin-countdown" data-timestamp="<?php echo esc_attr( (string) $next_ts ); ?>">
                    00:00:00
                </div>
            <?php else : ?>
                <p class="srm-no-event"><?php esc_html_e( 'No upcoming games.', 'scheduled-results-manager' ); ?></p>
            <?php endif; ?>
        </div>

    </div>

    <!-- Quick Links -->
    <div class="srm-quick-links">
        <h3><?php esc_html_e( 'Quick Actions', 'scheduled-results-manager' ); ?></h3>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=srm-results' ) ); ?>" class="button button-primary">
            <?php esc_html_e( 'Enter Today\'s Results', 'scheduled-results-manager' ); ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=srm-events&action=add' ) ); ?>" class="button button-secondary">
            <?php esc_html_e( 'Add New Game', 'scheduled-results-manager' ); ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=srm-monthly' ) ); ?>" class="button button-secondary">
            <?php esc_html_e( 'Monthly Results', 'scheduled-results-manager' ); ?>
        </a>
    </div>

    <!-- System Info -->
    <div class="srm-system-info">
        <h3><?php esc_html_e( 'System Information', 'scheduled-results-manager' ); ?></h3>
        <table class="srm-info-table">
            <tr>
                <th><?php esc_html_e( 'Plugin Version', 'scheduled-results-manager' ); ?></th>
                <td><?php echo esc_html( SRM_VERSION ); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'WordPress Timezone', 'scheduled-results-manager' ); ?></th>
                <td><?php echo esc_html( wp_timezone_string() ); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Current Server Time', 'scheduled-results-manager' ); ?></th>
                <td><?php echo esc_html( wp_date( 'Y-m-d H:i:s' ) ); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Today\'s Date', 'scheduled-results-manager' ); ?></th>
                <td><?php echo esc_html( wp_date( get_option( 'srm_date_format', 'd/m/Y' ) ) ); ?></td>
            </tr>
        </table>
    </div>
</div>
