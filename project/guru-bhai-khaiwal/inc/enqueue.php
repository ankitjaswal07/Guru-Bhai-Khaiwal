<?php
/**
 * Conditional asset loading.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the current request renders result/shortcode sections.
 */
function s20_is_results_context() {
	if ( is_front_page() ) {
		return true;
	}
	$templates = array(
		'page-templates/template-result-chart.php',
		'page-templates/template-today-results.php',
		'page-templates/template-yesterday-results.php',
		'page-templates/template-monthly-results.php',
		'page-templates/template-game-results.php',
	);
	foreach ( $templates as $template ) {
		if ( is_page_template( $template ) ) {
			return true;
		}
	}
	return (bool) apply_filters( 's20_is_results_context', false );
}

/**
 * Front-end styles and scripts.
 */
function s20_enqueue_assets() {
	$css = S20_URI . '/assets/css/';
	$js  = S20_URI . '/assets/js/';

	if ( s20_get_option( 'load_google_fonts', 0 ) ) {
		wp_enqueue_style(
			's20-fonts',
			'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap',
			array(),
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		);
	}

	wp_enqueue_style( 's20-main', $css . 'main.css', array(), S20_VERSION );
	wp_enqueue_style( 's20-header', $css . 'header.css', array( 's20-main' ), S20_VERSION );
	wp_enqueue_style( 's20-footer', $css . 'footer.css', array( 's20-main' ), S20_VERSION );
	// Plugin shortcodes may be placed inside any content, so the (tiny) table wrapper CSS is always available.
	wp_enqueue_style( 's20-tables', $css . 'tables.css', array( 's20-main' ), S20_VERSION );

	if ( is_front_page() ) {
		wp_enqueue_style( 's20-homepage', $css . 'homepage.css', array( 's20-main' ), S20_VERSION );
	}

	if ( s20_is_results_context() ) {
		wp_enqueue_style( 's20-results', $css . 'results.css', array( 's20-main' ), S20_VERSION );
	}

	$blog_context = is_home() || is_archive() || is_single() || is_search() || is_page_template( 'page-templates/template-blog.php' ) || ( is_front_page() && s20_get_option( 'blog_enabled', 1 ) );
	if ( $blog_context ) {
		wp_enqueue_style( 's20-blog', $css . 'blog.css', array( 's20-main' ), S20_VERSION );
	}

	$faq_context = is_page_template( 'page-templates/template-faq.php' ) || ( is_front_page() && s20_get_option( 'faq_enabled', 1 ) );
	if ( $faq_context ) {
		wp_enqueue_style( 's20-faq', $css . 'faq.css', array( 's20-main' ), S20_VERSION );
	}

	// Responsive rules always load last.
	wp_enqueue_style( 's20-responsive', $css . 'responsive.css', array( 's20-main' ), S20_VERSION );

	// Scripts (vanilla JS, deferred).
	$defer = array( 'strategy' => 'defer', 'in_footer' => true );
	wp_enqueue_script( 's20-navigation', $js . 'navigation.js', array(), S20_VERSION, $defer );

	if ( is_front_page() ) {
		wp_enqueue_script( 's20-main', $js . 'main.js', array(), S20_VERSION, $defer );
		wp_localize_script( 's20-main', 's20Clock', array( 'timezone' => wp_timezone_string() ) );
	}
	if ( $blog_context && is_front_page() && 'carousel' === s20_get_option( 'blog_layout', 'carousel' ) ) {
		wp_enqueue_script( 's20-blog', $js . 'blog.js', array(), S20_VERSION, $defer );
	}
	if ( $faq_context ) {
		wp_enqueue_script( 's20-faq', $js . 'faq.js', array(), S20_VERSION, $defer );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 's20_enqueue_assets' );

/**
 * Preconnect for Google Fonts.
 */
function s20_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && s20_get_option( 'load_google_fonts', 0 ) ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 's20_resource_hints', 10, 2 );

/**
 * Small admin stylesheet for the Guru Bhai Khaiwal options pages.
 */
function s20_admin_assets( $hook ) {
	if ( false !== strpos( $hook, 's20-' ) ) {
		wp_enqueue_style( 's20-admin', S20_URI . '/assets/css/admin.css', array(), S20_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 's20_admin_assets' );
