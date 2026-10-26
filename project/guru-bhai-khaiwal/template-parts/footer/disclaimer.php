<?php
/**
 * Footer disclaimer bar (icon box | text | 18+ badge).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

if ( ! s20_option_enabled( 'footer_disclaimer_enabled', true ) ) {
	return;
}
$s20_title = s20_get_option( 'footer_disclaimer_title', '' );
$s20_text  = s20_get_option( 'footer_disclaimer_text', '' );
$s20_badge = s20_get_option( 'footer_age_badge', '' );
$s20_badge_text = s20_get_option( 'footer_age_text', '' );
$s20_icon  = s20_get_option( 'footer_disclaimer_icon', 'warning' );
if ( '' === trim( $s20_title . $s20_text . $s20_badge ) ) {
	return;
}
?>
<div class="s20-footer-disclaimer-wrap">
	<div class="s20-container">
		<div class="s20-footer-disclaimer">
			<?php if ( $s20_title ) : ?>
				<div class="s20-disclaimer-title">
					<?php if ( $s20_icon && 'none' !== $s20_icon ) { s20_the_icon( $s20_icon ); } ?>
					<h3><?php s20_text( $s20_title ); ?></h3>
				</div>
			<?php endif; ?>
			<?php if ( $s20_text ) : ?><p class="s20-disclaimer-text"><?php s20_text( nl2br( $s20_text ) ); ?></p><?php endif; ?>
			<?php if ( $s20_badge || $s20_badge_text ) : ?>
				<div class="s20-disclaimer-age">
					<?php if ( $s20_badge ) : ?><p class="s20-age-badge"><?php s20_text( $s20_badge ); ?></p><?php endif; ?>
					<?php if ( $s20_badge_text ) : ?><p class="s20-age-text"><?php s20_text( $s20_badge_text ); ?></p><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
