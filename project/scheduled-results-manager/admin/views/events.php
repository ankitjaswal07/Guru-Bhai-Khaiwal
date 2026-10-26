<?php
/**
 * Admin Games View
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! SRM_Access::current_user_can_access_plugin() ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'scheduled-results-manager' ) );
}

$action   = sanitize_key( $_GET['action'] ?? 'list' );
$event_id = absint( $_GET['event_id'] ?? 0 );
$events   = SRM_Games::get_all_games();
$editing  = null;

if ( 'edit' === $action && $event_id ) {
    $editing = SRM_Games::get_game( $event_id );
}

// Show inline error if any
$error_msg = isset( $_GET['srm_error'] ) ? sanitize_text_field( urldecode( $_GET['srm_error'] ) ) : '';
?>
<div class="wrap srm-admin-wrap">
    <h1 class="srm-page-title">
        <span class="dashicons dashicons-list-view"></span>
        <?php esc_html_e( 'Games', 'scheduled-results-manager' ); ?>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=srm-events&action=add' ) ); ?>" class="page-title-action">
            <?php esc_html_e( 'Add New', 'scheduled-results-manager' ); ?>
        </a>
    </h1>

    <?php if ( $error_msg ) : ?>
        <div class="notice notice-error"><p><?php echo esc_html( $error_msg ); ?></p></div>
    <?php endif; ?>

    <?php if ( 'add' === $action || 'edit' === $action ) : ?>
    <!-- Add / Edit Form -->
    <div class="srm-form-wrap">
        <h2><?php echo 'edit' === $action ? esc_html__( 'Edit Game', 'scheduled-results-manager' ) : esc_html__( 'Add New Game', 'scheduled-results-manager' ); ?></h2>

        <form method="post" action="">
            <?php
            if ( 'edit' === $action ) {
                wp_nonce_field( 'srm_update_game' );
                echo '<input type="hidden" name="srm_action" value="update_game">';
                echo '<input type="hidden" name="event_id" value="' . esc_attr( (string) $event_id ) . '">';
            } else {
                wp_nonce_field( 'srm_create_game' );
                echo '<input type="hidden" name="srm_action" value="create_game">';
            }
            ?>

            <table class="form-table srm-form-table">
                <tr>
                    <th><label for="event_name"><?php esc_html_e( 'Game Name', 'scheduled-results-manager' ); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="text" id="event_name" name="event_name" class="regular-text"
                               value="<?php echo esc_attr( $editing->event_name ?? '' ); ?>" required>
                        <p class="description"><?php esc_html_e( 'Primary game name.', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_name_secondary"><?php esc_html_e( 'Secondary Name', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="event_name_secondary" name="event_name_secondary" class="regular-text"
                               value="<?php echo esc_attr( $editing->event_name_secondary ?? '' ); ?>">
                        <p class="description"><?php esc_html_e( 'Optional alternate language name.', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="scheduled_time"><?php esc_html_e( 'Scheduled Time', 'scheduled-results-manager' ); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="time" id="scheduled_time" name="scheduled_time" class="srm-time-picker"
                               value="<?php echo esc_attr( isset( $editing->scheduled_time ) ? substr( $editing->scheduled_time, 0, 5 ) : '' ); ?>" required>
                        <p class="description"><?php esc_html_e( '24-hour format. Example: 13:10 for 01:10 PM.', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="display_order"><?php esc_html_e( 'Display Order', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <input type="number" id="display_order" name="display_order" class="small-text"
                               value="<?php echo esc_attr( (string) ( $editing->display_order ?? 0 ) ); ?>" min="0">
                        <p class="description"><?php esc_html_e( 'Lower numbers appear first.', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Active', 'scheduled-results-manager' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="status" value="1"
                                   <?php checked( (int) ( $editing->status ?? 1 ), 1 ); ?>>
                            <?php esc_html_e( 'Enable this game', 'scheduled-results-manager' ); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button button-primary"
                       value="<?php echo 'edit' === $action ? esc_attr__( 'Update Game', 'scheduled-results-manager' ) : esc_attr__( 'Add Game', 'scheduled-results-manager' ); ?>">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=srm-events' ) ); ?>" class="button button-secondary">
                    <?php esc_html_e( 'Cancel', 'scheduled-results-manager' ); ?>
                </a>
            </p>
        </form>
    </div>
    <?php endif; ?>

    <!-- Games Table -->
    <div class="srm-table-wrap">
        <h2><?php esc_html_e( 'All Games', 'scheduled-results-manager' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Drag rows to reorder. Changes save automatically.', 'scheduled-results-manager' ); ?></p>

        <?php if ( empty( $events ) ) : ?>
            <p><?php esc_html_e( 'No games found. Add your first game above.', 'scheduled-results-manager' ); ?></p>
        <?php else : ?>
        <table class="wp-list-table widefat fixed striped srm-events-table" id="srm-sortable-events">
            <thead>
                <tr>
                    <th class="srm-drag-col"></th>
                    <th><?php esc_html_e( 'Order', 'scheduled-results-manager' ); ?></th>
                    <th><?php esc_html_e( 'Game Name', 'scheduled-results-manager' ); ?></th>
                    <th><?php esc_html_e( 'Secondary Name', 'scheduled-results-manager' ); ?></th>
                    <th><?php esc_html_e( 'Scheduled Time', 'scheduled-results-manager' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'scheduled-results-manager' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'scheduled-results-manager' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $events as $event ) : ?>
                <tr data-event-id="<?php echo esc_attr( (string) $event->id ); ?>">
                    <td class="srm-drag-handle"><span class="dashicons dashicons-move" title="<?php esc_attr_e( 'Drag to reorder', 'scheduled-results-manager' ); ?>"></span></td>
                    <td><?php echo esc_html( (string) $event->display_order ); ?></td>
                    <td><strong><?php echo esc_html( $event->event_name ); ?></strong></td>
                    <td><?php echo esc_html( $event->event_name_secondary ?? '—' ); ?></td>
                    <td><?php echo esc_html( SRM_Games::format_time( $event->scheduled_time ) ); ?></td>
                    <td>
                        <?php if ( $event->status ) : ?>
                            <span class="srm-badge srm-badge-success"><?php esc_html_e( 'Active', 'scheduled-results-manager' ); ?></span>
                        <?php else : ?>
                            <span class="srm-badge srm-badge-inactive"><?php esc_html_e( 'Inactive', 'scheduled-results-manager' ); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="srm-actions">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=srm-events&action=edit&event_id=' . $event->id ) ); ?>" class="button button-small">
                            <?php esc_html_e( 'Edit', 'scheduled-results-manager' ); ?>
                        </a>
                        <form method="post" style="display:inline">
                          <input type="hidden" name="srm_action" value="duplicate_game"><input type="hidden" name="event_id" value="<?php echo absint($event->id); ?>">
                          <?php wp_nonce_field('srm_duplicate_game_'.$event->id); ?><button class="button button-small">Duplicate</button>
                        </form>
                        <button class="button button-small srm-toggle-status"
                                data-event-id="<?php echo esc_attr( (string) $event->id ); ?>"
                                data-status="<?php echo esc_attr( (string) $event->status ); ?>">
                            <?php echo $event->status ? esc_html__( 'Disable', 'scheduled-results-manager' ) : esc_html__( 'Enable', 'scheduled-results-manager' ); ?>
                        </button>
                        <button type="button" class="button button-small" disabled title="Retained for compatibility. Disable a game to preserve its history.">Delete (protected)</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
