<?php
/**
 * Default content for repeater fields (ACF has no default_value for repeaters).
 *
 * Seeded ONCE into the ACF options (after theme activation) so administrators start
 * with the neutral informational content and can edit / delete anything afterwards.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

function s20_default_promo_cards() { return array(); }

function s20_default_homepage_section_order() {
	return array_map(
		static function ( $section ) {
			return array( 'section' => $section );
		},
		array( 'hero', 'games', 'today_yesterday', 'promo', 'current_month', 'previous_month', 'contact_direct', 'contact_us', 'blog', 'faq' )
	);
}

function s20_default_payment_methods() { return array(); }

function s20_default_faq_items() {
    $legacy=get_posts(array('post_type'=>'gbk_faq','post_status'=>'publish','numberposts'=>50,'orderby'=>'menu_order','order'=>'ASC'));
    if($legacy)return array_map(static fn($p)=>array('question'=>$p->post_title,'answer'=>$p->post_content,'active'=>1),$legacy);

	$file = S20_DIR . '/inc/data/faq-defaults.php';
	$items = file_exists( $file ) ? include $file : array();
	return is_array( $items ) ? $items : array();
}

function s20_default_footer_columns() {
    $quick=array(array('label'=>'होम','url'=>'/','new_tab'=>0),array('label'=>'गेम का समय','url'=>'/#s20-games','new_tab'=>0),array('label'=>'सवाल-जवाब','url'=>'/#s20-faq','new_tab'=>0));
    $charts=array();$info=array();
    foreach(get_pages(array('post_status'=>'publish','number'=>100,'sort_column'=>'menu_order,post_title')) as $page){
        $link=array('label'=>get_the_title($page),'url'=>get_permalink($page),'new_tab'=>0);
        if(preg_match('/(?:results?|archives?|charts?)$/',$page->post_name))$charts[]=$link;
        elseif(in_array($page->post_name,array('about','about-us','privacy-policy','disclaimer','terms-and-conditions','contact'),true))$info[]=$link;
    }
    return array(array('heading'=>'ज़रूरी लिंक','links'=>$quick),array('heading'=>'रिज़ल्ट चार्ट','links'=>$charts),array('heading'=>'जानकारी','links'=>$info));
}

function s20_default_contact_details() {
	$rows = array();
	if ( s20_site_phone() ) {
		$rows[] = array( 'icon' => 'phone', 'label' => 'Phone', 'value' => s20_site_phone(), 'url' => s20_tel_href( s20_site_phone() ) );
	}
	if ( s20_site_whatsapp() ) {
		$rows[] = array( 'icon' => 'whatsapp', 'label' => 'WhatsApp', 'value' => s20_site_whatsapp(), 'url' => s20_whatsapp_href( s20_site_whatsapp() ) );
	}
	if ( s20_site_telegram() ) {
		$rows[] = array( 'icon' => 'telegram', 'label' => 'Telegram', 'value' => 'Join our channel', 'url' => s20_site_telegram() );
	}
	$email = s20_get_option( 'site_email', get_option( 'admin_email' ) );
	if ( $email ) {
		$rows[] = array( 'icon' => 'mail', 'label' => 'Email', 'value' => $email, 'url' => 'mailto:' . $email );
	}
	return $rows;
}

function s20_default_social_links() {
	$rows = array();
	if ( s20_site_telegram() ) {
		$rows[] = array( 'icon' => 'telegram', 'custom_icon' => '', 'label' => 'Telegram', 'url' => s20_site_telegram() );
	}
	if ( s20_site_whatsapp() ) {
		$rows[] = array( 'icon' => 'whatsapp', 'custom_icon' => '', 'label' => 'WhatsApp', 'url' => s20_whatsapp_href( s20_site_whatsapp() ) );
	}
	return $rows;
}

/**
 * Seed repeater defaults once.
 */
function s20_seed_defaults() {
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( get_option( 's20_defaults_seeded' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	$seed = array(
		'field_s20_home_homepage_section_order' => s20_default_homepage_section_order(),
		'field_s20_home_promo_cards'      => s20_default_promo_cards(),
		'field_s20_payment_payment_methods' => s20_default_payment_methods(),
		'field_s20_faq_faq_items'         => s20_default_faq_items(),
		'field_s20_footer_footer_columns' => s20_default_footer_columns(),
		'field_s20_contact_contact_details' => s20_default_contact_details(),
		'field_s20_social_social_links'   => s20_default_social_links(),
	);

	foreach ( $seed as $field_key => $rows ) {
		$field = function_exists( 'acf_get_field' ) ? acf_get_field( $field_key ) : null;
		if ( ! $field ) {
			continue;
		}
		$existing = get_field( $field['name'], 'option' );
		if ( null === get_option( 'options_' . $field['name'], null ) && ! empty( $rows ) ) {
			update_field( $field_key, $rows, 'option' );
		}
	}

	update_option( 's20_defaults_seeded', S20_VERSION );
}
add_action( 'admin_init', 's20_seed_defaults', 20 );

/** Bind legacy global homepage data to the existing front page, without replacing cleared fields. */
add_action('admin_init',function(){
    $front=(int)get_option('page_on_front');
    if(!$front||!current_user_can('manage_options')||!function_exists('update_field')||!function_exists('get_field'))return;
    if(get_option('s20_home_bound_'.$front))return;
    $group=$GLOBALS['s20_field_groups']['group_s20_homepage']??null;
    if(!$group)return;
    foreach($group['fields'] as $field){
        $name=$field['name']??'';if(!$name||in_array($field['type'],array('tab','message'),true)||metadata_exists('post',$front,$name))continue;
        if(null!==get_option('options_'.$name,null))update_field($field['key'],get_field($name,'option'),$front);
        elseif('homepage_section_order'===$name)update_field($field['key'],s20_default_homepage_section_order(),$front);
    }
    update_option('s20_home_bound_'.$front,1,false);
},25);
