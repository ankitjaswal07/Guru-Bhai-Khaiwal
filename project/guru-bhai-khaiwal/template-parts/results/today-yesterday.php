<?php
/**
 * Today & Yesterday results (plugin shortcodes).
 *
 * Resolution order:
 *  1. Homepage > combined shortcode, else Shortcodes > combined shortcode.
 *  2. Otherwise separate Today / Yesterday shortcodes (homepage fields, else global fields).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_heading  = s20_get_option( 'today_yesterday_heading', '' );
$s20_desc     = s20_get_option( 'today_yesterday_description', '' );

$s20_combined = s20_get_option( 'home_today_yesterday_shortcode', '' );
if ( '' === trim( (string) $s20_combined ) ) {
	$s20_combined = s20_get_option( 'today_yesterday_shortcode', '' );
}
$s20_output = s20_shortcode_output( $s20_combined );

if ( '' === $s20_output ) {
	$s20_today = s20_get_option( 'today_shortcode', '' );
	if ( '' === trim( (string) $s20_today ) ) {
		$s20_today = s20_get_option( 'today_results_shortcode', '' );
	}
	$s20_yesterday = s20_get_option( 'yesterday_shortcode', '' );
	if ( '' === trim( (string) $s20_yesterday ) ) {
		$s20_yesterday = s20_get_option( 'yesterday_results_shortcode', '' );
	}

	$s20_parts  = array();
	$s20_y_html = s20_shortcode_output( $s20_yesterday );
	$s20_t_html = s20_shortcode_output( $s20_today );
	if ( $s20_y_html ) {
		$s20_parts[] = '<div class="s20-results-col s20-results-yesterday">' . $s20_y_html . '</div>';
	}
	if ( $s20_t_html ) {
		$s20_parts[] = '<div class="s20-results-col s20-results-today">' . $s20_t_html . '</div>';
	}
	if ( ! empty( $s20_parts ) ) {
		$s20_output = '<div class="s20-results-split s20-results-split--' . count( $s20_parts ) . '">' . implode( '', $s20_parts ) . '</div>';
	}
}

if ( '' === $s20_output ) {
	$s20_output = s20_shortcode_fallback();
	if ( '' === $s20_output ) {
		return;
	}
}
$s20_width = (int) s20_get_option( 'results_container_width', 1500 );
?>
<section class="s20-section s20-results-section s20-today-yesterday" id="s20-today-yesterday">
	<div class="s20-container s20-container--wide"<?php echo $s20_width ? ' style="max-width:' . (int) $s20_width . 'px"' : ''; ?>>
		<?php s20_section_heading( $s20_heading, $s20_desc ); ?>
		<?php s20_scroll_hint(); ?>
		<div class="s20-shortcode s20-table-scroll"><?php echo $s20_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output. ?></div>
	</div>
</section>
