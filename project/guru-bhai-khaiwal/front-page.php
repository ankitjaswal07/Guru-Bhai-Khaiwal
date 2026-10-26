<?php
/**
 * Front page: sections in the exact order of the live website.
 * Every section can be enabled/disabled and edited in Guru Bhai Khaiwal > Homepage.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();


$s20_default_sections = array(
	'hero'            => 'template-parts/hero/hero',
	'games'           => 'template-parts/games/active-games',
	'today_yesterday' => 'template-parts/results/today-yesterday',
	'promo'           => 'template-parts/promotional/promo-cards',
	'current_month'   => 'template-parts/results/current-month',
	'previous_month'  => 'template-parts/results/previous-month',
	'contact_direct'  => 'template-parts/contact/contact-direct',
	'contact_us'      => 'template-parts/contact/contact-us',
	'blog'            => 'template-parts/blog/latest-posts',
	'faq'             => 'template-parts/faq/faq-section',
);

$s20_front_page_id = get_queried_object_id();
$s20_section_order = s20_get_option( 'homepage_section_order', s20_default_homepage_section_order() );
$s20_page_sections = s20_get_field( 'page_sections', $s20_front_page_id, array() );
$s20_sections = array();
foreach ( (array) $s20_section_order as $s20_row ) {
	$s20_key = isset( $s20_row['section'] ) ? sanitize_key( $s20_row['section'] ) : '';
	$s20_is_custom = preg_match( '/^page_section_(\d+)$/', $s20_key, $s20_match );
	if ( $s20_is_custom ) {
		$s20_index = (int) $s20_match[1] - 1;
		if ( ! isset( $s20_page_sections[ $s20_index ] ) || ( isset( $s20_page_sections[ $s20_index ]['active'] ) && ! $s20_page_sections[ $s20_index ]['active'] ) ) {
			continue;
		}
	}
	if ( $s20_key && ( isset( $s20_default_sections[ $s20_key ] ) || $s20_is_custom ) && ! isset( $s20_sections[ $s20_key ] ) ) {
		$s20_sections[ $s20_key ] = isset( $s20_default_sections[ $s20_key ] ) ? $s20_default_sections[ $s20_key ] : '';
	}
}

if ( empty( $s20_section_order ) ) {
	$s20_sections = $s20_default_sections;
}

/**
 * Filter the homepage section order / list (child themes).
 */
$s20_sections = apply_filters( 's20_front_page_sections', $s20_sections );

foreach ( $s20_sections as $s20_key => $s20_part ) {
	if ( preg_match( '/^page_section_(\d+)$/', $s20_key, $s20_match ) ) {
		s20_render_page_sections( $s20_front_page_id, (int) $s20_match[1] );
		continue;
	}
	if ( ! s20_option_enabled( $s20_key . '_enabled', true ) ) {
		continue;
	}
	get_template_part( $s20_part );
}
get_footer();
