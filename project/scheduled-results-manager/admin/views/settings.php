<?php
/**
 * Admin Settings View
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! SRM_Access::current_user_can_access_plugin() ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'scheduled-results-manager' ) );
}

$s = SRM_Settings::get_all();
?>
<div class="wrap srm-admin-wrap"><p class="notice notice-info">AJAX interval controls current/next schedule-panel refresh. Historical tables refresh on navigation. Source review is required even when the default status is Published. Imports always remain drafts. Deletion and uninstall erasure are disabled to preserve history; use Pending or deactivate a game. Style changes below affect the frontend; Theme Styles may override table colors.</p>
    <h1 class="srm-page-title">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php esc_html_e( 'Settings', 'scheduled-results-manager' ); ?>
    </h1>

    <form method="post" action="">
        <?php wp_nonce_field( 'srm_save_settings' ); ?>
        <input type="hidden" name="srm_action" value="save_settings">

        <!-- General Settings -->
        <div class="srm-settings-section">
            <h2><?php esc_html_e( 'General', 'scheduled-results-manager' ); ?></h2>
            <table class="form-table srm-form-table">
                <tr>
                    <th><label for="srm_plugin_name"><?php esc_html_e( 'Plugin Name', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="text" id="srm_plugin_name" name="srm_plugin_name" class="regular-text" value="<?php echo esc_attr( $s['plugin_name'] ); ?>"></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'WordPress Timezone', 'scheduled-results-manager' ); ?></th>
                    <td>
                        <code><?php echo esc_html( $s['timezone'] ); ?></code>
                        <p class="description">
                            <?php
                            printf(
                                /* translators: %s link */
                                esc_html__( 'To change the timezone, go to %s.', 'scheduled-results-manager' ),
                                '<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'General Settings', 'scheduled-results-manager' ) . '</a>'
                            );
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><label for="srm_date_format"><?php esc_html_e( 'Date Format', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="srm_date_format" name="srm_date_format" class="regular-text" value="<?php echo esc_attr( $s['date_format'] ); ?>">
                        <p class="description"><?php esc_html_e( 'PHP date format. Example: d/m/Y', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="srm_time_format"><?php esc_html_e( 'Time Format', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="srm_time_format" name="srm_time_format" class="regular-text" value="<?php echo esc_attr( $s['time_format'] ); ?>">
                        <p class="description"><?php esc_html_e( 'PHP time format. Example: h:i A (12hr) or H:i (24hr)', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Live Status Settings -->
        <div class="srm-settings-section">
            <h2><?php esc_html_e( 'Live Status', 'scheduled-results-manager' ); ?></h2>
            <table class="form-table srm-form-table">
                <tr>
                    <th><label for="srm_ajax_interval"><?php esc_html_e( 'AJAX Refresh Interval (seconds)', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <input type="number" id="srm_ajax_interval" name="srm_ajax_interval" class="small-text" value="<?php echo esc_attr( (string) $s['ajax_interval'] ); ?>" min="10" max="300">
                        <p class="description"><?php esc_html_e( 'How often to poll the server for live updates. Minimum 10 seconds.', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Countdown', 'scheduled-results-manager' ); ?></th>
                    <td><label><input type="checkbox" name="srm_countdown_enabled" value="1" <?php checked( $s['countdown_enabled'], '1' ); ?>> <?php esc_html_e( 'Enable countdown timer', 'scheduled-results-manager' ); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Current Game', 'scheduled-results-manager' ); ?></th>
                    <td><label><input type="checkbox" name="srm_current_game_enabled" value="1" <?php checked( $s['current_game_enabled'], '1' ); ?>> <?php esc_html_e( 'Show current game widget', 'scheduled-results-manager' ); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Next Game', 'scheduled-results-manager' ); ?></th>
                    <td><label><input type="checkbox" name="srm_next_game_enabled" value="1" <?php checked( $s['next_game_enabled'], '1' ); ?>> <?php esc_html_e( 'Show next game widget', 'scheduled-results-manager' ); ?></label></td>
                </tr>
            </table>
        </div>

        <!-- Results Settings -->
        <div class="srm-settings-section">
            <h2><?php esc_html_e( 'Results', 'scheduled-results-manager' ); ?></h2>
            <table class="form-table srm-form-table">
                <tr>
                    <th><label for="srm_result_format"><?php esc_html_e( 'Result Format', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <select id="srm_result_format" name="srm_result_format">
                            <option value="any"     <?php selected( $s['result_format'], 'any' ); ?>><?php esc_html_e( 'Any (alphanumeric)', 'scheduled-results-manager' ); ?></option>
                            <option value="numeric" <?php selected( $s['result_format'], 'numeric' ); ?>><?php esc_html_e( 'Numeric only', 'scheduled-results-manager' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="srm_default_result_status"><?php esc_html_e( 'Default Result Status', 'scheduled-results-manager' ); ?></label></th>
                    <td>
                        <select id="srm_default_result_status" name="srm_default_result_status">
                            <option value="pending"   <?php selected( $s['default_result_status'], 'pending' ); ?>><?php esc_html_e( 'Pending', 'scheduled-results-manager' ); ?></option>
                            <option value="published" <?php selected( $s['default_result_status'], 'published' ); ?>><?php esc_html_e( 'Published', 'scheduled-results-manager' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Allow Editing', 'scheduled-results-manager' ); ?></th>
                    <td><label><input type="checkbox" name="srm_allow_editing" value="1" <?php checked( $s['allow_editing'], '1' ); ?>> <?php esc_html_e( 'Allow editing existing results', 'scheduled-results-manager' ); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Allow Deletion', 'scheduled-results-manager' ); ?></th>
                    <td><label><input type="checkbox" disabled name="srm_allow_deletion" value="1" <?php checked( $s['allow_deletion'], '1' ); ?>> <?php esc_html_e( 'Allow deleting results', 'scheduled-results-manager' ); ?></label></td>
                </tr>
            </table>
        </div>

        <!-- Frontend / Table Styling -->
        <div class="srm-settings-section">
            <h2><?php esc_html_e( 'Frontend Table Styling', 'scheduled-results-manager' ); ?></h2>
            <table class="form-table srm-form-table">
                <tr>
                    <th><label for="srm_header_bg"><?php esc_html_e( 'Header Background', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_header_bg" name="srm_header_bg" value="<?php echo esc_attr( $s['header_bg'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_header_color"><?php esc_html_e( 'Header Text Color', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_header_color" name="srm_header_color" value="<?php echo esc_attr( $s['header_color'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_table_bg"><?php esc_html_e( 'Table Background', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_table_bg" name="srm_table_bg" value="<?php echo esc_attr( $s['table_bg'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_alt_row_color"><?php esc_html_e( 'Alternate Row Color', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_alt_row_color" name="srm_alt_row_color" value="<?php echo esc_attr( $s['alt_row_color'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_border_color"><?php esc_html_e( 'Border Color', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_border_color" name="srm_border_color" value="<?php echo esc_attr( $s['border_color'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_text_color"><?php esc_html_e( 'Text Color', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_text_color" name="srm_text_color" value="<?php echo esc_attr( $s['text_color'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_card_bg"><?php esc_html_e( 'Card Background', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_card_bg" name="srm_card_bg" value="<?php echo esc_attr( $s['card_bg'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_card_color"><?php esc_html_e( 'Card Text Color', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_card_color" name="srm_card_color" value="<?php echo esc_attr( $s['card_color'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_card_accent"><?php esc_html_e( 'Card Accent Color', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="color" id="srm_card_accent" name="srm_card_accent" value="<?php echo esc_attr( $s['card_accent'] ); ?>"></td>
                </tr>
                <tr>
                    <th><label for="srm_font_size"><?php esc_html_e( 'Font Size (px)', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="number" id="srm_font_size" name="srm_font_size" class="small-text" value="<?php echo esc_attr( (string) $s['font_size'] ); ?>" min="10" max="24"></td>
                </tr>
                <tr>
                    <th><label for="srm_header_font_size"><?php esc_html_e( 'Header Font Size (px)', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="number" id="srm_header_font_size" name="srm_header_font_size" class="small-text" value="<?php echo esc_attr( (string) $s['header_font_size'] ); ?>" min="10" max="24"></td>
                </tr>
                <tr>
                    <th><label for="srm_border_width"><?php esc_html_e( 'Border Width (px)', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="number" id="srm_border_width" name="srm_border_width" class="small-text" value="<?php echo esc_attr( (string) $s['border_width'] ); ?>" min="0" max="5"></td>
                </tr>
                <tr>
                    <th><label for="srm_cell_padding"><?php esc_html_e( 'Cell Padding (px)', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="number" id="srm_cell_padding" name="srm_cell_padding" class="small-text" value="<?php echo esc_attr( (string) $s['cell_padding'] ); ?>" min="2" max="30"></td>
                </tr>
                <tr>
                    <th><label for="srm_mobile_font_size"><?php esc_html_e( 'Mobile Font Size (px)', 'scheduled-results-manager' ); ?></label></th>
                    <td><input type="number" id="srm_mobile_font_size" name="srm_mobile_font_size" class="small-text" value="<?php echo esc_attr( (string) $s['mobile_font_size'] ); ?>" min="10" max="20"></td>
                </tr>
            </table>
        </div>

        <!-- Data Management -->
        <div class="srm-settings-section srm-danger-zone">
            <h2><?php esc_html_e( 'Data Management', 'scheduled-results-manager' ); ?></h2>
            <table class="form-table srm-form-table">
                <tr>
                    <th><?php esc_html_e( 'Delete All Data on Uninstall', 'scheduled-results-manager' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" disabled name="srm_delete_on_uninstall" value="1" <?php checked( $s['delete_on_uninstall'], '1' ); ?>>
                            <?php esc_html_e( 'Delete all plugin data when the plugin is uninstalled', 'scheduled-results-manager' ); ?>
                        </label>
                        <p class="description srm-warning"><?php esc_html_e( 'Protected: uninstall always retains games, results and settings. Back up the database before making changes.', 'scheduled-results-manager' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button( __( 'Save Settings', 'scheduled-results-manager' ) ); ?>
    </form>
</div>
