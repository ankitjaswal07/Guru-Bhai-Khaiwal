<?php
/**
 * Theme setup.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports, menus and image sizes.
 */
function s20_setup() {
	load_theme_textdomain( 'guru-bhai-khaiwal', S20_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 291,
			'width'       => 1123,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu (Header)', 'guru-bhai-khaiwal' ),
			'footer'  => __( 'Footer Quick Links (optional, overrides first footer column)', 'guru-bhai-khaiwal' ),
		)
	);

	// Blog card image (matches the 825x609 ratio used on the live site).
	add_image_size( 's20-card', 825, 609, true );
	add_image_size( 's20-card-small', 480, 354, true );

	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1300;
	}
}
add_action( 'after_setup_theme', 's20_setup' );

/**
 * Remove the emoji polyfill (native emoji are used throughout the design).
 */
function s20_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 's20_disable_emojis' );

/**
 * Excerpt tweaks for blog cards.
 */
function s20_excerpt_length( $length ) {
	return is_admin() ? $length : 16;
}
add_filter( 'excerpt_length', 's20_excerpt_length', 999 );

function s20_excerpt_more( $more ) {
	return is_admin() ? $more : '...';
}
add_filter( 'excerpt_more', 's20_excerpt_more' );

/**
 * Body classes.
 */
function s20_body_classes( $classes ) {
	$classes[] = 's20-body';
	if ( is_front_page() ) {
		$classes[] = 's20-front';
	}
	if ( ! s20_acf_active() ) {
		$classes[] = 's20-no-acf';
	}
	return $classes;
}
add_filter( 'body_class', 's20_body_classes' );

/**
 * Register a couple of widget-less footer sidebars is intentionally skipped:
 * footer content is managed 100% through ACF (Guru Bhai Khaiwal > Footer).
 */
