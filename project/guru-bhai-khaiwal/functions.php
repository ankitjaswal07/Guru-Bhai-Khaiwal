<?php
/**
 * Guru Bhai Khaiwal theme bootstrap.
 *
 * Architecture:
 *   Existing results plugin  -> provides ALL game / result data through shortcodes.
 *   ACF PRO                  -> manages every static text, image, link and shortcode setting.
 *   This theme               -> markup, CSS, vanilla JS and shortcode display only.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

define( 'S20_VERSION', '2.1.0' );
define( 'S20_DIR', get_template_directory() );
define( 'S20_URI', get_template_directory_uri() );

$s20_includes = array(
	'theme-setup',        // Theme supports, menus, image sizes.
	'enqueue',            // Conditional CSS / JS loading.
	'icons',              // Small inline SVG icon set.
	'brand-defaults',
	'helpers',            // Escaping, ACF getters, URL helpers.
	'shortcodes',         // Shortcode resolving + rendering helpers and tiny utility shortcodes.
	'acf',                // ACF availability checks / admin notices.
	'acf-options',        // ACF PRO options pages (Guru Bhai Khaiwal admin menu).
	'acf-fields',         // ACF PRO field groups registered in PHP.
	'acf-defaults',
	'theme-styles',
	'seo',
	'compatibility',       // One-time seeding of repeater defaults (live-site content).
	'template-functions', // Template output helpers (sections, headings, buttons, notices).
	'blog',               // Blog query helpers.
	'contact-form',       // Built-in fallback contact form handler.
);

foreach ( $s20_includes as $s20_file ) {
	require_once S20_DIR . '/inc/' . $s20_file . '.php';
}
unset( $s20_includes, $s20_file );
