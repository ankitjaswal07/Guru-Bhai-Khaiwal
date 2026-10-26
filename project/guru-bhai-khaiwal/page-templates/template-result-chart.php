<?php
/**
 * Template Name: Results: Result Chart
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
			'class'  => 's20-tpl-result-chart',
			'blocks' => array(
				array( 'page_field' => 'result_chart_shortcode', 'option_field' => 'result_chart_shortcode', 'heading_option' => 'results_heading_result_chart' ),
			),
		)
	);
endwhile;

get_footer();
