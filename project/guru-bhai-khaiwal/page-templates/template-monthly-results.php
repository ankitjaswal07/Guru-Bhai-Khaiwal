<?php
/**
 * Template Name: Results: Monthly (Current + Previous)
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
			'class'  => 's20-tpl-monthly-results',
			'blocks' => array(
				array( 'page_field' => 'current_month_results_shortcode', 'option_field' => 'current_month_results_shortcode', 'heading_option' => 'results_heading_current_month' ),
				array( 'page_field' => 'previous_month_results_shortcode', 'option_field' => 'previous_month_results_shortcode', 'heading_option' => 'results_heading_previous_month' ),
			),
		)
	);
endwhile;

get_footer();
