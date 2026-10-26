<?php
/**
 * Announcement / top bar: disclaimer text + Telegram / WhatsApp / Login links.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_announce_on = s20_option_enabled( 'header_announcement_enabled', true );
$s20_announce    = s20_get_option( 'header_announcement', '' );
$s20_announce_url = s20_link_href( s20_get_option( 'header_announcement_url', '' ) );

$s20_links = array();
if ( s20_option_enabled( 'header_telegram_enabled', true ) ) {
	$s20_tg = s20_get_option( 'header_telegram_url', '' );
	$s20_tg = $s20_tg ? $s20_tg : s20_site_telegram();
	$s20_links[] = array( 'icon' => 'telegram', 'text' => s20_get_option( 'header_telegram_text', 'Join Telegram' ), 'url' => s20_link_href( $s20_tg ), 'class' => 's20-topbar-telegram' );
}
if ( s20_option_enabled( 'header_whatsapp_enabled', true ) ) {
	$s20_wa = s20_get_option( 'header_whatsapp_url', '' );
	$s20_wa = $s20_wa ? $s20_wa : s20_site_whatsapp();
	$s20_links[] = array( 'icon' => 'whatsapp', 'text' => s20_get_option( 'header_whatsapp_text', 'WhatsApp' ), 'url' => s20_whatsapp_href( $s20_wa ), 'class' => 's20-topbar-whatsapp' );
}
if ( s20_option_enabled( 'header_login_enabled', true ) ) {
	$s20_login = s20_get_option( 'header_login_url', '' );
	if ( ! $s20_login ) {
		$s20_plugin = trim( (string) s20_get_option( 'plugin_admin_url', '' ) );
		$s20_login  = $s20_plugin ? admin_url( ltrim( $s20_plugin, '/' ) ) : wp_login_url();
	}
	$s20_links[] = array( 'icon' => 'user', 'text' => s20_get_option( 'header_login_text', 'Login' ), 'url' => s20_link_href( $s20_login ), 'class' => 's20-topbar-login' );
}
if ( s20_option_enabled( 'social_show_header', false ) ) {
	foreach ( s20_get_repeater( 'social_links', array() ) as $s20_social ) {
		if ( empty( $s20_social['url'] ) ) {
			continue;
		}
		$s20_links[] = array( 'icon' => isset( $s20_social['icon'] ) ? $s20_social['icon'] : 'none', 'text' => isset( $s20_social['label'] ) ? $s20_social['label'] : '', 'url' => s20_link_href( $s20_social['url'] ), 'class' => 's20-topbar-social' );
	}
}

if ( ! $s20_announce_on || ( '' === trim( (string) $s20_announce ) && empty( $s20_links ) ) ) {
	return;
}
?>
<div class="s20-topbar">
	<div class="s20-container s20-topbar-inner">
		<?php if ( '' !== trim( (string) $s20_announce ) ) : ?>
			<div class="s20-topbar-text">
				<?php if ( $s20_announce_url ) : ?>
					<a href="<?php echo esc_url( $s20_announce_url ); ?>"><?php s20_text( $s20_announce ); ?></a>
				<?php else : ?>
					<span><?php s20_text( $s20_announce ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $s20_links ) ) : ?>
			<ul class="s20-topbar-links">
				<?php foreach ( $s20_links as $s20_link ) : ?>
					<li class="<?php echo esc_attr( $s20_link['class'] ); ?>">
						<?php if ( $s20_link['url'] ) : ?>
							<a href="<?php echo esc_url( $s20_link['url'] ); ?>"<?php echo ( 0 === strpos( $s20_link['url'], 'http' ) && false === strpos( $s20_link['url'], home_url() ) ) ? ' target="_blank" rel="noopener nofollow"' : ''; ?>>
								<?php s20_the_icon( $s20_link['icon'] ); ?><span><?php s20_text( $s20_link['text'] ); ?></span>
							</a>
						<?php else : ?>
							<span class="s20-topbar-static"><?php s20_the_icon( $s20_link['icon'] ); ?><span><?php s20_text( $s20_link['text'] ); ?></span></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
