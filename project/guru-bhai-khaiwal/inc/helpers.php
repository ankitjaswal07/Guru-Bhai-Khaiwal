<?php
/**
 * Helper functions: ACF getters (with safe fallbacks), escaping, URLs, images.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is ACF (PRO) active?
 */
function s20_acf_active() {
	return function_exists( 'get_field' ) && function_exists( 'acf_add_options_page' );
}

/**
 * Read an ACF options value (Guru Bhai Khaiwal > settings pages) with a fallback.
 *
 * Falls back to the raw wp_options entry ACF writes ("options_{name}") when ACF is inactive,
 * so the site keeps rendering with previously saved content.
 *
 * @param string $name    Field name.
 * @param mixed  $default Default when nothing is stored.
 * @return mixed
 */
function s20_get_option( $name, $default = '' ) {
    if ( in_array( $name, array( 'payment_enabled', 'games_show_payment' ), true ) ) { return false; }
    $defaults = s20_brand_defaults();
    $fallback = array_key_exists( $name, $defaults ) ? $defaults[ $name ] : ($GLOBALS['s20_field_defaults'][$name] ?? $default);
    $fallback = s20_native_fallback( $name, $fallback );
    $front = is_front_page() ? (int) get_option( 'page_on_front' ) : 0;
    if ( $front && metadata_exists( 'post', $front, $name ) ) {
        $value = s20_read_saved_field( $name, $front );
    } elseif ( null !== get_option( 'options_' . $name, null ) ) {
        $value = s20_read_saved_field( $name, 'option' );
    } else {
        $value = $fallback;
    }
    return apply_filters( 's20_option', $value, $name, $default );
}


function s20_read_saved_field( $name, $post_id = 'option' ) {
    if ( function_exists( 'get_field' ) ) { return get_field( $name, $post_id ); }
    $option = 'option' === $post_id;
    $value = $option ? get_option( 'options_' . $name ) : get_post_meta( $post_id, $name, true );
    $repeaters = array( 'homepage_section_order' => array( 'section' ), 'promo_cards' => array( 'card_enabled','card_heading','card_icon','card_description','card_person_name','card_phone_text','card_phone','card_whatsapp','card_image','card_button_text','card_button_url','card_secondary_button_text','card_secondary_button_url','card_note' ), 'faq_items' => array( 'question','answer','active' ), 'footer_columns' => array( 'heading','links' ), 'links' => array( 'label','url','new_tab' ), 'social_links' => array( 'icon','custom_icon','label','url' ), 'contact_details' => array( 'icon','label','value','url' ), 'page_sections' => array( 'heading','content','image','active','layout','button_text','button_url','shortcode' ) );
    $base = preg_match( '/_links$/', $name ) ? 'links' : $name;
    if('page_sections'===$base && is_array($value) && isset($value[0]) && is_string($value[0])){
        $rows=array();foreach(array_slice($value,0,500) as $i=>$layout){$row=array('acf_fc_layout'=>$layout);foreach($repeaters[$base] as $sub){$row[$sub]=s20_read_saved_field($name.'_'.$i.'_'.$sub,$post_id);}$rows[]=$row;}return $rows;
    }
    if ( isset( $repeaters[ $base ] ) && is_numeric( $value ) ) {
        $rows = array();
        for ( $i = 0; $i < min( 500, (int) $value ); $i++ ) {
            $row = array();
            foreach ( $repeaters[ $base ] as $sub ) { $row[ $sub ] = s20_read_saved_field( $name . '_' . $i . '_' . $sub, $post_id ); }
            $rows[] = $row;
        }
        return $rows;
    }
    return $value;
}

/**
 * Boolean option helper (ACF true/false).
 */
function s20_option_enabled( $name, $default = true ) {
	$value = s20_get_option( $name, null );
	if ( null === $value ) {
		return (bool) $default;
	}
	return (bool) $value;
}

/**
 * Read a field from the current (or given) post with fallback.
 */
function s20_get_field( $name, $post_id = null, $default = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = null;
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, $post_id );
	} elseif ( $post_id ) {
		$value = metadata_exists('post',$post_id,$name) ? s20_read_saved_field($name,$post_id) : null;
	}
	if ( null === $value || '' === $value ) {
		return $default;
	}
	return $value;
}

/**
 * Sanitize a phone number to a tel: href.
 */
function s20_tel_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', (string) $phone );
	return $digits ? 'tel:' . $digits : '';
}

/**
 * Build a WhatsApp URL from a number or a full URL.
 */
function s20_whatsapp_href( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( preg_match( '#^https?://#i', $value ) ) {
		return esc_url( $value );
	}
	$digits = preg_replace( '/[^0-9]/', '', $value );
	return $digits ? 'https://wa.me/' . $digits : '';
}

/**
 * Resolve any link-ish value: full URL, tel:, mailto:, relative path or anchor.
 */
function s20_link_href( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( preg_match( '#^(tel:|mailto:|https?://|\#|/)#i', $value ) ) {
		return esc_url( $value );
	}
	return esc_url( home_url( '/' . ltrim( $value, '/' ) ) );
}

/**
 * Allowed inline HTML inside headings / short text fields.
 */
function s20_kses_inline( $text ) {
	$allowed = array(
		'a'      => array( 'href' => array(), 'title' => array(), 'target' => array(), 'rel' => array(), 'class' => array() ),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'span'   => array( 'class' => array(), 'style' => array() ),
		'br'     => array(),
		'small'  => array(),
		'mark'   => array(),
	);
	return wp_kses( (string) $text, $allowed );
}

/**
 * Echo escaped inline text (keeps <br>, <strong>, <a>).
 */
function s20_text( $text ) {
	echo s20_kses_inline( $text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by wp_kses.
}

/**
 * Echo multi-line text (textarea) with paragraphs/line breaks preserved.
 */
function s20_rich_text( $text ) {
	echo wp_kses_post( wpautop( (string) $text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Output a responsive image from an ACF image value (array or ID) or attachment ID.
 *
 * @param mixed  $image ACF image (array|int|string url).
 * @param string $size  Registered size.
 * @param array  $attr  Extra attributes.
 */
function s20_image( $image, $size = 'full', $attr = array() ) {
	if ( empty( $image ) ) {
		return;
	}
	$id = 0;
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		$id = (int) $image['ID'];
	} elseif ( is_numeric( $image ) ) {
		$id = (int) $image;
	}
	$attr = wp_parse_args( $attr, array( 'loading' => 'lazy', 'decoding' => 'async' ) );

	if ( $id ) {
		echo wp_get_attachment_image( $id, $size, false, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( is_string( $image ) ) {
		printf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
			esc_url( $image ),
			esc_attr( isset( $attr['alt'] ) ? $attr['alt'] : '' ),
			esc_attr( isset( $attr['class'] ) ? $attr['class'] : '' )
		);
	}
}

/**
 * Get the URL of an ACF image value.
 */
function s20_image_url( $image, $size = 'full' ) {
	if ( empty( $image ) ) {
		return '';
	}
	if ( is_array( $image ) ) {
		if ( 'full' !== $size && ! empty( $image['sizes'][ $size ] ) ) {
			return $image['sizes'][ $size ];
		}
		return isset( $image['url'] ) ? $image['url'] : '';
	}
	if ( is_numeric( $image ) ) {
		$src = wp_get_attachment_image_src( (int) $image, $size );
		return $src ? $src[0] : '';
	}
	return is_string( $image ) ? $image : '';
}

/**
 * Site logo: Header setting > General setting > Customizer custom logo.
 *
 * @param string $context 'header' or 'footer'.
 */
function s20_logo( $context = 'header' ) {
	$image = '';
	if ( 'footer' === $context ) {
		$image = s20_get_option( 'footer_logo' );
	} else {
		$image = s20_get_option( 'header_logo' );
	}
	if ( empty( $image ) ) {
		$image = s20_get_option( 'site_logo' );
	}

	$classes = 's20-logo-img';
	if ( ! empty( $image ) ) {
		s20_image( $image, 'full', array( 'class' => $classes, 'alt' => gbk_brand(), 'loading' => 'eager', 'fetchpriority' => 'high' ) );
		return;
	}
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => $classes, 'alt' => gbk_brand(), 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	echo '<span class="s20-logo-text">' . esc_html( gbk_brand() ) . '</span>';
}

/**
 * Global phone / WhatsApp / Telegram getters with fallbacks.
 */
function s20_site_phone() {
	return (string) s20_get_option( 'site_phone', '' );
}
function s20_site_whatsapp() {
	$wa = s20_get_option( 'site_whatsapp', '' );
	return $wa ? (string) $wa : s20_site_phone();
}
function s20_site_telegram() {
	return (string) s20_get_option( 'site_telegram', '' );
}

/**
 * Replace {year} / {site} placeholders in text.
 */
function s20_placeholders( $text ) {
	return str_replace(
		array( '{year}', '{site}', '{phone}' ),
		array( wp_date( 'Y' ), gbk_brand(), s20_site_phone() ),
		(string) $text
	);
}

/**
 * Normalise ACF repeater output to an array.
 */
function s20_rows( $value ) {
	return ( is_array( $value ) && ! empty( $value ) ) ? $value : array();
}

/**
 * Get a repeater from options with a seeded default when nothing has ever been saved.
 */
function s20_get_repeater( $name, $defaults = array() ) {
	$rows = s20_rows( s20_get_option( $name, array() ) );
	if ( empty( $rows ) && null === get_option('options_'.$name,null) && !(is_front_page() && metadata_exists('post',(int)get_option('page_on_front'),$name)) ) {
		return $defaults;
	}
	return $rows;
}
