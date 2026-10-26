<?php
/**
 * Template Name: Results: Yesterday
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
			'class'  => 's20-tpl-yesterday-results',
			'blocks' => array(
				array( 'page_field' => 'yesterday_results_shortcode', 'option_field' => 'yesterday_results_shortcode', 'heading_option' => 'results_heading_yesterday' ),
			),
		)
	);
endwhile;

get_footer();
