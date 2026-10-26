<?php
/**
 * Active games section (plugin shortcode) + payment block.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_heading    = s20_get_option( 'games_heading', '' );
$s20_subheading = s20_get_option( 'games_subheading', '' );
$s20_timing     = s20_get_option( 'games_timing_heading', '' );
$s20_desc       = s20_get_option( 'games_description', '' );
$s20_games_sc   = s20_get_option( 'home_games_shortcode', '' );
if ( '' === trim( (string) $s20_games_sc ) ) {
	$s20_games_sc = s20_get_option( 'games_shortcode', '' );
}
$s20_games_html = s20_shortcode_output( $s20_games_sc );
if ( '' === $s20_games_html ) {
	$s20_games_html = s20_shortcode_fallback();
}
$s20_show_payment = s20_option_enabled( 'games_show_payment', true ) && s20_option_enabled( 'payment_enabled', true );

if ( '' === $s20_games_html && ! $s20_show_payment && '' === trim( $s20_heading . $s20_subheading ) ) {
	return;
}
?>
<section class="s20-section s20-games" id="s20-games">
	<div class="s20-container s20-games-inner">
		<?php if ( $s20_heading ) : ?><h2 class="s20-games-heading"><?php s20_text( $s20_heading ); ?></h2><?php endif; ?>
		<?php if ( $s20_subheading ) : ?><p class="s20-games-subheading"><?php s20_text( $s20_subheading ); ?></p><?php endif; ?>
		<?php if ( '' !== trim( (string) $s20_desc ) ) : ?><div class="s20-games-desc"><?php s20_rich_text( $s20_desc ); ?></div><?php endif; ?>
		<?php if ( '' !== $s20_games_html ) : ?>
			<?php if ( $s20_timing ) : ?><h3 class="s20-games-timing"><?php s20_text( $s20_timing ); ?></h3><?php endif; ?>
			<div class="s20-shortcode s20-table-scroll s20-games-table"><?php echo $s20_games_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output. ?></div>
		<?php endif; ?>
		<?php if ( $s20_show_payment ) { get_template_part( 'template-parts/payment/payment' ); } ?>
	</div>
</section>
