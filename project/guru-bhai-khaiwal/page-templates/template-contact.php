<?php
/** Template Name: Contact Information */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="container content-area"><?php gbk_breadcrumb(); while ( have_posts() ) : the_post(); ?><h1><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); echo '<p>' . esc_html( gbk_option( 'contact_text' ) ) . '</p>'; gbk_contact_links(); ?></div><?php endwhile; ?></div><?php get_footer(); ?>
