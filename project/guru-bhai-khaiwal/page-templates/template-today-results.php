<?php
/**
 * Template Name: Results: Today
 * Template Post Type: page
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/results/results-page',
		null,
		array(
			'class'  => 's20-tpl-today-results',
			'blocks' => array(
				array( 'page_field' => 'today_results_shortcode', 'option_field' => 'today_results_shortcode', 'heading_option' => 'results_heading_today' ),
			),
		)
	);
endwhile;

get_footer();
