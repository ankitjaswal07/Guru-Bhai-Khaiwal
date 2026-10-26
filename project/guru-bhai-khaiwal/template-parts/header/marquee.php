<?php
/**
 * Scrolling text bar under the header.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

if ( ! s20_option_enabled( 'header_marquee_enabled', true ) ) {
	return;
}
$s20_marquee = s20_get_option( 'header_marquee_text', '' );
if ( '' === trim( (string) $s20_marquee ) ) {
	return;
}
$s20_scroll = s20_option_enabled( 'header_marquee_scroll', true );
$s20_speed  = max( 4, (int) s20_get_option( 'header_marquee_speed', 18 ) );
?>
<div class="s20-marquee<?php echo $s20_scroll ? ' s20-marquee--scroll' : ''; ?>" role="marquee" style="--s20-marquee-speed:<?php echo esc_attr( $s20_speed ); ?>s">
	<div class="s20-marquee-track">
		<span class="s20-marquee-text"><?php s20_text( $s20_marquee ); ?></span>
		<?php if ( $s20_scroll ) : ?><span class="s20-marquee-text" aria-hidden="true"><?php s20_text( $s20_marquee ); ?></span><?php endif; ?>
	</div>
</div>
