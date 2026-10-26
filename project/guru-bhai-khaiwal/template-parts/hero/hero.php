<?php
/**
 * Hero: dark card with strip heading, live clock, announcement pill and the
 * plugin's Current / Next game panel (shortcode).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_heading   = s20_get_option( 'hero_heading', '' );
$s20_strip_img = s20_get_option( 'hero_strip_image' );
$s20_desc      = s20_get_option( 'hero_description', '' );
$s20_image     = s20_get_option( 'hero_image' );
$s20_clock     = s20_option_enabled( 'hero_show_clock', true );
$s20_pill_on   = s20_option_enabled( 'hero_pill_enabled', true );
$s20_pill_text = s20_get_option( 'hero_pill_text', '' );
$s20_pill_icon = s20_get_option( 'hero_pill_icon', 'megaphone' );
$s20_pill_img  = s20_get_option( 'hero_pill_icon_image' );
$s20_pill_url  = s20_link_href( s20_get_option( 'hero_pill_url', '' ) );
$s20_btn_text  = s20_get_option( 'hero_button_text', '' );
$s20_btn_url   = s20_link_href( s20_get_option( 'hero_button_url', '' ) );
$s20_btn2_text = s20_get_option( 'hero_secondary_button_text', '' );
$s20_btn2_url  = s20_link_href( s20_get_option( 'hero_secondary_button_url', '' ) );

$s20_live_html = '';
if ( s20_option_enabled( 'hero_live_status_enabled', true ) ) {
	$s20_live_html = s20_shortcode_output( s20_resolve_shortcode( '', 'hero_live_status_shortcode' ) );
	if ( '' === $s20_live_html ) {
		$s20_live_html = s20_shortcode_output( s20_get_option( 'live_status_shortcode', '' ) );
	}
	if ( '' === $s20_live_html ) {
		$s20_live_html = s20_shortcode_fallback();
	}
}
?>
<section class="s20-section s20-hero" id="s20-hero">
	<div class="s20-container">
		<div class="s20-dark-card s20-hero-card">
			<?php s20_strip_heading( $s20_heading, 'h1', $s20_strip_img, 's20-hero-strip' ); ?>

			<?php if ( $s20_clock ) : ?>
				<h2 class="s20-clock-box"><span class="s20-live-clock" id="s20-current-datetime" data-s20-clock><?php echo esc_html( wp_date( 'j/n/Y H:i:s' ) ); ?></span></h2>
			<?php endif; ?>

			<?php if ( ! empty( $s20_image ) ) : ?>
				<div class="s20-hero-image"><?php s20_image( $s20_image, 'large', array( 'class' => 's20-hero-img', 'loading' => 'eager' ) ); ?></div>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $s20_desc ) ) : ?>
				<div class="s20-hero-desc"><?php s20_rich_text( $s20_desc ); ?></div>
			<?php endif; ?>

			<?php if ( $s20_pill_on && '' !== trim( (string) $s20_pill_text ) ) : ?>
				<?php $s20_pill_tag = $s20_pill_url ? 'a' : 'div'; ?>
				<<?php echo $s20_pill_tag; // phpcs:ignore ?> class="s20-pill"<?php echo $s20_pill_url ? ' href="' . esc_url( $s20_pill_url ) . '"' : ''; ?>>
					<?php if ( ! empty( $s20_pill_img ) ) : ?>
						<span class="s20-pill-icon"><?php s20_image( $s20_pill_img, 'thumbnail', array( 'class' => 's20-pill-img' ) ); ?></span>
					<?php elseif ( $s20_pill_icon && 'none' !== $s20_pill_icon ) : ?>
						<span class="s20-pill-icon"><?php s20_the_icon( $s20_pill_icon ); ?></span>
					<?php endif; ?>
					<span class="s20-pill-text"><?php s20_text( $s20_pill_text ); ?></span>
				</<?php echo $s20_pill_tag; // phpcs:ignore ?>>
			<?php endif; ?>

			<?php if ( $s20_btn_text || $s20_btn2_text ) : ?>
				<div class="s20-hero-actions">
					<?php s20_button( $s20_btn_text, $s20_btn_url ); ?>
					<?php s20_button( $s20_btn2_text, $s20_btn2_url, array( 'class' => 's20-btn--outline' ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $s20_live_html ) : ?>
				<div class="s20-live-status s20-shortcode"><?php echo $s20_live_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output. ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
