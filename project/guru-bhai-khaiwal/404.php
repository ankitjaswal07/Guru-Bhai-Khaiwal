<?php
/**
 * 404 template.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="s20-section s20-page-section s20-404">
	<div class="s20-container s20-container--narrow s20-text-center">
		<p class="s20-404-code">404</p>
		<h1 class="s20-section-title"><?php esc_html_e( 'Page not found', 'guru-bhai-khaiwal' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist or has been moved.', 'guru-bhai-khaiwal' ); ?></p>
		<div class="s20-section-action"><?php s20_button( __( 'Back to Home', 'guru-bhai-khaiwal' ), home_url( '/' ) ); ?></div>
		<?php get_search_form(); ?>
	</div>
</section>
<?php
get_footer();
