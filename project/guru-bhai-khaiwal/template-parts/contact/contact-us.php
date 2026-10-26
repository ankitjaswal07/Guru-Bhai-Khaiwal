<?php
/**
 * "Contact Us" card with WhatsApp + Call buttons.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

s20_promo_card(
	array(
		'card_heading'               => s20_get_option( 'contact_us_heading', '' ),
		'card_description'           => s20_get_option( 'contact_us_description', '' ),
		'card_person_name'           => s20_get_option( 'contact_us_name', '' ),
		'card_phone_text'            => s20_get_option( 'contact_us_phone_text', '' ),
		'card_phone'                 => s20_get_option( 'contact_us_phone', '' ),
		'card_whatsapp'              => s20_get_option( 'contact_us_whatsapp', '' ),
		'card_image'                 => s20_get_option( 'contact_us_image' ),
		'card_button_text'           => s20_get_option( 'contact_us_whatsapp_button_text', '' ),
		'card_secondary_button_text' => s20_get_option( 'contact_us_call_button_text', '' ),
		'card_note'                  => s20_get_option( 'contact_us_note', '' ),
		'strip_image'                => s20_get_option( 'hero_strip_image' ),
	),
	's20-contact-us'
);
