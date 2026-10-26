<?php
/**
 * Single blog post.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<header class="s20-page-header">
				<?php s20_post_meta( true, true ); ?>
				<h1 class="s20-section-title s20-page-title"><?php the_title(); ?></h1>
			</header>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 's20-section s20-page-section s20-single' ); ?>>
		<div class="s20-container s20-container--narrow">

			<?php if ( has_post_thumbnail() ) : ?><figure class="s20-single-thumb"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure><?php endif; ?>
			<div class="s20-entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<nav class="s20-page-links">', 'after' => '</nav>' ) ); ?>
			</div>
			<?php the_tags( '<div class="s20-tags">', ' ', '</div>' ); ?>
			<nav class="s20-post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'guru-bhai-khaiwal' ); ?>">
				<div class="s20-post-nav-prev"><?php previous_post_link( '%link', s20_icon( 'arrow-left' ) . ' %title' ); ?></div>
				<div class="s20-post-nav-next"><?php next_post_link( '%link', '%title ' . s20_icon( 'arrow-right' ) ); ?></div>
			</nav>
		</div>
	</article>
	<?php
	$s20_related = s20_related_posts_query( get_the_ID(), 3 );
	if ( $s20_related->have_posts() ) :
		?>
		<section class="s20-section s20-related">
			<div class="s20-container">
				<h2 class="s20-section-title"><?php esc_html_e( 'Related Updates', 'guru-bhai-khaiwal' ); ?></h2>
				<div class="s20-blog-grid s20-cols-3">
					<?php
					while ( $s20_related->have_posts() ) :
						$s20_related->the_post();
						get_template_part( 'template-parts/blog/card', null, array( 'show_date' => true ) );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="s20-section s20-comments"><div class="s20-container s20-container--narrow">';
		comments_template();
		echo '</div></div>';
	}
endwhile;

get_footer();
