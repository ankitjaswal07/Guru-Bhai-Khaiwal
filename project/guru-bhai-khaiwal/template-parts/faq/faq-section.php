<?php
/**
 * Homepage FAQ section.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="s20-section s20-faq-section" id="s20-faq">
	<div class="s20-container">
		<?php s20_section_heading( s20_get_option( 'faq_home_heading', '' ), s20_get_option( 'faq_home_description', '' ) ); ?>
		<?php get_template_part( 'template-parts/faq/faq-list', null, array( 'limit' => (int) s20_get_option( 'faq_home_limit', 0 ) ) ); ?>
	</div>
</section>
