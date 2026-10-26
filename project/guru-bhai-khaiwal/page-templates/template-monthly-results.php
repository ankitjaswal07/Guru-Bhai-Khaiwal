<?php
/** Template Name: Monthly Archive */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="container content-area"><?php gbk_breadcrumb(); while ( have_posts() ) : the_post(); ?><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); gbk_component( '[srm_monthly_results]' ); gbk_component( '[gbk_archive]' ); ?></div><?php endwhile; ?></div><?php get_footer(); ?>
