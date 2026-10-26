<?php
/**
 * "Contact direct" card.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

s20_promo_card(
	array(
		'card_heading'     => s20_get_option( 'contact_direct_heading', '' ),
		'card_description' => s20_get_option( 'contact_direct_description', '' ),
		'card_person_name' => s20_get_option( 'contact_direct_name', '' ),
		'card_phone_text'  => s20_get_option( 'contact_direct_phone_text', '' ),
		'card_phone'       => s20_get_option( 'contact_direct_phone', '' ),
		'card_whatsapp'    => s20_get_option( 'contact_direct_whatsapp', '' ),
		'card_image'       => s20_get_option( 'contact_direct_image' ),
		'card_button_text' => s20_get_option( 'contact_direct_button_text', '' ),
		'card_button_url'  => s20_get_option( 'contact_direct_button_url', '' ),
		'card_note'        => s20_get_option( 'contact_direct_note', '' ),
		'strip_image'      => s20_get_option( 'hero_strip_image' ),
	),
	's20-contact-direct'
);
