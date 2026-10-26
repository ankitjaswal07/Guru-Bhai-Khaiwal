<?php
/**
 * Shortcode integration with the existing results plugin.
 *
 * The theme never hard-codes plugin shortcode names. Administrators enter them in
 * Guru Bhai Khaiwal > Shortcodes (global) or on individual pages (page-specific fields).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve which shortcode to use: page-level field first, then global option.
 *
 * @param string $page_field   Field name on the current page (may be '').
 * @param string $option_field Field name in Guru Bhai Khaiwal > Shortcodes.
 * @return string Raw shortcode string ('' when none configured).
 */
function s20_resolve_shortcode( $page_field = '', $option_field = '' ) {
	$shortcode = '';
	if ( $page_field && is_singular() ) {
		$shortcode = s20_get_field( $page_field, get_the_ID(), '' );
	}
	if ( '' === trim( (string) $shortcode ) && $option_field ) {
		$shortcode = s20_get_option( $option_field, '' );
	}
	return trim( (string) $shortcode );
}

/**
 * Whether a string looks like a shortcode ("[something ...]").
 */
function s20_is_shortcode( $value ) {
	return (bool) preg_match( '/\[[a-zA-Z0-9_\-]+[^\]]*\]/', (string) $value );
}

/**
 * Execute an ACF-entered shortcode string and return its output.
 *
 * The value is intentionally passed to do_shortcode() (that is the whole point of the
 * field); only administrators can edit ACF options, and non-shortcode text is escaped.
 *
 * @param string $shortcode Raw shortcode string(s).
 * @return string HTML output ('' when empty).
 */
function s20_shortcode_output( $shortcode ) {
	$shortcode = trim( (string) $shortcode );
	if ( '' === $shortcode ) {
		return '';
	}
	if ( ! s20_is_shortcode( $shortcode ) ) {
		// Plain text was entered - show it safely instead of executing anything.
		return '<p class="s20-shortcode-plain">' . esc_html( $shortcode ) . '</p>';
	}
	if ( preg_match('/\[([a-zA-Z0-9_-]+)/',$shortcode,$match) && !shortcode_exists($match[1]) ) { return s20_shortcode_fallback('The informational archive is not available. Please check back later.'); }
	$output = do_shortcode( shortcode_unautop( $shortcode ) );
	return trim( (string) $output );
}

/**
 * Echo a shortcode wrapped in the responsive table scroller.
 *
 * @param string $shortcode Raw shortcode.
 * @param string $class     Extra wrapper classes.
 * @return bool True when something was printed.
 */
function s20_render_shortcode( $shortcode, $class = '' ) {
	$output = s20_shortcode_output( $shortcode );
	if ( '' === $output ) {
		return false;
	}
	echo '<div class="s20-shortcode s20-table-scroll ' . esc_attr( $class ) . '">' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output.
	return true;
}

/**
 * Fallback markup when a shortcode field is empty (behaviour configurable in ACF).
 *
 * @return string '' when the section should be hidden.
 */
function s20_shortcode_fallback( $message = '' ) {
	$behavior = s20_get_option( 'shortcode_empty_behavior', 'hide' );
	if ( 'message' !== $behavior ) {
		return '';
	}
	$message = $message ? $message : s20_get_option( 'shortcode_empty_message', __( 'Results will be available soon.', 'guru-bhai-khaiwal' ) );
	return '<p class="s20-shortcode-fallback">' . esc_html( $message ) . '</p>';
}

/* -------------------------------------------------------------------------
 * Tiny utility shortcodes for editors (not related to games/results).
 * ---------------------------------------------------------------------- */

/**
 * [s20_phone] - global phone number as a tel: link.
 */
function s20_sc_phone( $atts ) {
	$atts  = shortcode_atts( array( 'text' => '' ), $atts, 's20_phone' );
	$phone = s20_site_phone();
	if ( ! $phone ) {
		return '';
	}
	$text = $atts['text'] ? $atts['text'] : $phone;
	return '<a class="s20-phone-link" href="' . esc_url( s20_tel_href( $phone ) ) . '">' . esc_html( $text ) . '</a>';
}
add_shortcode( 's20_phone', 's20_sc_phone' );

/**
 * [s20_whatsapp text="WhatsApp Us"] - WhatsApp button using the global number.
 */
function s20_sc_whatsapp( $atts ) {
	$atts = shortcode_atts( array( 'text' => s20_get_option( 'site_button_text', 'WhatsApp Us' ), 'number' => '' ), $atts, 's20_whatsapp' );
	$href = s20_whatsapp_href( $atts['number'] ? $atts['number'] : s20_site_whatsapp() );
	if ( ! $href ) {
		return '';
	}
	return '<a class="s20-btn" href="' . esc_url( $href ) . '" target="_blank" rel="noopener nofollow">' . esc_html( $atts['text'] ) . '</a>';
}
add_shortcode( 's20_whatsapp', 's20_sc_whatsapp' );

/**
 * [s20_year] - current year (for copyright text).
 */
function s20_sc_year() {
	return esc_html( wp_date( 'Y' ) );
}
add_shortcode( 's20_year', 's20_sc_year' );
