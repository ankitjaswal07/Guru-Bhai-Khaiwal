<?php
/**
 * Template output helpers.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fallback for the primary menu when none is assigned.
 */
function s20_nav_fallback() {
	echo '<ul class="s20-nav-list">';
	echo '<li class="menu-item' . ( is_front_page() ? ' current-menu-item' : '' ) . '"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'guru-bhai-khaiwal' ) . '</a></li>';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
			'number'   => 6,
			'exclude'  => get_option( 'page_on_front' ),
		)
	);
	echo '</ul>';
}

/**
 * Button.
 *
 * @param string $text  Label.
 * @param string $url   Href (already resolved).
 * @param array  $args  { class, new_tab, icon }.
 */
function s20_button( $text, $url, $args = array() ) {
	$text = trim( (string) $text );
	if ( '' === $text || '' === $url ) {
		return;
	}
	$args    = wp_parse_args( $args, array( 'class' => '', 'new_tab' => null, 'icon' => '' ) );
	$new_tab = null === $args['new_tab'] ? (bool) preg_match( '#^https?://#i', $url ) && false === strpos( $url, home_url() ) : (bool) $args['new_tab'];
	$target  = $new_tab ? ' target="_blank" rel="noopener nofollow"' : '';
	echo '<a class="s20-btn ' . esc_attr( $args['class'] ) . '" href="' . esc_url( $url ) . '"' . $target . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( $args['icon'] ) {
		s20_the_icon( $args['icon'], 's20-btn-icon' );
	}
	echo '<span>' . s20_kses_inline( $text ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Gold "strip" heading used at the top of dark cards.
 */
function s20_strip_heading( $text, $tag = 'h2', $image = null, $class = '' ) {
	$text = trim( (string) $text );
	if ( '' === $text ) {
		return;
	}
	$tag   = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'p', 'span' ), true ) ? $tag : 'h2';
	$style = '';
	$url   = s20_image_url( $image );
	if ( $url ) {
		$style = ' style="background-image:url(' . esc_url( $url ) . ')"';
	}
	printf(
		'<%1$s class="s20-strip %2$s%3$s"%4$s>%5$s</%1$s>',
		$tag, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_attr( $class ),
		$url ? ' s20-strip--image' : '',
		$style, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		s20_kses_inline( $text ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Section heading + optional description.
 */
function s20_section_heading( $heading, $description = '', $tag = 'h2', $class = '' ) {
	if ( '' !== trim( (string) $heading ) ) {
		$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4' ), true ) ? $tag : 'h2';
		echo '<' . $tag . ' class="s20-section-title ' . esc_attr( $class ) . '">' . s20_kses_inline( $heading ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( '' !== trim( (string) $description ) ) {
		echo '<div class="s20-section-desc">';
		s20_rich_text( $description );
		echo '</div>';
	}
}

/**
 * Render reorderable sections configured on a page.
 */
function s20_render_page_sections( $post_id = 0, $section_number = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$sections = s20_get_field( 'page_sections', $post_id, array() );
	if ( ! is_array( $sections ) || empty( $sections ) ) {
		return;
	}

	foreach ( $sections as $section_index => $section ) {
		if ( $section_number > 0 && (int) $section_index + 1 !== $section_number ) {
			continue;
		}
		if ( isset( $section['active'] ) && ! $section['active'] ) {
			continue;
		}
		$layout  = isset( $section['acf_fc_layout'] ) ? sanitize_key( $section['acf_fc_layout'] ) : '';
		$heading = isset( $section['heading'] ) ? $section['heading'] : '';
		$content = isset( $section['content'] ) ? $section['content'] : '';
		$image   = isset( $section['image'] ) ? $section['image'] : '';
		if ( ! in_array( $layout, array( 'full_content', 'image_left', 'content_left' ), true ) || ( '' === trim( (string) $heading ) && '' === trim( (string) $content ) && ! $image ) ) {
			continue;
		}
		?>
		<section class="s20-page-builder-section s20-page-builder-section--<?php echo esc_attr( $layout ); ?>">
			<div class="s20-page-builder-grid s20-container">
				<?php if ( 'image_left' === $layout || 'content_left' === $layout ) : ?>
					<div class="s20-page-builder-media">
						<?php if ( $image ) { s20_image( $image, 'large' ); } ?>
					</div>
				<?php endif; ?>
				<div class="s20-page-builder-content s20-entry-content">
					<?php if ( $heading ) : ?><h2><?php s20_text( $heading ); ?></h2><?php endif; ?>
					<?php if ( $content ) { s20_rich_text( $content ); } ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/**
 * Render a full "results" style section around a plugin shortcode.
 *
 * @param array $args {
 *   @type string $id          Section id.
 *   @type string $heading     Heading.
 *   @type string $description Description.
 *   @type string $shortcode   Raw shortcode.
 *   @type string $class       Extra section classes.
 *   @type string $tag         Heading tag.
 * }
 * @return bool Whether the section was printed.
 */
function s20_shortcode_section( $args ) {
	$args   = wp_parse_args( $args, array( 'id' => '', 'heading' => '', 'description' => '', 'shortcode' => '', 'class' => '', 'tag' => 'h2', 'wide' => true ) );
	$output = s20_shortcode_output( $args['shortcode'] );
	if ( '' === $output ) {
		$output = s20_shortcode_fallback();
		if ( '' === $output ) {
			return false;
		}
	}
	$width = (int) s20_get_option( 'results_container_width', 1500 );
	echo '<section class="s20-section s20-results-section ' . esc_attr( $args['class'] ) . '"' . ( $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '' ) . '>';
	echo '<div class="s20-container' . ( $args['wide'] ? ' s20-container--wide' : '' ) . '"' . ( $args['wide'] && $width ? ' style="max-width:' . (int) $width . 'px"' : '' ) . '>';
	s20_section_heading( $args['heading'], $args['description'], $args['tag'] );
	s20_scroll_hint();
	echo '<div class="s20-shortcode s20-table-scroll">' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output.
	echo '</div></section>';
	return true;
}

/**
 * Small mobile hint above wide tables.
 */
function s20_scroll_hint() {
	if ( ! s20_option_enabled( 'results_scroll_hint', true ) ) {
		return;
	}
	$text = s20_get_option( 'results_scroll_hint_text', '← Swipe to see all columns →' );
	echo '<p class="s20-scroll-hint">' . esc_html( $text ) . '</p>';
}

/**
 * Dark promotional / contact card.
 *
 * @param array $card Keys: card_heading, card_icon, card_description, card_person_name, card_phone_text,
 *                    card_phone, card_whatsapp, card_image, card_button_text, card_button_url,
 *                    card_secondary_button_text, card_secondary_button_url, card_note, strip_image.
 */
function s20_promo_card( $card, $class = '' ) {
	$card = wp_parse_args(
		(array) $card,
		array(
			'card_enabled' => 1, 'card_heading' => '', 'card_icon' => 'none', 'card_description' => '', 'card_person_name' => '',
			'card_phone_text' => '', 'card_phone' => '', 'card_whatsapp' => '', 'card_image' => '', 'card_button_text' => '',
			'card_button_url' => '', 'card_secondary_button_text' => '', 'card_secondary_button_url' => '', 'card_note' => '', 'strip_image' => null,
		)
	);
	if ( empty( $card['card_enabled'] ) ) {
		return;
	}
	$phone_href = s20_tel_href( $card['card_phone'] );
	$wa_href    = s20_whatsapp_href( $card['card_whatsapp'] ? $card['card_whatsapp'] : $card['card_phone'] );
	$btn_url    = $card['card_button_url'] ? s20_link_href( $card['card_button_url'] ) : $wa_href;
	$btn2_url   = $card['card_secondary_button_url'] ? s20_link_href( $card['card_secondary_button_url'] ) : $phone_href;
	?>
	<section class="s20-section s20-card-section <?php echo esc_attr( $class ); ?>">
		<div class="s20-container">
			<div class="s20-dark-card">
				<?php s20_strip_heading( $card['card_heading'], 'h2', $card['strip_image'], 's20-strip--wide' ); ?>
				<?php if ( ! empty( $card['card_icon'] ) && 'none' !== $card['card_icon'] ) : ?>
					<div class="s20-card-icon"><?php s20_the_icon( $card['card_icon'] ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $card['card_image'] ) ) : ?>
					<div class="s20-card-image"><?php s20_image( $card['card_image'], 'medium_large', array( 'class' => 's20-card-img' ) ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $card['card_description'] ) ) : ?>
					<p class="s20-card-desc"><?php s20_text( nl2br( $card['card_description'] ) ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $card['card_person_name'] ) ) : ?>
					<h2 class="s20-card-name"><?php s20_text( $card['card_person_name'] ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $card['card_phone_text'] ) ) : ?>
					<h2 class="s20-card-phone">
						<?php if ( $phone_href ) : ?>
							<a href="<?php echo esc_url( $phone_href ); ?>"><?php s20_text( $card['card_phone_text'] ); ?></a>
						<?php else : ?>
							<?php s20_text( $card['card_phone_text'] ); ?>
						<?php endif; ?>
					</h2>
				<?php endif; ?>
				<?php if ( $card['card_button_text'] || $card['card_secondary_button_text'] ) : ?>
					<div class="s20-card-actions">
						<?php s20_button( $card['card_button_text'], $btn_url ); ?>
						<?php s20_button( $card['card_secondary_button_text'], $btn2_url ); ?>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $card['card_note'] ) ) : ?>
					<p class="s20-card-note"><?php s20_text( $card['card_note'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Notice box at the bottom of inner pages.
 */
function s20_page_notice() {
	if ( ! s20_option_enabled( 'page_notice_enabled', true ) ) {
		return;
	}
	if ( is_singular( 'page' ) && s20_get_field( 'page_hide_notice', get_the_ID(), 0 ) ) {
		return;
	}
	$heading = s20_get_option( 'page_notice_heading', '⚠ IMPORTANT INFORMATION' );
	$text    = s20_get_option( 'page_notice_text', '' );
	if ( '' === trim( $heading . $text ) ) {
		return;
	}
	echo '<div class="s20-notice-box">';
	if ( $heading ) {
		echo '<h3 class="s20-notice-title">' . s20_kses_inline( $heading ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( $text ) {
		echo '<div class="s20-notice-text">';
		s20_rich_text( $text );
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Page header for inner pages (title + optional intro).
 */
function s20_page_title( $title, $intro = '', $tag = 'h1' ) {
	if ( '' === trim( (string) $title ) ) {
		return;
	}
	echo '<header class="s20-page-header">';
	s20_section_heading( $title, $intro, $tag, 's20-page-title' );
	echo '</header>';
}

/**
 * Post meta (date / category) for cards and single posts.
 */
function s20_post_meta( $show_date = true, $show_cat = true ) {
	if ( ! $show_date && ! $show_cat ) {
		return;
	}
	echo '<div class="s20-post-meta">';
	if ( $show_date ) {
		echo '<time datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . esc_html( get_the_date() ) . '</time>';
	}
	if ( $show_cat ) {
		$cats = get_the_category();
		if ( ! empty( $cats ) ) {
			echo '<a class="s20-post-cat" href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
		}
	}
	echo '</div>';
}

/**
 * Pagination.
 */
function s20_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => s20_icon( 'arrow-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'guru-bhai-khaiwal' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'guru-bhai-khaiwal' ) . '</span>' . s20_icon( 'arrow-right' ),
			'class'     => 's20-pagination',
		)
	);
}

/**
 * Header date badge text.
 */
function s20_header_date_text() {
	$custom = s20_get_option( 'header_date_text', '' );
	if ( $custom ) {
		return $custom;
	}
	$format = s20_get_option( 'header_date_format', 'd M Y' );
	return wp_date( $format ? $format : 'd M Y' );
}
