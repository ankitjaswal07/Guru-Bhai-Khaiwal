<?php
/**
 * Search form.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_search_id = wp_unique_id( 's20-search-' );
?>
<form role="search" method="get" class="s20-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $s20_search_id ); ?>"><?php esc_html_e( 'Search for:', 'guru-bhai-khaiwal' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $s20_search_id ); ?>" class="s20-search-field" placeholder="<?php esc_attr_e( 'Search...', 'guru-bhai-khaiwal' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="s20-btn s20-search-submit"><?php esc_html_e( 'Search', 'guru-bhai-khaiwal' ); ?></button>
</form>
