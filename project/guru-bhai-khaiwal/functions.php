<?php
/** Classic theme bootstrap; structured records belong exclusively to the plugin. */
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/sections.php';
add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'guru-bhai-khaiwal', get_template_directory() . '/languages' );
    foreach ( array( 'title-tag', 'automatic-feed-links', 'post-thumbnails', 'responsive-embeds', 'align-wide', 'editor-styles' ) as $support ) { add_theme_support( $support ); }
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'custom-logo', array( 'width' => 240, 'height' => 70, 'flex-width' => true, 'flex-height' => true ) );
    add_post_type_support( 'page', 'excerpt' );
    register_nav_menus( array( 'primary' => __( 'Header navigation', 'guru-bhai-khaiwal' ), 'footer' => __( 'Footer navigation', 'guru-bhai-khaiwal' ) ) );
    add_editor_style( 'assets/main.css' );
    $GLOBALS['content_width'] = 1180;
} );
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'gbk-theme', get_template_directory_uri() . '/assets/main.css', array(), '2.0.0' );
    wp_enqueue_script( 'gbk-navigation', get_template_directory_uri() . '/assets/navigation.js', array(), '2.0.0', array( 'in_footer' => true, 'strategy' => 'defer' ) );
    $css = ':root{--navy:' . ( sanitize_hex_color( gbk_option( 'navy' ) ) ?: '#142536' ) . ';--gold:' . ( sanitize_hex_color( gbk_option( 'gold' ) ) ?: '#e5b85c' ) . ';--paper:' . ( sanitize_hex_color( gbk_option( 'paper' ) ) ?: '#f6f5f1' ) . ';}';
    if ( 'serif' === gbk_option( 'heading_font' ) ) { $css .= 'h1,h2{font-family:Georgia,"Noto Serif Devanagari",serif}'; }
    wp_add_inline_style( 'gbk-theme', $css );
} );
add_action( 'init', function () {
    register_post_type( 'gbk_faq', array( 'labels' => array( 'name' => 'FAQs', 'singular_name' => 'FAQ', 'add_new_item' => 'Add FAQ (question as title)' ), 'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'menu_icon' => 'dashicons-editor-help', 'supports' => array( 'title', 'editor', 'page-attributes' ), 'capability_type' => 'page', 'map_meta_cap' => true ) );
} );
function gbk_archive_url(): string {
    $id = absint( gbk_option( 'archive_page' ) );
    return $id && 'publish' === get_post_status( $id ) ? get_permalink( $id ) : home_url( '/#archive' );
}
function gbk_brand(): string { return get_bloginfo( 'name' ) ?: 'Guru Bhai Khaiwal'; }
function gbk_nav_fallback(): void {
    echo '<ul class="menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li><li><a href="' . esc_url( home_url( '/#schedule' ) ) . '">Schedule</a></li><li><a href="' . esc_url( gbk_archive_url() ) . '">Archives</a></li><li><a href="' . esc_url( home_url( '/#about' ) ) . '">About</a></li><li><a href="' . esc_url( home_url( '/#contact' ) ) . '">Contact</a></li></ul>';
}
function gbk_component( string $shortcode ): void {
    if ( class_exists( 'SRM_Shortcodes' ) ) { echo do_shortcode( $shortcode ); }
    else { echo '<p class="gbk-empty">' . esc_html__( 'The informational archive is not available yet. Please check back later.', 'guru-bhai-khaiwal' ) . '</p>'; }
}
function gbk_breadcrumb(): void {
    if ( is_front_page() ) { return; }
    echo '<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a><span aria-hidden="true"> / </span><span>' . esc_html( is_singular() ? get_the_title() : ( is_search() ? 'Search' : ( is_404() ? 'Page not found' : wp_strip_all_tags( get_the_archive_title() ) ) ) ) . '</span></nav>';
}
// Native per-game template selection; no ACF dependency.
add_action( 'add_meta_boxes', function () {
    add_meta_box( 'gbk-game', 'Individual game archive', function ( $post ) {
        wp_nonce_field( 'gbk_page_game', 'gbk_page_nonce' );
        echo '<label>Game <select name="gbk_game_id"><option value="0">Choose a game</option>';
        if ( class_exists( 'SRM_Games' ) ) {
            foreach ( SRM_Games::get_all_games() as $game ) {
                echo '<option value="' . absint( $game->id ) . '" ' . selected( absint( get_post_meta( $post->ID, '_gbk_game_id', true ) ), $game->id, false ) . '>' . esc_html( $game->event_name ) . '</option>';
            }
        }
        echo '</select></label><p>Used by the Single Game History page template. The results plugin must be active.</p>';
    }, 'page', 'side' );
} );
add_action( 'save_post_page', function ( $id ) {
    if ( wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) || ! current_user_can( 'edit_post', $id ) || ! isset( $_POST['gbk_page_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gbk_page_nonce'] ) ), 'gbk_page_game' ) ) { return; }
    update_post_meta( $id, '_gbk_game_id', absint( $_POST['gbk_game_id'] ?? 0 ) );
} );

// Keep Unicode glyphs local; avoid WordPress's remote emoji-image fallback.
add_action( 'init', function () {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
} );
