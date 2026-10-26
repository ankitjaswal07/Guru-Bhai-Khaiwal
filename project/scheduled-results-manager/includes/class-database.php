<?php
/**
 * Database Setup Class
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SRM_Database
 * Handles database table creation and upgrades.
 */
class SRM_Database {

    /**
     * Plugin database version option key.
     */
    const DB_VERSION_OPTION = 'srm_db_version';

    /**
     * Current database version.
     */
    const DB_VERSION = '2.0.0';

    /**
     * Get events table name.
     */
    public static function events_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'srm_events';
    }

    /**
     * Get results table name.
     */
    public static function results_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'srm_results';
    }

    /**
     * Run on plugin activation.
     */
    public static function install(): void {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Load dbDelta helper
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // --- Games table ---
        $events_table = self::events_table();
        $sql_events   = "CREATE TABLE {$events_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_name VARCHAR(191) NOT NULL,
            event_name_secondary VARCHAR(191) NULL DEFAULT NULL,
            slug VARCHAR(191) NOT NULL,
            scheduled_time TIME NOT NULL,
            display_order INT NOT NULL DEFAULT 0,
            status TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY scheduled_time (scheduled_time),
            KEY status (status),
            KEY display_order (display_order)
        ) {$charset_collate};";

        dbDelta( $sql_events );

        // --- Results table ---
        $results_table = self::results_table();
        $sql_results   = "CREATE TABLE {$results_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id BIGINT UNSIGNED NOT NULL,
            result_date DATE NOT NULL,
            result_value VARCHAR(50) NULL DEFAULT NULL,
            result_status VARCHAR(30) NOT NULL DEFAULT 'pending',
            original_game_name VARCHAR(191) NOT NULL DEFAULT '',
            source_url TEXT NULL,
            verification_status VARCHAR(30) NOT NULL DEFAULT 'unverified',
            verification_notes TEXT NULL,
            reviewed_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
            verified_at DATETIME NULL DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY event_date (event_id, result_date),
            KEY event_id (event_id),
            KEY result_date (result_date),
            KEY result_status (result_status),
            KEY public_archive (result_status, verification_status, result_date)
        ) {$charset_collate};";

        dbDelta( $sql_results );

        $logs = $wpdb->prefix . 'srm_import_logs';
        dbDelta( "CREATE TABLE {$logs} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL,
            file_hash VARCHAR(64) NOT NULL,
            summary LONGTEXT NOT NULL,
            PRIMARY KEY (id),
            KEY created_at (created_at)
        ) {$charset_collate};" );

        // Save default options
        self::save_default_options();

        // Add custom capability to administrator role
        self::add_capabilities();

        // Do not mark a partial/failed migration as complete.
        $columns = $wpdb->get_col( "SHOW COLUMNS FROM {$results_table}", 0 );
        $log_columns = $wpdb->get_col( "SHOW COLUMNS FROM {$logs}", 0 );
        if ( array_diff( array( 'source_url', 'original_game_name', 'verification_status', 'reviewed_by', 'verified_at' ), $columns ?: array() ) || ! in_array( 'summary', $log_columns ?: array(), true ) ) {
            update_option( 'gbk_schema_error', 'Database migration incomplete. Restore your backup if necessary and check database ALTER/CREATE privileges and the server log.' );
            return;
        }
        delete_option( 'gbk_schema_error' );
        // Store DB version only after the additive schema is available.
        update_option( self::DB_VERSION_OPTION, self::DB_VERSION );

        // Seed client schedules only on an empty installation. Never seed results.
        require_once SRM_PLUGIN_DIR . 'includes/class-seed.php';
        SRM_Seed_Data::seed_all();
    }

    /**
     * Save default plugin options on fresh install.
     */
    private static function save_default_options(): void {
        $defaults = array(
            'srm_plugin_name'              => 'Guru Bhai Khaiwal Results Manager',
            'srm_date_format'              => 'd/m/Y',
            'srm_time_format'              => 'h:i A',
            'srm_result_format'            => 'any',
            'srm_default_result_status'    => 'pending',
            'srm_allow_editing'            => '1',
            'srm_allow_deletion'           => '1',
            'srm_ajax_interval'            => '30',
            'srm_countdown_enabled'        => '1',
            'srm_current_game_enabled'    => '1',
            'srm_next_game_enabled'       => '1',
            'srm_delete_on_uninstall'      => '0',
            // Table styles
            'srm_header_bg'                => '#1e293b',
            'srm_header_color'             => '#ffffff',
            'srm_table_bg'                 => '#ffffff',
            'srm_alt_row_color'            => '#f8fafc',
            'srm_border_color'             => '#e2e8f0',
            'srm_text_color'               => '#1e293b',
            'srm_font_size'                => '14',
            'srm_header_font_size'         => '13',
            'srm_border_width'             => '1',
            'srm_cell_padding'             => '10',
            'srm_mobile_font_size'         => '12',
            // Card styles
            'srm_card_bg'                  => '#1e293b',
            'srm_card_color'               => '#ffffff',
            'srm_card_accent'              => '#f59e0b',
        );

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }
    }

    /**
     * Add custom capabilities.
     */
    public static function add_capabilities(): void {
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->add_cap( 'manage_scheduled_results' );
            $admin->add_cap( SRM_Access::capability() );
        }
    }

    /**
     * Remove capabilities on uninstall.
     */
    public static function remove_capabilities(): void {
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->remove_cap( 'manage_scheduled_results' );
            $admin->remove_cap( SRM_Access::capability() );
        }
    }

}
