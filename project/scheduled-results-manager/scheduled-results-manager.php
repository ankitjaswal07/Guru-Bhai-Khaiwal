<?php
/**
 * Plugin Name: Guru Bhai Khaiwal Results Manager
 * Description: Informational schedules and source-reviewed historical archives. Compatible upgrade of Scheduled Results Manager.
 * Version: 2.0.0
 * Author: Guru Bhai Khaiwal
 * License: GPL-2.0-or-later
 * Text Domain: scheduled-results-manager
 * Requires at least: 6.7
 * Requires PHP: 8.1
 */
defined( 'ABSPATH' ) || exit;
if ( ! function_exists( 'mb_check_encoding' ) ) {
    add_action( 'admin_notices', function () { echo '<div class="notice notice-error"><p>Guru Bhai Khaiwal Results Manager requires the PHP mbstring extension. Ask your host to enable it.</p></div>'; } );
    register_activation_hook( __FILE__, function () { wp_die( 'Enable the PHP mbstring extension before activating Guru Bhai Khaiwal Results Manager.' ); } );
    return;
}
define( 'SRM_VERSION', '2.0.0' );
define( 'SRM_PLUGIN_FILE', __FILE__ );
define( 'SRM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SRM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
foreach ( array( 'access', 'database', 'events', 'seed', 'results', 'import-export', 'current-event', 'shortcodes', 'api' ) as $srm_file ) {
    require_once SRM_PLUGIN_DIR . 'includes/class-' . $srm_file . '.php';
}
require_once SRM_PLUGIN_DIR . 'admin/class-admin.php';
register_activation_hook( __FILE__, function ( $network_wide = false ) {
    if ( $network_wide ) { wp_die( 'Network activation is not supported. Use a single-site installation and consult the migration guide.' ); }
    SRM_Database::install();
} );
add_action( 'init', function () {
    if ( version_compare( (string) get_option( SRM_Database::DB_VERSION_OPTION, '0' ), SRM_Database::DB_VERSION, '<' ) ) {
        SRM_Database::install();
    }
    SRM_Shortcodes::register();
} );
add_action( 'rest_api_init', array( 'SRM_API', 'register_routes' ) );
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'gbk-records', SRM_PLUGIN_URL . 'public/records.css', array(), SRM_VERSION );
} );
SRM_Admin::init();

add_action( 'admin_notices', function () {
    if ( current_user_can( 'manage_options' ) && get_option( 'gbk_schema_error' ) ) { echo '<div class="notice notice-error"><p>' . esc_html( get_option( 'gbk_schema_error' ) ) . '</p></div>'; }
} );
