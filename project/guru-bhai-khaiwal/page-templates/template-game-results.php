<?php
/** Template Name: Single Game History */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="container content-area"><?php gbk_breadcrumb(); while ( have_posts() ) : the_post(); ?><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); $id = absint( get_post_meta( get_the_ID(), '_gbk_game_id', true ) ); if ( $id ) { gbk_component( '[srm_game_history game_id="' . $id . '"]' ); } else { echo '<p>Select a game in the page editor to show its history.</p>'; } ?></div><?php endwhile; ?></div><?php get_footer(); ?>
