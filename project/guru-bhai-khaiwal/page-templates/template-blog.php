<?php
/**
 * Template Name: Blog: All Posts
 * Template Post Type: page
 *
 * Blog page showing all published posts.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php s20_page_title( get_the_title() ); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 's20-section s20-page-section s20-archive' ); ?>>
		<div class="s20-container">

			<?php if ( get_the_content() ) : ?><div class="s20-entry-content"><?php the_content(); ?></div><?php endif; ?>

			<?php
			$s20_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
			$s20_posts = new WP_Query(
				array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => max( 1, (int) get_option( 'posts_per_page', 10 ) ),
					'paged'               => $s20_paged,
					'ignore_sticky_posts' => false,
				)
			);
			?>

			<?php if ( $s20_posts->have_posts() ) : ?>
				<div class="s20-blog-grid s20-cols-3">
					<?php
					while ( $s20_posts->have_posts() ) :
						$s20_posts->the_post();
						get_template_part( 'template-parts/blog/card', null, array( 'show_date' => true, 'show_category' => true ) );
					endwhile;
					?>
				</div>

				<?php if ( $s20_posts->max_num_pages > 1 ) : ?>
					<nav class="s20-pagination" aria-label="<?php esc_attr_e( 'Blog pages', 'guru-bhai-khaiwal' ); ?>">
						<div class="nav-links">
							<?php
							echo wp_kses_post(
								paginate_links(
									array(
										'base'               => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
										'current'            => $s20_paged,
										'total'              => $s20_posts->max_num_pages,
										'mid_size'           => 1,
										'prev_text'          => s20_icon( 'arrow-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'guru-bhai-khaiwal' ) . '</span>',
										'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next', 'guru-bhai-khaiwal' ) . '</span>' . s20_icon( 'arrow-right' ),
										'type'               => 'plain',
										'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'guru-bhai-khaiwal' ) . ' </span>',
									)
								)
							);
							?>
						</div>
					</nav>
				<?php endif; ?>
			<?php else : ?>
				<p class="s20-empty"><?php esc_html_e( 'No blog posts found.', 'guru-bhai-khaiwal' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	<?php s20_render_page_sections(get_the_ID()); ?>
	</article>
	<?php
endwhile;

get_footer();
