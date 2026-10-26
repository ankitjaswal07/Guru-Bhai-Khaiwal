<?php
/**
 * Minimal inline SVG icon set (no icon font libraries are loaded).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Available icon choices for ACF select fields.
 */
function s20_icon_choices( $include_none = true ) {
	$choices = array(
		'megaphone' => __( 'Megaphone', 'guru-bhai-khaiwal' ),
		'bell'      => __( 'Bell', 'guru-bhai-khaiwal' ),
		'whatsapp'  => __( 'WhatsApp', 'guru-bhai-khaiwal' ),
		'telegram'  => __( 'Telegram', 'guru-bhai-khaiwal' ),
		'phone'     => __( 'Phone', 'guru-bhai-khaiwal' ),
		'mail'      => __( 'Email', 'guru-bhai-khaiwal' ),
		'crown'     => __( 'Crown', 'guru-bhai-khaiwal' ),
		'star'      => __( 'Star', 'guru-bhai-khaiwal' ),
		'check'     => __( 'Check', 'guru-bhai-khaiwal' ),
		'warning'   => __( 'Warning', 'guru-bhai-khaiwal' ),
		'calendar'  => __( 'Calendar', 'guru-bhai-khaiwal' ),
		'clock'     => __( 'Clock', 'guru-bhai-khaiwal' ),
		'location'  => __( 'Location', 'guru-bhai-khaiwal' ),
		'user'      => __( 'User', 'guru-bhai-khaiwal' ),
		'facebook'  => __( 'Facebook', 'guru-bhai-khaiwal' ),
		'instagram' => __( 'Instagram', 'guru-bhai-khaiwal' ),
		'youtube'   => __( 'YouTube', 'guru-bhai-khaiwal' ),
		'twitter'   => __( 'X / Twitter', 'guru-bhai-khaiwal' ),
		'chevron'   => __( 'Chevron', 'guru-bhai-khaiwal' ),
	);
	if ( $include_none ) {
		$choices = array( 'none' => __( 'None', 'guru-bhai-khaiwal' ) ) + $choices;
	}
	return $choices;
}

/**
 * Return an inline SVG for the given icon name.
 *
 * @param string $name  Icon key.
 * @param string $class Extra classes.
 * @return string Safe SVG markup ('' if unknown).
 */
function s20_icon( $name, $class = '' ) {
	$paths = array(
		'telegram'  => array( '0 0 448 512', 'M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z' ),
		'whatsapp'  => array( '0 0 448 512', 'M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z' ),
		'user'      => array( '0 0 496 512', 'M248 104c-53 0-96 43-96 96s43 96 96 96 96-43 96-96-43-96-96-96zm0 144c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm0-240C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 448c-49.7 0-95.1-18.3-130.1-48.4 14.9-23 40.4-38.6 69.6-39.5 20.8 6.4 40.6 9.6 60.5 9.6s39.7-3.1 60.5-9.6c29.2 1 54.7 16.5 69.6 39.5-35 30.1-80.4 48.4-130.1 48.4zm162.7-84.1c-24.4-31.4-62.1-51.9-105.1-51.9-10.2 0-26 9.6-57.6 9.6-31.5 0-47.4-9.6-57.6-9.6-42.9 0-80.6 20.5-105.1 51.9C61.9 339.2 48 299.2 48 256c0-110.3 89.7-200 200-200s200 89.7 200 200c0 43.2-13.9 83.2-37.3 115.9z' ),
		'calendar'  => array( '0 0 24 24', 'M7 2h2v2h6V2h2v2h3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3V2zm12 8H5v10h14V10zM5 6v2h14V6H5zm2 6h3v3H7v-3zm4 0h3v3h-3v-3zm4 0h3v3h-3v-3z' ),
		'clock'     => array( '0 0 24 24', 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm.5-13H11v6l5.2 3.1.8-1.2-4.5-2.7V7z' ),
		'megaphone' => array( '0 0 24 24', 'M14 3v18l-6-4H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h4l6-4zm2 5.1a4 4 0 0 1 0 7.8v-1.6a2.5 2.5 0 0 0 0-4.6V8.1zm0-3.6a8 8 0 0 1 0 15v-1.6a6.5 6.5 0 0 0 0-11.8V4.5z' ),
		'bell'      => array( '0 0 24 24', 'M12 2a6 6 0 0 0-6 6v3.6L4 15v2h16v-2l-2-3.4V8a6 6 0 0 0-6-6zm0 20a3 3 0 0 0 3-3H9a3 3 0 0 0 3 3z' ),
		'phone'     => array( '0 0 24 24', 'M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.6 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.6 3.6a1 1 0 0 1-.25 1L6.6 10.8z' ),
		'mail'      => array( '0 0 24 24', 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1 3.2V17h16V8.2l-8 5-8-5zM4.4 7l7.6 4.7L19.6 7H4.4z' ),
		'crown'     => array( '0 0 24 24', 'M3 8l4.5 4L12 5l4.5 7L21 8l-2 11H5L3 8zm2 13h14v1.5H5V21z' ),
		'star'      => array( '0 0 24 24', 'M12 2l3 6.6 7 .8-5.2 4.8 1.4 7L12 17.7 5.8 21.2l1.4-7L2 9.4l7-.8L12 2z' ),
		'check'     => array( '0 0 24 24', 'M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z' ),
		'warning'   => array( '0 0 24 24', 'M12 2 1 21h22L12 2zm0 4 7.5 13h-15L12 6zm-1 5v4h2v-4h-2zm0 5v2h2v-2h-2z' ),
		'location'  => array( '0 0 24 24', 'M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z' ),
		'facebook'  => array( '0 0 24 24', 'M13.5 22v-8h2.7l.4-3.2h-3.1V8.8c0-.9.3-1.6 1.6-1.6h1.7V4.3c-.3 0-1.3-.1-2.5-.1-2.5 0-4.1 1.5-4.1 4.2v2.4H7.4V14h2.8v8h3.3z' ),
		'instagram' => array( '0 0 24 24', 'M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zM17.3 5.5a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4zM21 8.3c-.1-1.7-.5-3.2-1.7-4.4C18.1 2.7 16.6 2.3 14.9 2.2 13 2.1 11 2.1 9.1 2.2 7.4 2.3 5.9 2.7 4.7 3.9 3.5 5.1 3.1 6.6 3 8.3c-.1 1.9-.1 3.9 0 5.8.1 1.7.5 3.2 1.7 4.4 1.2 1.2 2.7 1.6 4.4 1.7 1.9.1 3.9.1 5.8 0 1.7-.1 3.2-.5 4.4-1.7 1.2-1.2 1.6-2.7 1.7-4.4.1-1.9.1-3.9 0-5.8zm-2 6.9c-.2 1-.6 1.8-1.4 2.5s-1.5 1.1-2.5 1.3c-1.7.2-6.5.2-8.2 0-1-.2-1.8-.6-2.5-1.3s-1.1-1.5-1.3-2.5c-.2-1.7-.2-6.5 0-8.2.2-1 .6-1.8 1.3-2.5s1.5-1.1 2.5-1.3c1.7-.2 6.5-.2 8.2 0 1 .2 1.8.6 2.5 1.3s1.1 1.5 1.4 2.5c.2 1.7.2 6.5 0 8.2z' ),
		'youtube'   => array( '0 0 24 24', 'M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8zM10 15V9l5.2 3L10 15z' ),
		'twitter'   => array( '0 0 24 24', 'M17.5 3h3l-6.8 7.8L21.7 21h-6.2l-4.9-6.4L5 21H2l7.3-8.3L1.6 3h6.4l4.4 5.8L17.5 3zm-1 16.2h1.7L7 4.7H5.2l11.3 14.5z' ),
		'chevron'   => array( '0 0 24 24', 'M9 6l6 6-6 6-1.4-1.4L12.2 12 7.6 7.4 9 6z' ),
		'menu'      => array( '0 0 24 24', 'M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z' ),
		'close'     => array( '0 0 24 24', 'M18.3 5.7 12 12l6.3 6.3-1.4 1.4L12 13.4l-6.3 6.3-1.4-1.4L10.6 12 4.3 5.7l1.4-1.4L12 10.6l6.3-6.3 1.4 1.4z' ),
		'arrow-left'  => array( '0 0 24 24', 'M15.4 6.4 9.8 12l5.6 5.6L14 19l-7-7 7-7 1.4 1.4z' ),
		'arrow-right' => array( '0 0 24 24', 'M8.6 6.4 14.2 12l-5.6 5.6L10 19l7-7-7-7-1.4 1.4z' ),
		'plus'      => array( '0 0 24 24', 'M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z' ),
		'minus'     => array( '0 0 24 24', 'M5 11h14v2H5z' ),
	);

	$name = sanitize_key( $name );
	if ( empty( $paths[ $name ] ) ) {
		return '';
	}
	list( $viewbox, $d ) = $paths[ $name ];
	return sprintf(
		'<svg class="s20-icon s20-icon-%1$s %2$s" viewBox="%3$s" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path d="%4$s"/></svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		esc_attr( $viewbox ),
		esc_attr( $d )
	);
}

/**
 * Echo an icon. Safe: markup is generated from a fixed whitelist.
 */
function s20_the_icon( $name, $class = '' ) {
	echo s20_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
