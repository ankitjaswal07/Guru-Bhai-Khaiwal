<?php
/**
 * Template Name: FAQ Page
 * Template Post Type: page
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php s20_page_title( s20_get_option( 'faq_heading', get_the_title() ) ); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 's20-section s20-page-section s20-faq-page' ); ?>>
		<div class="s20-container">
			<p style="text-align: center;margin-bottom: 40px; font-size:18px;"><?php s20_text(s20_get_option( 'faq_description', '' )); ?></p>
			<?php get_template_part( 'template-parts/faq/faq-list' ); ?>
			<?php s20_page_notice(); ?>
		</div>
	<?php s20_render_page_sections(get_the_ID()); ?>
	</article>
	<?php
endwhile;

get_footer();
