<?php
/**
 * Template Name: Results: Single Game Chart
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
			'class'  => 's20-tpl-game-results',
			'blocks' => array(
				array( 'page_field' => 'result_chart_shortcode', 'option_field' => '', 'heading' => '' ),
			),
		)
	);
endwhile;

get_footer();
