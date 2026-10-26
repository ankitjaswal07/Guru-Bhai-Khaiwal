<?php
/** Template Name: Today Records */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="container content-area"><?php gbk_breadcrumb(); while ( have_posts() ) : the_post(); ?><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); gbk_component( '[srm_today_results]' ); ?></div><?php endwhile; ?></div><?php get_footer(); ?>
