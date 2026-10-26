<?php
/**
 * Payment block (content: Guru Bhai Khaiwal > Payment).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_heading = s20_get_option( 'payment_heading', '' );
$s20_icons   = s20_option_enabled( 'payment_show_icons', false ) ? s20_get_repeater( 'payment_methods', s20_default_payment_methods() ) : array();
$s20_text    = s20_get_option( 'payment_text', '' );
$s20_phone   = s20_get_option( 'payment_phone', s20_site_phone() );
$s20_tagline = s20_get_option( 'payment_tagline', '' );
$s20_closing = s20_get_option( 'payment_closing_heading', '' );
$s20_image   = s20_get_option( 'payment_image' );
$s20_btn     = s20_get_option( 'payment_button_text', '' );
$s20_btn_url = s20_get_option( 'payment_button_url', '' );
$s20_btn_url = $s20_btn_url ? s20_link_href( $s20_btn_url ) : s20_whatsapp_href( $s20_phone ? $s20_phone : s20_site_whatsapp() );
?>
<div class="s20-payment" id="s20-payment">
	<?php if ( $s20_heading ) : ?><p class="s20-payment-heading"><?php s20_text( $s20_heading ); ?></p><?php endif; ?>

	<?php if ( ! empty( $s20_icons ) ) : ?>
		<ul class="s20-payment-methods">
			<?php foreach ( $s20_icons as $s20_method ) : ?>
				<?php if ( isset( $s20_method['active'] ) && ! $s20_method['active'] ) { continue; } ?>
				<li class="s20-payment-method">
					<?php if ( ! empty( $s20_method['image'] ) ) : ?><span class="s20-payment-method-img"><?php s20_image( $s20_method['image'], 'thumbnail' ); ?></span><?php endif; ?>
					<?php if ( ! empty( $s20_method['name'] ) ) : ?><span class="s20-payment-method-name"><?php s20_text( $s20_method['name'] ); ?></span><?php endif; ?>
					<?php if ( ! empty( $s20_method['description'] ) ) : ?><span class="s20-payment-method-desc"><?php s20_text( $s20_method['description'] ); ?></span><?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( ! empty( $s20_image ) ) : ?><div class="s20-payment-image"><?php s20_image( $s20_image, 'medium' ); ?></div><?php endif; ?>
	<?php if ( $s20_text ) : ?><p class="s20-payment-text"><?php s20_text( $s20_text ); ?></p><?php endif; ?>
	<?php if ( $s20_tagline ) : ?><p class="s20-payment-tagline"><?php s20_text( $s20_tagline ); ?></p><?php endif; ?>
	<?php if ( $s20_closing ) : ?><h2 class="s20-payment-closing"><?php s20_text( $s20_closing ); ?></h2><?php endif; ?>
	<?php if ( $s20_btn && $s20_btn_url ) : ?><div class="s20-payment-action"><?php s20_button( $s20_btn, $s20_btn_url ); ?></div><?php endif; ?>
</div>
