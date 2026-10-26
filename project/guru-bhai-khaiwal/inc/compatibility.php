<?php
/** Retained 2.0 settings are read as fallback, never deleted. */
defined( 'ABSPATH' ) || exit;
function gbk_brand(): string { return (string) s20_get_option( 'site_title', 'Guru Bhai Khaiwal' ); }
function gbk_option( $name ) { return s20_get_option( $name ); }
function s20_native_fallback( $name, $default ) {
    $map = array( 'hero_heading' => 'hero_title', 'hero_description' => 'hero_text', 'header_announcement' => 'notice', 'footer_disclaimer_text' => 'footer_text', 'site_email' => 'email', 'site_phone' => 'phone', 'site_title' => 'brand_name', 'seo_description' => 'seo_description', 'hero_image'=>'hero_image', 'footer_tagline'=>'footer_tagline', 'style_accent'=>'gold', 'style_background'=>'paper' );
    return isset( $map[ $name ] ) ? get_theme_mod( 'gbk_' . $map[ $name ], $default ) : $default;
}
// Avoid remote emoji assets; native glyphs remain available.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );
add_filter('document_title_parts',function($parts){
    if(is_front_page()){$parts['title']=gbk_brand();unset($parts['site']);}else{$parts['site']=gbk_brand();}return $parts;
});
