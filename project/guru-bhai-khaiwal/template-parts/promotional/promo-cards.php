<?php
/**
 * Promotional dark cards (ACF repeater: Homepage > Promotional Cards).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_cards = s20_get_repeater( 'promo_cards', s20_default_promo_cards() );
$s20_strip = s20_get_option( 'hero_strip_image' );
foreach ( $s20_cards as $s20_i => $s20_card ) {
	$s20_card['strip_image'] = $s20_strip;
	s20_promo_card( $s20_card, 's20-promo s20-promo-' . ( (int) $s20_i + 1 ) );
}
