<?php
/**
 * Blog helpers (normal WordPress posts - no custom post type).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Query for the homepage "Latest updates" section.
 */
function s20_latest_posts_query() {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, (int) s20_get_option( 'blog_posts_count', 6 ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	$cat = (int) s20_get_option( 'blog_category', 0 );
	if ( $cat ) {
		$args['cat'] = $cat;
	}
	return new WP_Query( apply_filters( 's20_latest_posts_args', $args ) );
}

/**
 * Related posts for single view (same category first).
 */
function s20_related_posts_query( $post_id, $count = 3 ) {
	$cats = wp_get_post_categories( $post_id );
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $count,
		'post__not_in'        => array( $post_id ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( ! empty( $cats ) ) {
		$args['category__in'] = $cats;
	}
	return new WP_Query( $args );
}
