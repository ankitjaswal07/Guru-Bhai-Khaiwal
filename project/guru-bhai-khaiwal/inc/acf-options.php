<?php
/**
 * ACF PRO options pages: WordPress admin menu "Guru Bhai Khaiwal".
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Guru Bhai Khaiwal admin menu and its sub pages.
 */
function s20_register_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Guru Bhai Khaiwal Theme Settings', 'guru-bhai-khaiwal' ),
			'menu_title' => __( 'Guru Bhai Khaiwal', 'guru-bhai-khaiwal' ),
			'menu_slug'  => 's20-settings',
			'capability' => 'manage_options',
			'redirect'   => true,
			'icon_url'   => 'dashicons-star-filled',
			'position'   => 59,
		)
	);

	$sub_pages = array(
		's20-general'    => __( 'General Settings', 'guru-bhai-khaiwal' ),
		's20-homepage'   => __( 'Homepage Settings', 'guru-bhai-khaiwal' ),
		's20-styles'     => __( 'Theme Styles', 'guru-bhai-khaiwal' ),
		's20-header'     => __( 'Header Settings', 'guru-bhai-khaiwal' ),
		's20-results'    => __( 'Results Settings', 'guru-bhai-khaiwal' ),
		's20-contact'    => __( 'Contact Settings', 'guru-bhai-khaiwal' ),
		's20-payment'    => __( 'Payment Settings', 'guru-bhai-khaiwal' ),
		's20-faq'        => __( 'FAQ Settings', 'guru-bhai-khaiwal' ),
		's20-footer'     => __( 'Footer Settings', 'guru-bhai-khaiwal' ),
		's20-social'     => __( 'Social Settings', 'guru-bhai-khaiwal' ),
		's20-shortcodes' => __( 'Shortcode Settings', 'guru-bhai-khaiwal' ),
	);

	foreach ( $sub_pages as $slug => $title ) {
		acf_add_options_sub_page(
			array(
				'page_title'  => $title,
				'menu_title'  => str_replace( ' Settings', '', $title ),
				'menu_slug'   => $slug,
				'parent_slug' => 's20-settings',
				'post_id' => 's20-homepage' === $slug && get_option( 'page_on_front' ) ? (int) get_option( 'page_on_front' ) : 'options',
				'capability'  => 'manage_options',
			)
		);
	}
}
add_action( 'acf/init', 's20_register_options_pages' );

/**
 * Shortcut link to the existing results plugin (no duplicate management screen).
 */
function s20_plugin_shortcut_menu() {
	if ( ! s20_acf_active() ) {
		return;
	}
	$target = trim( (string) s20_get_option( 'plugin_admin_url', 'admin.php?page=srm-results' ) );
	if ( '' === $target ) {
		return;
	}
	add_submenu_page(
		's20-settings',
		__( 'Games & Results (Plugin)', 'guru-bhai-khaiwal' ),
		__( 'Games & Results →', 'guru-bhai-khaiwal' ),
		'manage_options',
		( 0 === strpos( $target, 'http' ) ) ? $target : admin_url( ltrim( $target, '/' ) )
	);
}
add_action( 'admin_menu', 's20_plugin_shortcut_menu', 99 );
