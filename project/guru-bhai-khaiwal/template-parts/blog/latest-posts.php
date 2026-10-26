<?php
/**
 * Homepage "Latest updates" (normal WordPress posts).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_query = s20_latest_posts_query();
if ( ! $s20_query->have_posts() ) {
	return;
}
$s20_layout  = s20_get_option( 'blog_layout', 'carousel' );
$s20_columns = (int) s20_get_option( 'blog_columns', 4 );
$s20_columns = in_array( $s20_columns, array( 2, 3, 4 ), true ) ? $s20_columns : 4;
$s20_card    = array(
	'show_image'    => s20_option_enabled( 'blog_show_image', true ),
	'show_date'     => s20_option_enabled( 'blog_show_date', false ),
	'show_excerpt'  => s20_option_enabled( 'blog_show_excerpt', true ),
	'show_category' => s20_option_enabled( 'blog_show_category', false ),
);
$s20_btn     = s20_get_option( 'blog_button_text', '' );
$s20_btn_url = s20_get_option( 'blog_button_url', '' );
$s20_btn_url = $s20_btn_url ? s20_link_href( $s20_btn_url ) : get_permalink( get_option( 'page_for_posts' ) );
?>
<section class="s20-section s20-blog-section" id="s20-blog">
	<div class="s20-container">
		<?php s20_section_heading( s20_get_option( 'blog_heading', '' ), s20_get_option( 'blog_description', '' ) ); ?>
		<div class="s20-blog-<?php echo esc_attr( $s20_layout ); ?> s20-cols-<?php echo (int) $s20_columns; ?>" data-s20-carousel="<?php echo 'carousel' === $s20_layout ? '1' : '0'; ?>">
			<?php if ( 'carousel' === $s20_layout ) : ?>
				<button class="s20-carousel-arrow s20-carousel-prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'guru-bhai-khaiwal' ); ?>"><?php s20_the_icon( 'arrow-left' ); ?></button>
				<button class="s20-carousel-arrow s20-carousel-next" type="button" aria-label="<?php esc_attr_e( 'Next', 'guru-bhai-khaiwal' ); ?>"><?php s20_the_icon( 'arrow-right' ); ?></button>
			<?php endif; ?>
			<div class="s20-blog-track" role="list">
				<?php
				while ( $s20_query->have_posts() ) :
					$s20_query->the_post();
					echo '<div class="s20-blog-slide" role="listitem">';
					get_template_part( 'template-parts/blog/card', null, $s20_card );
					echo '</div>';
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<?php if ( 'carousel' === $s20_layout ) : ?><div class="s20-carousel-dots" role="tablist"></div><?php endif; ?>
		</div>
		<?php if ( $s20_btn ) : ?><div class="s20-section-action"><?php s20_button( $s20_btn, $s20_btn_url ); ?></div><?php endif; ?>
	</div>
</section>
