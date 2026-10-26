<?php
/** Section dispatcher adapted from the supplied classic theme. */
defined( 'ABSPATH' ) || exit;
get_header();
$sections = array(); $index = 0;
foreach ( gbk_section_defaults() as $key => $defaults ) {
    ++$index;
    if ( get_theme_mod( 'gbk_' . $key . '_enabled', true ) ) { $sections[ $key ] = (int) get_theme_mod( 'gbk_' . $key . '_order', $index * 10 ); }
}
asort( $sections, SORT_NUMERIC );
if ( ! isset( $sections['hero'] ) ) { echo '<h1 class="screen-reader-text">' . esc_html( gbk_brand() ) . '</h1>'; }
foreach ( $sections as $key => $order ) { gbk_section( $key ); }
get_footer();
