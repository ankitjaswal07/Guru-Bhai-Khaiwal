<?php
/**
 * Main header: logo | primary navigation | date badge.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_logo_url   = s20_get_option( 'site_logo_url', '' );
$s20_logo_url   = $s20_logo_url ? s20_link_href( $s20_logo_url ) : home_url( '/' );
$s20_logo_width = (int) s20_get_option( 'site_logo_width', 160 );
$s20_date_on    = s20_option_enabled( 'header_date_enabled', true );
$s20_date_icon  = s20_get_option( 'header_date_icon', 'calendar' );
$s20_date_url   = s20_link_href( s20_get_option( 'header_date_url', '' ) );
$s20_sticky     = s20_option_enabled( 'header_sticky', false );
?>
<header class="s20-header<?php echo $s20_sticky ? ' s20-header--sticky' : ''; ?>" id="s20-header">
	<div class="s20-container s20-header-inner">
		<div class="s20-header-brand">
			<a class="s20-logo" href="<?php echo esc_url( $s20_logo_url ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="max-width:<?php echo esc_attr( $s20_logo_width ? $s20_logo_width : 160 ); ?>px">
				<?php s20_logo( 'header' ); ?>
			</a>
		</div>

		<nav class="s20-navigation" id="s20-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'guru-bhai-khaiwal' ); ?>">
			<button class="s20-menu-toggle" type="button" aria-controls="s20-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu Toggle', 'guru-bhai-khaiwal' ); ?>">
				<?php s20_the_icon( 'menu', 's20-menu-toggle-open' ); ?>
				<?php s20_the_icon( 'close', 's20-menu-toggle-close' ); ?>
			</button>
			<div class="s20-menu" id="s20-menu">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 's20-nav-list',
						'menu_id'        => 's20-primary-menu',
						'depth'          => 2,
						'fallback_cb'    => 's20_nav_fallback',
					)
				);
				?>
			</div>
		</nav>

		<?php if ( $s20_date_on ) : ?>
			<div class="s20-header-meta">
				<?php if ( $s20_date_url ) : ?><a class="s20-date-badge" href="<?php echo esc_url( $s20_date_url ); ?>"><?php else : ?><div class="s20-date-badge"><?php endif; ?>
					<?php if ( $s20_date_icon && 'none' !== $s20_date_icon ) { s20_the_icon( $s20_date_icon ); } ?>
					<span><?php echo esc_html( s20_header_date_text() ); ?></span>
				<?php if ( $s20_date_url ) : ?></a><?php else : ?></div><?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</header>
