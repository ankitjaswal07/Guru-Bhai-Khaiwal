<?php
/**
 * Current month results (plugin shortcode).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_shortcode = s20_get_option( 'current_month_shortcode', '' );
if ( '' === trim( (string) $s20_shortcode ) ) {
	$s20_shortcode = s20_get_option( 'current_month_results_shortcode', '' );
}
s20_shortcode_section(
	array(
		'id'          => 's20-current-month',
		'class'       => 's20-current-month',
		'heading'     => s20_get_option( 'current_month_heading', '' ),
		'description' => s20_get_option( 'current_month_description', '' ),
		'shortcode'   => $s20_shortcode,
	)
);
