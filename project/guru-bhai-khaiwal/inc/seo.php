<?php
/** Lightweight defaults. Defer head metadata to a recognized SEO plugin. */
defined( 'ABSPATH' ) || exit;
function gbk_filtered_request(): bool {
    foreach ( array( 'gbk_game', 'gbk_date', 'gbk_month', 'gbk_page', 'srm_game_id', 'srm_month', 'srm_year' ) as $key ) { if ( isset( $_GET[ $key ] ) ) { return true; } }
    return is_search();
}
add_filter( 'wp_robots', function ( $robots ) {
    if ( gbk_filtered_request() || is_404() ) { $robots['noindex'] = true; unset( $robots['index'] ); }
    return $robots;
} );
add_action( 'wp_head', function () {
    if ( ! has_site_icon() ) { echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( get_template_directory_uri() . '/assets/favicon.svg' ) . '">'; }
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) { return; }
    $description = gbk_option( 'seo_description' );
    if ( is_singular() && ! is_front_page() ) {
        $excerpt = get_the_excerpt( get_queried_object_id() );
        $description = $excerpt ? wp_trim_words( wp_strip_all_tags( strip_shortcodes( $excerpt ) ), 28, '…' ) : get_the_title() . ' — ' . $description;
    } elseif ( is_archive() ) { $description = wp_strip_all_tags( get_the_archive_title() ) . ' — ' . $description; }
    $paged = max( 1, (int) get_query_var( 'paged' ) );
    if ( $paged > 1 ) { $description .= ' · Page ' . $paged; }
    $canonical = is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : get_pagenum_link( $paged ) );
    if ( gbk_filtered_request() ) { $canonical = is_singular() ? get_permalink() : home_url( '/' ); }
    // WordPress supplies singular canonicals. Avoid duplicate tags.
    if ( ! is_singular() && ! is_404() && ! is_search() ) { echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">'; }
    echo '<meta name="description" content="' . esc_attr( $description ) . '"><meta property="og:site_name" content="' . esc_attr( gbk_brand() ) . '"><meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '"><meta property="og:description" content="' . esc_attr( $description ) . '"><meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '"><meta property="og:url" content="' . esc_url( $canonical ) . '"><meta property="og:locale" content="' . esc_attr( get_locale() ) . '"><meta name="twitter:card" content="summary">';
    $image = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( get_queried_object_id(), 'large' ) : wp_get_attachment_image_url( absint( get_theme_mod( 'custom_logo' ) ), 'full' );
    if ( $image ) { echo '<meta property="og:image" content="' . esc_url( $image ) . '">'; }
    if ( is_front_page() ) {
        echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => gbk_brand(), 'url' => home_url( '/' ), 'description' => $description, 'inLanguage' => get_bloginfo( 'language' ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . '</script>';
    }
}, 5 );
