<?php
/**
 * Footer columns: brand + link columns (ACF repeater) + social.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_bg      = s20_image_url( s20_get_option( 'footer_background' ) );
$s20_tagline = s20_get_option( 'footer_tagline', '' );
$s20_desc    = s20_get_option( 'footer_description', '' );
$s20_columns = s20_get_repeater( 'footer_columns', s20_default_footer_columns() );
$s20_social  = s20_option_enabled( 'footer_show_social', true ) ? s20_get_repeater( 'social_links', array() ) : array();
?>
<div class="s20-footer-main"<?php echo $s20_bg ? ' style="background-image:url(' . esc_url( $s20_bg ) . ')"' : ''; ?>>
	<div class="s20-container s20-footer-grid">
		<div class="s20-footer-col s20-footer-brand">
			<a class="s20-footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php s20_logo( 'footer' ); ?></a>
			<?php if ( $s20_tagline ) : ?><p class="s20-footer-tagline"><?php s20_text( $s20_tagline ); ?></p><?php endif; ?>
			<?php if ( $s20_desc ) : ?><p class="s20-footer-desc"><?php s20_text( nl2br( $s20_desc ) ); ?></p><?php endif; ?>
			<?php if ( ! empty( $s20_social ) ) : ?>
				<ul class="s20-social">
					<?php foreach ( $s20_social as $s20_item ) : ?>
						<?php if ( empty( $s20_item['url'] ) ) { continue; } ?>
						<li>
							<a href="<?php echo esc_url( s20_link_href( $s20_item['url'] ) ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr( isset( $s20_item['label'] ) ? $s20_item['label'] : '' ); ?>">
								<?php if ( ! empty( $s20_item['custom_icon'] ) ) { s20_image( $s20_item['custom_icon'], 'thumbnail', array( 'class' => 's20-social-img' ) ); } else { s20_the_icon( isset( $s20_item['icon'] ) ? $s20_item['icon'] : 'chevron' ); } ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php
		$s20_index = 0;
		foreach ( $s20_columns as $s20_col ) :
			$s20_index++;
			$s20_links = isset( $s20_col['links'] ) ? s20_rows( $s20_col['links'] ) : array();
			$s20_use_menu = ( 1 === $s20_index && has_nav_menu( 'footer' ) );
			if ( empty( $s20_links ) && ! $s20_use_menu ) {
				continue;
			}
			?>
			<div class="s20-footer-col">
				<?php if ( ! empty( $s20_col['heading'] ) ) : ?><span class="s20-footer-heading"><?php s20_text( $s20_col['heading'] ); ?></span><?php endif; ?>
				<?php if ( $s20_use_menu ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 's20-footer-links', 'depth' => 1, 'fallback_cb' => false ) ); ?>
				<?php else : ?>
					<ul class="s20-footer-links">
						<?php foreach ( $s20_links as $s20_link ) : ?>
							<?php if ( empty( $s20_link['label'] ) ) { continue; } ?>
							<li>
								<?php if ( ! empty( $s20_link['url'] ) ) : ?>
									<a href="<?php echo esc_url( s20_link_href( $s20_link['url'] ) ); ?>"<?php echo ! empty( $s20_link['new_tab'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php s20_the_icon( 'chevron' ); ?><span><?php s20_text( $s20_link['label'] ); ?></span></a>
								<?php else : ?>
									<span><?php s20_the_icon( 'chevron' ); ?><span><?php s20_text( $s20_link['label'] ); ?></span></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php get_template_part( 'template-parts/footer/disclaimer' ); ?>
</div>
