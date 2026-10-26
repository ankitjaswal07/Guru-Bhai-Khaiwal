<?php
/**
 * Blog posts index (Settings > Reading > Posts page).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="s20-section s20-page-section s20-archive">
	<div class="s20-container">
		<?php
		if ( is_home() && ! is_front_page() ) {
			s20_page_title( get_the_title( get_option( 'page_for_posts' ) ) );
		} elseif ( is_archive() ) {
			s20_page_title( get_the_archive_title(), get_the_archive_description() );
		} elseif ( is_search() ) {
			/* translators: %s: search query */
			s20_page_title( sprintf( esc_html__( 'Search results for "%s"', 'guru-bhai-khaiwal' ), get_search_query() ) );
		}
		?>
		<?php if ( have_posts() ) : ?>
			<div class="s20-blog-grid s20-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/blog/card', null, array( 'show_date' => true, 'show_category' => true ) );
				endwhile;
				?>
			</div>
			<?php s20_pagination(); ?>
		<?php else : ?>
			<p class="s20-empty"><?php esc_html_e( 'Nothing found.', 'guru-bhai-khaiwal' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	<?php if(is_home()&&($blog_page=(int)get_option('page_for_posts')))s20_render_page_sections($blog_page); ?>
	</div>
</section>
<?php
get_footer();
