<?php
/**
 * Comments template.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="s20-comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="s20-comments-title"><?php printf( esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'guru-bhai-khaiwal' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></h2>
		<ol class="s20-comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48 ) ); ?></ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form( array( 'class_submit' => 's20-btn' ) ); ?>
</div>
