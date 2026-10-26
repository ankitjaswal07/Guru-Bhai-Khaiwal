<?php
/** Template Name: Informational Articles */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="container content-area"><?php gbk_breadcrumb(); while ( have_posts() ) : the_post(); ?><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); ?></div><?php endwhile;
$current = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$articles = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => max( 1, min( 30, (int) get_option( 'posts_per_page', 10 ) ) ), 'paged' => $current, 'ignore_sticky_posts' => true ) );
if ( $articles->have_posts() ) {
    echo '<div class="post-grid">';
    while ( $articles->have_posts() ) { $articles->the_post(); get_template_part( 'template-parts/post-card' ); }
    echo '</div><nav class="pagination" aria-label="Article pages">' . wp_kses_post( (string) paginate_links( array( 'total' => $articles->max_num_pages, 'current' => $current ) ) ) . '</nav>';
} else { echo '<p>No informational articles have been published yet.</p>'; }
wp_reset_postdata(); ?></div><?php get_footer(); ?>
