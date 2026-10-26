<?php
/**
 * Blog card (used by the homepage carousel, archives and related posts).
 *
 * @param array $args { show_image, show_date, show_excerpt, show_category }
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( isset( $args ) ? $args : array(), array( 'show_image' => true, 'show_date' => false, 'show_excerpt' => true, 'show_category' => false ) );
?>
<article <?php post_class( 's20-post-card' ); ?>>
	<?php if ( $args['show_image'] ) : ?>
		<a class="s20-post-card-media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 's20-card', array( 'class' => 's20-post-card-img', 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<span class="s20-post-card-img s20-post-card-img--empty"><?php s20_the_icon( 'star' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>
	<div class="s20-post-card-body">
		<?php s20_post_meta( $args['show_date'], $args['show_category'] ); ?>
		<h5 class="s20-post-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
		<?php if ( $args['show_excerpt'] ) : ?><p class="s20-post-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
	</div>
</article>
