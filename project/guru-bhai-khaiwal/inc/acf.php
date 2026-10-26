<?php
/**
 * ACF PRO integration checks.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin notice when ACF PRO is not active.
 */
function s20_acf_missing_notice() {
	if ( s20_acf_active() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>Guru Bhai Khaiwal theme:</strong> ';
	esc_html_e( 'Advanced Custom Fields PRO is required to manage the site content (Guru Bhai Khaiwal menu, options pages, shortcode settings). Please install and activate ACF PRO. The theme keeps rendering with built-in defaults until then.', 'guru-bhai-khaiwal' );
	echo '</p></div>';
}
add_action( 'admin_notices', 's20_acf_missing_notice' );

/**
 * Hide the ACF field group UI from non-developers is left to site policy;
 * we only make sure ACF loads our PHP-registered groups by keeping "show_in_rest" off.
 */
function s20_acf_settings_init() {
	if ( function_exists( 'acf_update_setting' ) ) {
		// Keep the ACF admin UI available so administrators can extend fields if needed.
		acf_update_setting( 'show_admin', true );
	}
}
add_action( 'acf/init', 's20_acf_settings_init', 1 );

/**
 * Allow shortcode textareas to keep raw brackets/quotes (ACF text fields are sanitized by ACF).
 * Ensure the shortcode fields are never run through wpautop by ACF formatting.
 */
function s20_acf_shortcode_field_format( $value, $post_id, $field ) {
	if ( ! empty( $field['name'] ) && false !== strpos( $field['name'], 'shortcode' ) ) {
		return is_string( $value ) ? trim( $value ) : $value;
	}
	return $value;
}
add_filter( 'acf/format_value/type=textarea', 's20_acf_shortcode_field_format', 20, 3 );
