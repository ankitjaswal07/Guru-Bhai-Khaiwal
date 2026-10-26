<?php
/**
 * ACF PRO field groups (registered in PHP, no manual import required).
 *
 * Field identities match the supplied theme. Neutral defaults override legacy copy.
 * Licensed ACF PRO provides options pages and repeaters.
 *
 * NOTE: Games and results are NOT defined here. They come from the existing plugin
 * through the shortcode fields (Guru Bhai Khaiwal > Shortcodes and page-level fields).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Small builders (keep the definitions readable).
 * ---------------------------------------------------------------------- */

function s20_acf_f( $group, $name, $label, $type = 'text', $args = array() ) {
	$neutral = s20_brand_defaults();
	if ( 'page' !== $group && array_key_exists( $name, $neutral ) ) { $args['default_value'] = $neutral[ $name ]; }
	if ( 'page' !== $group && isset($args['default_value']) ) { $GLOBALS['s20_field_defaults'][$name]=$args['default_value']; }
	if ( 'payment' === $group && 'message' !== $type ) { $args['disabled'] = 1; $args['instructions'] = 'Legacy compatibility field. Payment rendering and processing are disabled for this informational website.'; }
	return array_merge(
		array(
			'key'   => 'field_s20_' . $group . '_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$args
	);
}

function s20_acf_tab( $group, $label ) {
	return array(
		'key'       => 'field_s20_' . $group . '_tab_' . sanitize_key( $label ),
		'label'     => $label,
		'type'      => 'tab',
		'placement' => 'left',
	);
}

function s20_acf_toggle( $group, $name, $label, $default = 1, $instructions = '' ) {
	return s20_acf_f( $group, $name, $label, 'true_false', array( 'ui' => 1, 'default_value' => $default, 'instructions' => $instructions ) );
}

function s20_acf_text( $group, $name, $label, $default = '', $instructions = '' ) {
	return s20_acf_f( $group, $name, $label, 'text', array( 'default_value' => $default, 'instructions' => $instructions ) );
}

function s20_acf_textarea( $group, $name, $label, $default = '', $rows = 3, $instructions = '' ) {
	return s20_acf_f( $group, $name, $label, 'textarea', array( 'default_value' => $default, 'rows' => $rows, 'new_lines' => '', 'instructions' => $instructions ) );
}

function s20_acf_shortcode( $group, $name, $label, $instructions = '' ) {
	$instructions = $instructions ? $instructions : __( 'Enter the shortcode provided by your results plugin, e.g. [your_shortcode]. Leave empty to hide (or to use the global shortcode where applicable).', 'guru-bhai-khaiwal' );
	return s20_acf_f( $group, $name, $label, 'textarea', array( 'rows' => 2, 'new_lines' => '', 'placeholder' => '[your_shortcode]', 'instructions' => $instructions, 'wrapper' => array( 'class' => 's20-shortcode-field' ) ) );
}

function s20_acf_url( $group, $name, $label, $default = '', $instructions = '' ) {
	return s20_acf_f( $group, $name, $label, 'text', array( 'default_value' => $default, 'placeholder' => 'https://', 'instructions' => $instructions ? $instructions : __( 'Full URL, relative path (/page/), tel:, mailto: or # anchor.', 'guru-bhai-khaiwal' ) ) );
}

function s20_acf_image( $group, $name, $label, $instructions = '' ) {
	return s20_acf_f( $group, $name, $label, 'image', array( 'return_format' => 'array', 'preview_size' => 'medium', 'library' => 'all', 'instructions' => $instructions ) );
}

function s20_acf_number( $group, $name, $label, $default = 0, $append = '', $min = 0 ) {
	return s20_acf_f( $group, $name, $label, 'number', array( 'default_value' => $default, 'append' => $append, 'min' => $min ) );
}

function s20_acf_select( $group, $name, $label, $choices, $default = '' ) {
	return s20_acf_f( $group, $name, $label, 'select', array( 'choices' => $choices, 'default_value' => $default, 'ui' => 0, 'return_format' => 'value' ) );
}

function s20_acf_icon_select( $group, $name, $label, $default = 'none' ) {
	return s20_acf_select( $group, $name, $label, s20_icon_choices( true ), $default );
}

function s20_acf_repeater( $group, $name, $label, $sub_fields, $args = array() ) {
	return s20_acf_f(
		$group,
		$name,
		$label,
		'repeater',
		array_merge(
			array(
				'layout'       => 'row',
				'button_label' => __( 'Add item', 'guru-bhai-khaiwal' ),
				'sub_fields'   => $sub_fields,
			),
			$args
		)
	);
}

function s20_acf_message( $group, $name, $message ) {
	return s20_acf_f( $group, $name, '', 'message', array( 'message' => $message, 'new_lines' => 'wpautop', 'esc_html' => 0 ) );
}

function s20_acf_options_location( $slug ) {
	return array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => $slug ) ) );
}

function s20_homepage_section_order_choices() {
	$choices = array(
		'hero'            => __( 'Hero', 'guru-bhai-khaiwal' ),
		'games'           => __( 'Active Games', 'guru-bhai-khaiwal' ),
		'today_yesterday' => __( 'Today & Yesterday Results', 'guru-bhai-khaiwal' ),
		'promo'           => __( 'Promotional Cards', 'guru-bhai-khaiwal' ),
		'current_month'   => __( 'Current Month Results', 'guru-bhai-khaiwal' ),
		'previous_month'  => __( 'Previous Month Results', 'guru-bhai-khaiwal' ),
		'contact_direct'  => __( 'Contact Direct', 'guru-bhai-khaiwal' ),
		'contact_us'      => __( 'Contact Us', 'guru-bhai-khaiwal' ),
		'blog'            => __( 'Latest Posts', 'guru-bhai-khaiwal' ),
		'faq'             => __( 'FAQ', 'guru-bhai-khaiwal' ),
	);

	$front_page = (int) get_option( 'page_on_front' );
	$sections   = $front_page && function_exists( 'get_field' ) ? get_field( 'page_sections', $front_page ) : array();
	foreach ( (array) $sections as $index => $section ) {
		if ( isset( $section['active'] ) && ! $section['active'] ) {
			continue;
		}
		$number = (int) $index + 1;
		$label  = ! empty( $section['heading'] ) ? wp_strip_all_tags( $section['heading'] ) : sprintf( __( 'Custom Content Section %d', 'guru-bhai-khaiwal' ), $number );
		$choices[ 'page_section_' . $number ] = $label;
	}

	return $choices;
}

function s20_load_homepage_section_order_field( $field ) {
	$field['choices'] = s20_homepage_section_order_choices();
	return $field;
}

/**
 * Sub-fields shared by promotional / contact cards.
 */
function s20_acf_card_fields( $group ) {
	$g = $group;
	return array(
		s20_acf_toggle( $g, 'card_enabled', __( 'Active', 'guru-bhai-khaiwal' ), 1 ),
		s20_acf_text( $g, 'card_heading', __( 'Strip Heading', 'guru-bhai-khaiwal' ) ),
		s20_acf_icon_select( $g, 'card_icon', __( 'Icon (optional)', 'guru-bhai-khaiwal' ), 'none' ),
		s20_acf_textarea( $g, 'card_description', __( 'Description', 'guru-bhai-khaiwal' ), '', 2 ),
		s20_acf_text( $g, 'card_person_name', __( 'Person Name', 'guru-bhai-khaiwal' ) ),
		s20_acf_text( $g, 'card_phone_text', __( 'Phone Display Text', 'guru-bhai-khaiwal' ), '', __( 'Shown text, e.g. ☎ +91XXXXXXXXXX ☎️', 'guru-bhai-khaiwal' ) ),
		s20_acf_text( $g, 'card_phone', __( 'Phone Number (tel link)', 'guru-bhai-khaiwal' ) ),
		s20_acf_text( $g, 'card_whatsapp', __( 'WhatsApp Number or URL', 'guru-bhai-khaiwal' ) ),
		s20_acf_image( $g, 'card_image', __( 'Image (optional)', 'guru-bhai-khaiwal' ) ),
		s20_acf_text( $g, 'card_button_text', __( 'Button Text', 'guru-bhai-khaiwal' ), 'WhatsApp Us' ),
		s20_acf_url( $g, 'card_button_url', __( 'Button URL (empty = WhatsApp number)', 'guru-bhai-khaiwal' ) ),
		s20_acf_text( $g, 'card_secondary_button_text', __( 'Secondary Button Text', 'guru-bhai-khaiwal' ) ),
		s20_acf_url( $g, 'card_secondary_button_url', __( 'Secondary Button URL (empty = tel: phone)', 'guru-bhai-khaiwal' ) ),
		s20_acf_text( $g, 'card_note', __( 'Note below buttons', 'guru-bhai-khaiwal' ) ),
	);
}

/* -------------------------------------------------------------------------
 * Register groups.
 * ---------------------------------------------------------------------- */

function s20_register_acf_fields() {

	/* ---------------------------- General ---------------------------- */
	$g = 'general';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_general',
			'title'    => __( 'General Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_tab( $g, 'Branding' ),
				s20_acf_text( $g, 'site_title', 'Site Title', 'Guru Bhai Khaiwal' ),
				s20_acf_textarea( $g, 'seo_description', 'Default SEO Description' ),
				s20_acf_image( $g, 'site_logo', __( 'Site Logo', 'guru-bhai-khaiwal' ), __( 'Used in header and footer unless overridden there.', 'guru-bhai-khaiwal' ) ),
				s20_acf_number( $g, 'site_logo_width', __( 'Header Logo Max Width', 'guru-bhai-khaiwal' ), 290, 'px', 40 ),
				s20_acf_url( $g, 'site_logo_url', __( 'Logo Link URL (empty = homepage)', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'load_google_fonts', __( 'Load Google Fonts (Poppins, Oswald, Roboto)', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_tab( $g, 'Contact Numbers' ),
				s20_acf_text( $g, 'site_phone', __( 'Primary Phone', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'site_whatsapp', __( 'Primary WhatsApp (number or wa.me URL)', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_url( $g, 'site_telegram', __( 'Telegram URL', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'site_email', __( 'Contact Email', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'site_button_text', __( 'Default WhatsApp Button Text', 'guru-bhai-khaiwal' ), 'WhatsApp Us' ),
				s20_acf_tab( $g, 'Inner Page Notice' ),
				s20_acf_toggle( $g, 'page_notice_enabled', __( 'Show notice box at the bottom of inner pages', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'page_notice_heading', __( 'Notice Heading', 'guru-bhai-khaiwal' ), '⚠ IMPORTANT INFORMATION' ),
				s20_acf_textarea( $g, 'page_notice_text', __( 'Notice Text', 'guru-bhai-khaiwal' ), 'Guru Bhai Khaiwal provides information for informational purposes only. We do not promote or encourage illegal activities.' ),
				s20_acf_tab( $g, 'Plugin' ),
				s20_acf_text( $g, 'plugin_admin_url', __( 'Results Plugin Admin URL', 'guru-bhai-khaiwal' ), 'admin.php?page=srm-results', __( 'Shortcut shown in the Guru Bhai Khaiwal menu. Games & results are managed only by your existing plugin.', 'guru-bhai-khaiwal' ) ),
			),
			'location' => s20_acf_options_location( 's20-general' ),
		)
	);

	/* ----------------------------- Header ---------------------------- */
	$g = 'header';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_header',
			'title'    => __( 'Header Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_tab( $g, 'Announcement Bar' ),
				s20_acf_toggle( $g, 'header_announcement_enabled', __( 'Show announcement bar', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'header_announcement', __( 'Announcement Text', 'guru-bhai-khaiwal' ), '⚠️ This site is for informational purposes only. We do not promote or encourage any illegal activities.' ),
				s20_acf_url( $g, 'header_announcement_url', __( 'Announcement Link (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'header_telegram_enabled', __( 'Show Telegram link', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'header_telegram_text', __( 'Telegram Text', 'guru-bhai-khaiwal' ), 'Join Telegram' ),
				s20_acf_url( $g, 'header_telegram_url', __( 'Telegram URL (empty = General > Telegram)', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'header_whatsapp_enabled', __( 'Show WhatsApp link', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'header_whatsapp_text', __( 'WhatsApp Text', 'guru-bhai-khaiwal' ), 'WhatsApp' ),
				s20_acf_text( $g, 'header_whatsapp_url', __( 'WhatsApp number/URL (empty = General > WhatsApp)', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'header_login_enabled', __( 'Show Login link', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'header_login_text', __( 'Login Text', 'guru-bhai-khaiwal' ), 'Login' ),
				s20_acf_url( $g, 'header_login_url', __( 'Login URL (empty = results plugin admin page / wp-login)', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Logo & Navigation' ),
				s20_acf_image( $g, 'header_logo', __( 'Header Logo (overrides General logo)', 'guru-bhai-khaiwal' ) ),
				s20_acf_message( $g, 'nav_msg', __( 'The main navigation is a standard WordPress menu: Appearance > Menus > assign to "Primary Menu (Header)".', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'header_sticky', __( 'Sticky header on scroll', 'guru-bhai-khaiwal' ), 0 ),
				s20_acf_tab( $g, 'Date Badge' ),
				s20_acf_toggle( $g, 'header_date_enabled', __( 'Show date badge (right side)', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'header_date_format', __( 'Date Format (PHP)', 'guru-bhai-khaiwal' ), 'd M Y' ),
				s20_acf_icon_select( $g, 'header_date_icon', __( 'Badge Icon', 'guru-bhai-khaiwal' ), 'calendar' ),
				s20_acf_text( $g, 'header_date_text', __( 'Custom badge text (optional, replaces date)', 'guru-bhai-khaiwal' ) ),
				s20_acf_url( $g, 'header_date_url', __( 'Badge Link (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Marquee' ),
				s20_acf_toggle( $g, 'header_marquee_enabled', __( 'Show marquee bar under header', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'header_marquee_text', __( 'Marquee Text', 'guru-bhai-khaiwal' ), "India's No 1 Super Fast Result Site (SATTA20.COM)" ),
				s20_acf_toggle( $g, 'header_marquee_scroll', __( 'Scroll the text', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_number( $g, 'header_marquee_speed', __( 'Scroll duration', 'guru-bhai-khaiwal' ), 18, 'sec', 4 ),
			),
			'location' => s20_acf_options_location( 's20-header' ),
		)
	);

	/* ---------------------------- Homepage --------------------------- */
	$g = 'home';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_homepage',
			'title'    => __( 'Homepage Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_repeater(
					$g,
					'homepage_section_order',
					__( 'Homepage Section Order', 'guru-bhai-khaiwal' ),
					array(
						s20_acf_select(
							'homeorder',
							'section',
							__( 'Section', 'guru-bhai-khaiwal' ),
							array(
								'hero'            => __( 'Hero', 'guru-bhai-khaiwal' ),
								'games'           => __( 'Active Games', 'guru-bhai-khaiwal' ),
								'today_yesterday' => __( 'Today & Yesterday Results', 'guru-bhai-khaiwal' ),
								'promo'           => __( 'Promotional Cards', 'guru-bhai-khaiwal' ),
								'current_month'   => __( 'Current Month Results', 'guru-bhai-khaiwal' ),
								'previous_month'  => __( 'Previous Month Results', 'guru-bhai-khaiwal' ),
								'contact_direct'  => __( 'Contact Direct', 'guru-bhai-khaiwal' ),
								'contact_us'      => __( 'Contact Us', 'guru-bhai-khaiwal' ),
								'blog'            => __( 'Latest Posts', 'guru-bhai-khaiwal' ),
								'faq'             => __( 'FAQ', 'guru-bhai-khaiwal' ),
								'page_section_1' => __( 'Custom Content Section 1', 'guru-bhai-khaiwal' ),
								'page_section_2' => __( 'Custom Content Section 2', 'guru-bhai-khaiwal' ),
								'page_section_3' => __( 'Custom Content Section 3', 'guru-bhai-khaiwal' ),
								'page_section_4' => __( 'Custom Content Section 4', 'guru-bhai-khaiwal' ),
								'page_section_5' => __( 'Custom Content Section 5', 'guru-bhai-khaiwal' ),
								'page_section_6' => __( 'Custom Content Section 6', 'guru-bhai-khaiwal' ),
								'page_section_7' => __( 'Custom Content Section 7', 'guru-bhai-khaiwal' ),
								'page_section_8' => __( 'Custom Content Section 8', 'guru-bhai-khaiwal' ),
								'page_section_9' => __( 'Custom Content Section 9', 'guru-bhai-khaiwal' ),
								'page_section_10' => __( 'Custom Content Section 10', 'guru-bhai-khaiwal' ),
							),
						),
					),
					array(
						'button_label' => __( 'Add section', 'guru-bhai-khaiwal' ),
						'collapsed'    => 'field_s20_homeorder_section',
					)
				),
				s20_acf_tab( $g, 'Hero' ),
				s20_acf_toggle( $g, 'hero_enabled', __( 'Show Hero', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'hero_heading', __( 'Strip Heading', 'guru-bhai-khaiwal' ), 'Guru Bhai Khaiwal' ),
				s20_acf_image( $g, 'hero_strip_image', __( 'Strip Background Image (optional, e.g. strip-bg.png)', 'guru-bhai-khaiwal' ) ),
				s20_acf_textarea( $g, 'hero_description', __( 'Description (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_image( $g, 'hero_image', __( 'Hero Image (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'hero_show_clock', __( 'Show live date & time box', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_toggle( $g, 'hero_pill_enabled', __( 'Show announcement pill', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_icon_select( $g, 'hero_pill_icon', __( 'Pill Icon', 'guru-bhai-khaiwal' ), 'megaphone' ),
				s20_acf_image( $g, 'hero_pill_icon_image', __( 'Pill Icon Image (optional, overrides icon)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'hero_pill_text', __( 'Pill Text', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_url( $g, 'hero_pill_url', __( 'Pill Link (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'hero_button_text', __( 'Button Text (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_url( $g, 'hero_button_url', __( 'Button URL', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'hero_secondary_button_text', __( 'Secondary Button Text (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_url( $g, 'hero_secondary_button_url', __( 'Secondary Button URL', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'hero_live_status_enabled', __( 'Show Current / Next Game panel (plugin shortcode)', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_shortcode( $g, 'hero_live_status_shortcode', __( 'Current / Next Game Shortcode (empty = Shortcodes > Live Status)', 'guru-bhai-khaiwal' ) ),

				s20_acf_tab( $g, 'Active Games' ),
				s20_acf_toggle( $g, 'games_enabled', __( 'Show Active Games section', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'games_heading', __( 'Heading', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'games_subheading', __( 'Sub Heading', 'guru-bhai-khaiwal' ), '♕♕ Guru Bhai Khaiwal  ♕♕' ),
				s20_acf_text( $g, 'games_timing_heading', __( 'Timing Heading', 'guru-bhai-khaiwal' ), 'NEW TIMING FOR GAME👇🏻👇🏻' ),
				s20_acf_textarea( $g, 'games_description', __( 'Description (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_shortcode( $g, 'home_games_shortcode', __( 'Games Shortcode (empty = Shortcodes > Games)', 'guru-bhai-khaiwal' ) ),
				s20_acf_toggle( $g, 'games_show_payment', __( 'Show Payment block inside this section (content: Guru Bhai Khaiwal > Payment)', 'guru-bhai-khaiwal' ), 1 ),

				s20_acf_tab( $g, 'Today & Yesterday' ),
				s20_acf_toggle( $g, 'today_yesterday_enabled', __( 'Show Today & Yesterday Results', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'today_yesterday_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'Today & Yesterday Results' ),
				s20_acf_textarea( $g, 'today_yesterday_description', __( 'Description (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_shortcode( $g, 'home_today_yesterday_shortcode', __( 'Combined Today/Yesterday Shortcode (empty = Shortcodes > Combined)', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'today_shortcode', __( "Today's Results Shortcode (optional, used when no combined shortcode)", 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'yesterday_shortcode', __( "Yesterday's Results Shortcode (optional)", 'guru-bhai-khaiwal' ) ),

				s20_acf_tab( $g, 'Promotional Cards' ),
				s20_acf_toggle( $g, 'promo_enabled', __( 'Show promotional cards (after Today & Yesterday)', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_repeater( $g, 'promo_cards', __( 'Promotional Cards', 'guru-bhai-khaiwal' ), s20_acf_card_fields( 'promo' ), array( 'button_label' => __( 'Add card', 'guru-bhai-khaiwal' ), 'collapsed' => 'field_s20_promo_card_heading' ) ),

				s20_acf_tab( $g, 'Current Month' ),
				s20_acf_toggle( $g, 'current_month_enabled', __( 'Show Current Month Results', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'current_month_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'Current Month Results' ),
				s20_acf_textarea( $g, 'current_month_description', __( 'Description (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_shortcode( $g, 'current_month_shortcode', __( 'Current Month Shortcode (empty = Shortcodes setting)', 'guru-bhai-khaiwal' ) ),

				s20_acf_tab( $g, 'Previous Month' ),
				s20_acf_toggle( $g, 'previous_month_enabled', __( 'Show Previous Month Results', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'previous_month_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'Previous Month Results' ),
				s20_acf_textarea( $g, 'previous_month_description', __( 'Description (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_shortcode( $g, 'previous_month_shortcode', __( 'Previous Month Shortcode (empty = Shortcodes setting)', 'guru-bhai-khaiwal' ) ),

				s20_acf_tab( $g, 'Contact Direct' ),
				s20_acf_toggle( $g, 'contact_direct_enabled', __( 'Show "Contact Direct" card', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'contact_direct_heading', __( 'Strip Heading', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_textarea( $g, 'contact_direct_description', __( 'Description', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_text( $g, 'contact_direct_name', __( 'Person Name', 'guru-bhai-khaiwal' ), 'Guru Bhai Khaiwal' ),
				s20_acf_text( $g, 'contact_direct_phone_text', __( 'Phone Display Text', 'guru-bhai-khaiwal' ), '☎☎️' ),
				s20_acf_text( $g, 'contact_direct_phone', __( 'Phone Number', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'contact_direct_whatsapp', __( 'WhatsApp Number/URL', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_image( $g, 'contact_direct_image', __( 'Image (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'contact_direct_button_text', __( 'Button Text', 'guru-bhai-khaiwal' ), 'WhatsApp Us' ),
				s20_acf_url( $g, 'contact_direct_button_url', __( 'Button URL (empty = WhatsApp)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'contact_direct_note', __( 'Note', 'guru-bhai-khaiwal' ), '' ),

				s20_acf_tab( $g, 'Contact Us' ),
				s20_acf_toggle( $g, 'contact_us_enabled', __( 'Show "Contact Us" card', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'contact_us_heading', __( 'Strip Heading', 'guru-bhai-khaiwal' ), 'Contact Us' ),
				s20_acf_textarea( $g, 'contact_us_description', __( 'Description', 'guru-bhai-khaiwal' ), 'For any query', 2 ),
				s20_acf_text( $g, 'contact_us_name', __( 'Person Name', 'guru-bhai-khaiwal' ), 'Guru Bhai Khaiwal' ),
				s20_acf_text( $g, 'contact_us_phone_text', __( 'Phone Display Text', 'guru-bhai-khaiwal' ), '☎   ☎️' ),
				s20_acf_text( $g, 'contact_us_phone', __( 'Phone Number', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'contact_us_whatsapp', __( 'WhatsApp Number/URL', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_image( $g, 'contact_us_image', __( 'Image (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'contact_us_whatsapp_button_text', __( 'WhatsApp Button Text', 'guru-bhai-khaiwal' ), 'WhatsApp Us' ),
				s20_acf_text( $g, 'contact_us_call_button_text', __( 'Call Button Text', 'guru-bhai-khaiwal' ), 'Call Us' ),
				s20_acf_text( $g, 'contact_us_note', __( 'Note', 'guru-bhai-khaiwal' ), '' ),

				s20_acf_tab( $g, 'Blog' ),
				s20_acf_toggle( $g, 'blog_enabled', __( 'Show Latest Posts section', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'blog_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'Latest Satta King Updates' ),
				s20_acf_textarea( $g, 'blog_description', __( 'Description', 'guru-bhai-khaiwal' ), 'Stay updated with the latest Satta King news, result guides, charts, market information, and helpful insights.', 2 ),
				s20_acf_number( $g, 'blog_posts_count', __( 'Number of posts', 'guru-bhai-khaiwal' ), 6, '', 1 ),
				s20_acf_select( $g, 'blog_layout', __( 'Layout', 'guru-bhai-khaiwal' ), array( 'carousel' => __( 'Carousel (like live site)', 'guru-bhai-khaiwal' ), 'grid' => __( 'Grid', 'guru-bhai-khaiwal' ) ), 'carousel' ),
				s20_acf_select( $g, 'blog_columns', __( 'Cards per row (desktop)', 'guru-bhai-khaiwal' ), array( '2' => '2', '3' => '3', '4' => '4' ), '4' ),
				s20_acf_f( $g, 'blog_category', __( 'Only from category (optional)', 'guru-bhai-khaiwal' ), 'taxonomy', array( 'taxonomy' => 'category', 'field_type' => 'select', 'allow_null' => 1, 'return_format' => 'id', 'add_term' => 0 ) ),
				s20_acf_toggle( $g, 'blog_show_image', __( 'Show featured image', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_toggle( $g, 'blog_show_date', __( 'Show date', 'guru-bhai-khaiwal' ), 0 ),
				s20_acf_toggle( $g, 'blog_show_excerpt', __( 'Show excerpt', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_toggle( $g, 'blog_show_category', __( 'Show category', 'guru-bhai-khaiwal' ), 0 ),
				s20_acf_text( $g, 'blog_button_text', __( '"View all" button text (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_url( $g, 'blog_button_url', __( 'Button URL (empty = blog page)', 'guru-bhai-khaiwal' ) ),

				s20_acf_tab( $g, 'FAQ' ),
				s20_acf_toggle( $g, 'faq_enabled', __( 'Show FAQ section', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'faq_home_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'Frequently Asked Questions' ),
				s20_acf_textarea( $g, 'faq_home_description', __( 'Description', 'guru-bhai-khaiwal' ), 'Find answers to common questions about Satta King results, charts, markets, and updates.', 2 ),
				s20_acf_number( $g, 'faq_home_limit', __( 'Max items on homepage (0 = all)', 'guru-bhai-khaiwal' ), 0 ),
				s20_acf_message( $g, 'faq_msg', __( 'FAQ items are managed in Guru Bhai Khaiwal > FAQ.', 'guru-bhai-khaiwal' ) ),
			),
			'location' => array(
				array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 's20-homepage' ) ),
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ),
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
		)
	);

	/* ---------------------------- Results ---------------------------- */
	$g = 'results';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_results',
			'title'    => __( 'Results Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_message( $g, 'intro', __( 'Default headings for result page templates. Each page can override its heading and shortcode in the page editor.', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'results_heading_result_chart', __( 'Result Chart page heading', 'guru-bhai-khaiwal' ), 'Result Chart' ),
				s20_acf_text( $g, 'results_heading_today', __( 'Today page heading', 'guru-bhai-khaiwal' ), "Today's Results" ),
				s20_acf_text( $g, 'results_heading_yesterday', __( 'Yesterday page heading', 'guru-bhai-khaiwal' ), "Yesterday's Results" ),
				s20_acf_text( $g, 'results_heading_current_month', __( 'Monthly page - current month heading', 'guru-bhai-khaiwal' ), 'Current Month Results' ),
				s20_acf_text( $g, 'results_heading_previous_month', __( 'Monthly page - previous month heading', 'guru-bhai-khaiwal' ), 'Previous Month Results' ),
				s20_acf_textarea( $g, 'results_intro', __( 'Intro text shown under result page headings (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_number( $g, 'results_container_width', __( 'Results container max width', 'guru-bhai-khaiwal' ), 1500, 'px', 320 ),
				s20_acf_toggle( $g, 'results_scroll_hint', __( 'Show "swipe to see more" hint above wide tables on mobile', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'results_scroll_hint_text', __( 'Hint text', 'guru-bhai-khaiwal' ), '← Swipe to see all columns →' ),
			),
			'location' => s20_acf_options_location( 's20-results' ),
		)
	);

	/* ---------------------------- Contact ---------------------------- */
	$g = 'contact';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_contact',
			'title'    => __( 'Contact Page Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_tab( $g, 'Get in touch' ),
				s20_acf_text( $g, 'contact_page_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'GET IN TOUCH' ),
				s20_acf_textarea( $g, 'contact_page_intro', __( 'Intro', 'guru-bhai-khaiwal' ), 'Have questions about the website or its information?', 2 ),
				s20_acf_repeater(
					$g,
					'contact_details',
					__( 'Contact Details', 'guru-bhai-khaiwal' ),
					array(
						s20_acf_icon_select( 'contactd', 'icon', __( 'Icon', 'guru-bhai-khaiwal' ), 'phone' ),
						s20_acf_text( 'contactd', 'label', __( 'Label', 'guru-bhai-khaiwal' ) ),
						s20_acf_text( 'contactd', 'value', __( 'Value', 'guru-bhai-khaiwal' ) ),
						s20_acf_url( 'contactd', 'url', __( 'Link (optional)', 'guru-bhai-khaiwal' ) ),
					),
					array( 'layout' => 'table' )
				),
				s20_acf_toggle( $g, 'contact_show_whatsapp_button', __( 'Show WhatsApp button', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_tab( $g, 'Form' ),
				s20_acf_text( $g, 'contact_form_heading', __( 'Form Heading', 'guru-bhai-khaiwal' ), 'SEND US A MESSAGE' ),
				s20_acf_textarea( $g, 'contact_form_intro', __( 'Form Intro', 'guru-bhai-khaiwal' ), 'Contact our team using the information below.', 2 ),
				s20_acf_shortcode( $g, 'contact_form_shortcode', __( 'Form plugin shortcode (optional, e.g. Contact Form 7 / WPForms)', 'guru-bhai-khaiwal' ), __( 'If empty, the built-in Name / Email / Message form is used.', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'contact_form_recipient', __( 'Built-in form recipient email (empty = admin email)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'contact_form_button_text', __( 'Submit Button Text', 'guru-bhai-khaiwal' ), 'Send' ),
				s20_acf_text( $g, 'contact_form_success', __( 'Success Message', 'guru-bhai-khaiwal' ), 'Thank you! Your message has been sent.' ),
				s20_acf_text( $g, 'contact_form_error', __( 'Error Message', 'guru-bhai-khaiwal' ), 'Sorry, something went wrong. Please try again.' ),
			),
			'location' => s20_acf_options_location( 's20-contact' ),
		)
	);

	/* ---------------------------- Payment ---------------------------- */
	$g = 'payment';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_payment',
			'title'    => __( 'Payment Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_toggle( $g, 'payment_enabled', __( 'Show Payment block', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_text( $g, 'payment_heading', __( 'Heading', 'guru-bhai-khaiwal' ), 'Payment Option' ),
				s20_acf_toggle( $g, 'payment_show_icons', __( 'Show payment method icons row', 'guru-bhai-khaiwal' ), 0, __( 'The live site currently keeps this row hidden.', 'guru-bhai-khaiwal' ) ),
				s20_acf_repeater(
					$g,
					'payment_methods',
					__( 'Payment Methods', 'guru-bhai-khaiwal' ),
					array(
						s20_acf_image( 'paym', 'image', __( 'Icon / Image', 'guru-bhai-khaiwal' ) ),
						s20_acf_text( 'paym', 'name', __( 'Name', 'guru-bhai-khaiwal' ) ),
						s20_acf_text( 'paym', 'description', __( 'Description (optional)', 'guru-bhai-khaiwal' ) ),
						s20_acf_toggle( 'paym', 'active', __( 'Active', 'guru-bhai-khaiwal' ), 1 ),
					),
					array( 'layout' => 'table' )
				),
				s20_acf_text( $g, 'payment_text', __( 'Payment Text', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'payment_phone', __( 'Payment Phone / UPI number', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'payment_tagline', __( 'Tagline', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_text( $g, 'payment_closing_heading', __( 'Closing Heading', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_image( $g, 'payment_image', __( 'QR / Payment Image (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'payment_button_text', __( 'Button Text', 'guru-bhai-khaiwal' ), 'WhatsApp Us' ),
				s20_acf_url( $g, 'payment_button_url', __( 'Button URL (empty = WhatsApp of payment phone)', 'guru-bhai-khaiwal' ) ),
			),
			'location' => s20_acf_options_location( 's20-payment' ),
		)
	);

	/* ------------------------------ FAQ ------------------------------ */
	$g = 'faq';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_faq',
			'title'    => __( 'FAQ Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_text( $g, 'faq_heading', __( 'FAQ Page Heading', 'guru-bhai-khaiwal' ), 'Frequently Asked Questions' ),
				s20_acf_textarea( $g, 'faq_description', __( 'FAQ Page Description', 'guru-bhai-khaiwal' ), 'Find answers to common questions about Satta King results, charts, markets, and updates.', 2 ),
				s20_acf_toggle( $g, 'faq_first_open', __( 'Open first item by default', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_toggle( $g, 'faq_single_open', __( 'Only one item open at a time', 'guru-bhai-khaiwal' ), 0 ),
				s20_acf_repeater(
					$g,
					'faq_items',
					__( 'FAQ Items', 'guru-bhai-khaiwal' ),
					array(
						s20_acf_text( 'faqi', 'question', __( 'Question', 'guru-bhai-khaiwal' ) ),
						s20_acf_f( 'faqi', 'answer', __( 'Answer', 'guru-bhai-khaiwal' ), 'wysiwyg', array( 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0, 'delay' => 1 ) ),
						s20_acf_toggle( 'faqi', 'active', __( 'Active', 'guru-bhai-khaiwal' ), 1 ),
					),
					array( 'button_label' => __( 'Add question', 'guru-bhai-khaiwal' ), 'collapsed' => 'field_s20_faqi_question' )
				),
			),
			'location' => s20_acf_options_location( 's20-faq' ),
		)
	);

	/* ----------------------------- Footer ---------------------------- */
	$g = 'footer';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_footer',
			'title'    => __( 'Footer Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_tab( $g, 'Brand' ),
				s20_acf_image( $g, 'footer_logo', __( 'Footer Logo (empty = site logo)', 'guru-bhai-khaiwal' ) ),
				s20_acf_text( $g, 'footer_tagline', __( 'Tagline', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_textarea( $g, 'footer_description', __( 'Description', 'guru-bhai-khaiwal' ), '', 3 ),
				s20_acf_image( $g, 'footer_background', __( 'Footer Background Image (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Link Columns' ),
				s20_acf_repeater(
					$g,
					'footer_columns',
					__( 'Footer Columns', 'guru-bhai-khaiwal' ),
					array(
						s20_acf_text( 'footc', 'heading', __( 'Column Heading', 'guru-bhai-khaiwal' ) ),
						s20_acf_repeater(
							'footc',
							'links',
							__( 'Links', 'guru-bhai-khaiwal' ),
							array(
								s20_acf_text( 'footl', 'label', __( 'Label', 'guru-bhai-khaiwal' ) ),
								s20_acf_url( 'footl', 'url', __( 'URL', 'guru-bhai-khaiwal' ) ),
								s20_acf_toggle( 'footl', 'new_tab', __( 'New tab', 'guru-bhai-khaiwal' ), 0 ),
							),
							array( 'layout' => 'table', 'button_label' => __( 'Add link', 'guru-bhai-khaiwal' ) )
						),
					),
					array( 'button_label' => __( 'Add column', 'guru-bhai-khaiwal' ), 'collapsed' => 'field_s20_footc_heading', 'max' => 4 )
				),
				s20_acf_toggle( $g, 'footer_show_social', __( 'Show social icons (Guru Bhai Khaiwal > Social)', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_tab( $g, 'Disclaimer' ),
				s20_acf_toggle( $g, 'footer_disclaimer_enabled', __( 'Show disclaimer bar', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_icon_select( $g, 'footer_disclaimer_icon', __( 'Disclaimer Icon', 'guru-bhai-khaiwal' ), 'warning' ),
				s20_acf_text( $g, 'footer_disclaimer_title', __( 'Disclaimer Title', 'guru-bhai-khaiwal' ), 'INFORMATIONAL DISCLAIMER' ),
				s20_acf_textarea( $g, 'footer_disclaimer_text', __( 'Disclaimer Text', 'guru-bhai-khaiwal' ), 'This website is for informational purposes only. We do not promote or encourage illegal gambling or betting activities. Users must be 18+ years to visit this website.', 3 ),
				s20_acf_text( $g, 'footer_age_badge', __( 'Age Badge', 'guru-bhai-khaiwal' ), '18+' ),
				s20_acf_text( $g, 'footer_age_text', __( 'Age Badge Text', 'guru-bhai-khaiwal' ), '' ),
				s20_acf_tab( $g, 'Copyright' ),
				s20_acf_text( $g, 'copyright_text', __( 'Copyright Text ({year} = current year)', 'guru-bhai-khaiwal' ), '© {year} Guru Bhai Khaiwal.com | All Rights Reserved.' ),
				s20_acf_textarea( $g, 'footer_extra_text', __( 'Additional text (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
			),
			'location' => s20_acf_options_location( 's20-footer' ),
		)
	);

	/* ----------------------------- Social ---------------------------- */
	$g = 'social';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_social',
			'title'    => __( 'Social Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_repeater(
					$g,
					'social_links',
					__( 'Social Links', 'guru-bhai-khaiwal' ),
					array(
						s20_acf_icon_select( 'soc', 'icon', __( 'Icon', 'guru-bhai-khaiwal' ), 'telegram' ),
						s20_acf_image( 'soc', 'custom_icon', __( 'Custom icon image (optional)', 'guru-bhai-khaiwal' ) ),
						s20_acf_text( 'soc', 'label', __( 'Label', 'guru-bhai-khaiwal' ) ),
						s20_acf_url( 'soc', 'url', __( 'URL', 'guru-bhai-khaiwal' ) ),
					),
					array( 'layout' => 'table', 'button_label' => __( 'Add social link', 'guru-bhai-khaiwal' ) )
				),
				s20_acf_toggle( $g, 'social_show_header', __( 'Also show social icons in the announcement bar', 'guru-bhai-khaiwal' ), 0 ),
			),
			'location' => s20_acf_options_location( 's20-social' ),
		)
	);

	/* --------------------------- Shortcodes -------------------------- */
	$g = 'sc';
	s20_add_field_group(
		array(
			'key'      => 'group_s20_shortcodes',
			'title'    => __( 'Shortcode Settings (results plugin integration)', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_message( $g, 'intro', __( 'Enter the shortcodes provided by your existing results plugin. The theme only displays their output inside the design - it never stores games or results itself. Individual pages can override these in the page editor.', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Games' ),
				s20_acf_shortcode( $g, 'games_shortcode', __( 'Game Listing Shortcode', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'live_status_shortcode', __( 'Current / Next Game (Live Status) Shortcode', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Today' ),
				s20_acf_shortcode( $g, 'today_results_shortcode', __( "Today's Results Shortcode", 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Yesterday' ),
				s20_acf_shortcode( $g, 'yesterday_results_shortcode', __( "Yesterday's Results Shortcode", 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'today_yesterday_shortcode', __( 'Combined Today & Yesterday Shortcode (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Current Month' ),
				s20_acf_shortcode( $g, 'current_month_results_shortcode', __( 'Current Month Results Shortcode', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Previous Month' ),
				s20_acf_shortcode( $g, 'previous_month_results_shortcode', __( 'Previous Month Results Shortcode', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Result Chart' ),
				s20_acf_shortcode( $g, 'result_chart_shortcode', __( 'Result Chart Shortcode', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Other' ),
				s20_acf_shortcode( $g, 'additional_shortcode_1', __( 'Additional Shortcode 1', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'additional_shortcode_2', __( 'Additional Shortcode 2', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'additional_shortcode_3', __( 'Additional Shortcode 3', 'guru-bhai-khaiwal' ) ),
				s20_acf_tab( $g, 'Fallback' ),
				s20_acf_select( $g, 'shortcode_empty_behavior', __( 'When a shortcode field is empty', 'guru-bhai-khaiwal' ), array( 'hide' => __( 'Hide the section', 'guru-bhai-khaiwal' ), 'message' => __( 'Show a fallback message', 'guru-bhai-khaiwal' ) ), 'hide' ),
				s20_acf_text( $g, 'shortcode_empty_message', __( 'Fallback message', 'guru-bhai-khaiwal' ), 'Results will be available soon.' ),
			),
			'location' => s20_acf_options_location( 's20-shortcodes' ),
		)
	);

	/* ------------------- Page-specific shortcode fields --------------- */
	$g = 'page';
	$result_templates = array(
		'page-templates/template-result-chart.php',
		'page-templates/template-today-results.php',
		'page-templates/template-yesterday-results.php',
		'page-templates/template-monthly-results.php',
		'page-templates/template-game-results.php',
	);
	$location = array();
	foreach ( $result_templates as $template ) {
		$location[] = array( array( 'param' => 'page_template', 'operator' => '==', 'value' => $template ) );
	}
	s20_add_field_group(
		array(
			'key'      => 'group_s20_page_results',
			'title'    => __( 'Results Page Settings (this page)', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_text( $g, 'page_heading', __( 'Heading override (empty = default heading / page title)', 'guru-bhai-khaiwal' ) ),
				s20_acf_textarea( $g, 'page_intro', __( 'Intro text (optional)', 'guru-bhai-khaiwal' ), '', 2 ),
				s20_acf_shortcode( $g, 'result_chart_shortcode', __( 'Result Chart Shortcode (Result Chart / Game templates)', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'today_results_shortcode', __( "Today's Results Shortcode (Today template)", 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'yesterday_results_shortcode', __( "Yesterday's Results Shortcode (Yesterday template)", 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'current_month_results_shortcode', __( 'Current Month Shortcode (Monthly template)', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'previous_month_results_shortcode', __( 'Previous Month Shortcode (Monthly template)', 'guru-bhai-khaiwal' ) ),
				s20_acf_shortcode( $g, 'page_extra_shortcode', __( 'Additional shortcode (rendered after the main output)', 'guru-bhai-khaiwal' ) ),
			),
			'location' => $location,
			'position' => 'acf_after_title',
		)
	);

	s20_add_field_group(
		array(
			'key'      => 'group_s20_page_generic',
			'title'    => __( 'Page Display Settings', 'guru-bhai-khaiwal' ),
			'fields'   => array(
				s20_acf_toggle( $g, 'page_show_title', __( 'Show page title', 'guru-bhai-khaiwal' ), 1 ),
				s20_acf_toggle( $g, 'page_hide_notice', __( 'Hide the bottom notice box on this page', 'guru-bhai-khaiwal' ), 0 ),
				s20_acf_shortcode( $g, 'page_content_shortcode', __( 'Shortcode rendered after the page content (optional)', 'guru-bhai-khaiwal' ) ),
				s20_acf_f(
					$g,
					'page_sections',
					__( 'Page Sections', 'guru-bhai-khaiwal' ),
					'flexible_content',
					array(
						'button_label' => __( 'Add section', 'guru-bhai-khaiwal' ),
						'layouts'      => array(
							'full_content' => array(
								'key'        => 'layout_s20_page_full_content',
								'name'       => 'full_content',
								'label'      => __( 'Full content section', 'guru-bhai-khaiwal' ),
								'display'    => 'block',
								'sub_fields' => array(
									s20_acf_toggle( 'pagefull', 'active', __( 'Active', 'guru-bhai-khaiwal' ), 1 ),
									s20_acf_text( 'pagefull', 'heading', __( 'Heading', 'guru-bhai-khaiwal' ) ),
									s20_acf_f( 'pagefull', 'content', __( 'Content', 'guru-bhai-khaiwal' ), 'wysiwyg', array( 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1 ) ),
								),
							),
							'image_left' => array(
								'key'        => 'layout_s20_page_image_left',
								'name'       => 'image_left',
								'label'      => __( 'Image left, content right', 'guru-bhai-khaiwal' ),
								'display'    => 'block',
								'sub_fields' => array(
									s20_acf_toggle( 'pageimageleft', 'active', __( 'Active', 'guru-bhai-khaiwal' ), 1 ),
									s20_acf_image( 'pageimageleft', 'image', __( 'Image', 'guru-bhai-khaiwal' ) ),
									s20_acf_text( 'pageimageleft', 'heading', __( 'Heading', 'guru-bhai-khaiwal' ) ),
									s20_acf_f( 'pageimageleft', 'content', __( 'Content', 'guru-bhai-khaiwal' ), 'wysiwyg', array( 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1 ) ),
								),
							),
							'content_left' => array(
								'key'        => 'layout_s20_page_content_left',
								'name'       => 'content_left',
								'label'      => __( 'Content left, image right', 'guru-bhai-khaiwal' ),
								'display'    => 'block',
								'sub_fields' => array(
									s20_acf_toggle( 'pagecontentleft', 'active', __( 'Active', 'guru-bhai-khaiwal' ), 1 ),
									s20_acf_text( 'pagecontentleft', 'heading', __( 'Heading', 'guru-bhai-khaiwal' ) ),
									s20_acf_f( 'pagecontentleft', 'content', __( 'Content', 'guru-bhai-khaiwal' ), 'wysiwyg', array( 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1 ) ),
									s20_acf_image( 'pagecontentleft', 'image', __( 'Image', 'guru-bhai-khaiwal' ) ),
								),
							),
						),
					)
				),
			),
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ),
				),
			),
			'position' => 'acf_after_title',
		)
	);
}
add_action( 'acf/init', 's20_register_acf_fields' );
add_filter( 'acf/load_field/key=field_s20_homeorder_section', 's20_load_homepage_section_order_field' );

/** Public rendering may use defaults without ACF, but no fake admin editor is registered. */
function s20_add_field_group($group) {
    if('group_s20_page_generic'===$group['key'])$group['location'][]=array(array('param'=>'options_page','operator'=>'==','value'=>'s20-homepage'));
    $GLOBALS['s20_field_groups'][$group['key']]=$group;
    if(function_exists('acf_add_local_field_group'))acf_add_local_field_group($group);
}
add_action('after_setup_theme',function(){if(!function_exists('acf_add_local_field_group'))s20_register_acf_fields();});
