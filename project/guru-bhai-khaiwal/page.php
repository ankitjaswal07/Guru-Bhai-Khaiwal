<?php
/**
 * Default page template (About, Privacy Policy, Disclaimer, Terms, ...).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
				<?php if ( s20_get_field( 'page_show_title', get_the_ID(), 1 ) ) { s20_page_title( get_the_title() ); } ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 's20-section s20-page-section' ); ?>>
		<div class="s20-container s20-container--narrow">

			<?php if ( has_post_thumbnail() ) : ?><figure class="s20-page-thumb"><?php the_post_thumbnail( 'large' ); ?></figure><?php endif; ?>
			<div class="s20-entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<nav class="s20-page-links">', 'after' => '</nav>' ) ); ?>
			</div>
			<?php s20_render_shortcode( s20_get_field( 'page_content_shortcode', get_the_ID(), '' ), 's20-page-shortcode' ); ?>
			<?php s20_render_page_sections( get_the_ID() ); ?>
			<?php s20_page_notice(); ?>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;

get_footer();
