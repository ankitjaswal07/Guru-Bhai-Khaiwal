<?php
/**
 * Template Name: Contact Page
 * Template Post Type: page
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$s20_details = s20_get_repeater( 'contact_details', s20_default_contact_details() );
	$s20_wa      = s20_option_enabled( 'contact_show_whatsapp_button', true ) ? s20_whatsapp_href( s20_site_whatsapp() ) : '';
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 's20-section s20-page-section s20-contact-page' ); ?>>
		<div class="s20-container">
			<div class="s20-contact-grid">
				<div class="s20-contact-info s20-dark-card s20-dark-card--left">
					<h2 class="s20-section-title s20-section-title--light"><?php s20_text( s20_get_option( 'contact_page_heading', 'GET IN TOUCH' ) ); ?></h2>
					<div class="s20-section-desc s20-section-desc--light"><?php s20_rich_text( s20_get_option( 'contact_page_intro', '' ) ); ?></div>
					<?php if ( ! empty( $s20_details ) ) : ?>
						<ul class="s20-contact-list">
							<?php foreach ( $s20_details as $s20_row ) : ?>
								<?php if ( empty( $s20_row['value'] ) && empty( $s20_row['label'] ) ) { continue; } ?>
								<li>
									<span class="s20-contact-icon"><?php s20_the_icon( ! empty( $s20_row['icon'] ) ? $s20_row['icon'] : 'phone' ); ?></span>
									<span class="s20-contact-body">
										<?php if ( ! empty( $s20_row['label'] ) ) : ?><span class="s20-contact-label"><?php s20_text( $s20_row['label'] ); ?></span><?php endif; ?>
										<?php if ( ! empty( $s20_row['url'] ) ) : ?>
											<a class="s20-contact-value" href="<?php echo esc_url( s20_link_href( $s20_row['url'] ) ); ?>"><?php s20_text( $s20_row['value'] ); ?></a>
										<?php else : ?>
											<span class="s20-contact-value"><?php s20_text( isset( $s20_row['value'] ) ? $s20_row['value'] : '' ); ?></span>
										<?php endif; ?>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $s20_wa ) : ?><div class="s20-card-actions s20-card-actions--left"><?php s20_button( s20_get_option( 'site_button_text', 'WhatsApp Us' ), $s20_wa, array( 'icon' => 'whatsapp' ) ); ?></div><?php endif; ?>
					<?php if ( get_the_content() ) : ?><div class="s20-entry-content s20-entry-content--light"><?php the_content(); ?></div><?php endif; ?>
				</div>
				<div class="s20-contact-form-wrap">
					<h2 class="s20-section-title"><?php s20_text( s20_get_option( 'contact_form_heading', 'SEND US A MESSAGE' ) ); ?></h2>
					<div class="s20-section-desc"><?php s20_rich_text( s20_get_option( 'contact_form_intro', '' ) ); ?></div>
					<?php s20_contact_form(); ?>
				</div>
			</div>
			<?php s20_page_notice(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
