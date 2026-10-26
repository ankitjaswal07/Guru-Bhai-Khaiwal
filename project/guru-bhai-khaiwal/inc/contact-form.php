<?php
/**
 * Built-in fallback contact form (used when no form plugin shortcode is configured).
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the form (or the configured form plugin shortcode).
 */
function s20_contact_form() {
	$shortcode = s20_get_option( 'contact_form_shortcode', '' );
	if ( '' !== trim( (string) $shortcode ) ) {
		echo '<div class="s20-form-plugin">' . s20_shortcode_output( $shortcode ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	$status = isset( $_GET['s20_contact'] ) ? sanitize_key( wp_unslash( $_GET['s20_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'sent' === $status ) {
		echo '<p class="s20-form-msg s20-form-msg--ok">' . esc_html( s20_get_option( 'contact_form_success', 'Thank you! Your message has been sent.' ) ) . '</p>';
	} elseif ( 'error' === $status ) {
		echo '<p class="s20-form-msg s20-form-msg--error">' . esc_html( s20_get_option( 'contact_form_error', 'Sorry, something went wrong. Please try again.' ) ) . '</p>';
	}
	?>
	<form class="s20-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="s20_contact" />
		<input type="hidden" name="s20_redirect" value="<?php echo esc_url( get_permalink() ); ?>" />
		<?php wp_nonce_field( 's20_contact_form', 's20_contact_nonce' ); ?>
		<p class="s20-form-hp"><label>Website<input type="text" name="s20_website" tabindex="-1" autocomplete="off" /></label></p>
		<p><label class="screen-reader-text" for="s20-name"><?php esc_html_e( 'Name', 'guru-bhai-khaiwal' ); ?></label><input id="s20-name" type="text" name="s20_name" placeholder="<?php esc_attr_e( 'Name', 'guru-bhai-khaiwal' ); ?>" required /></p>
		<p><label class="screen-reader-text" for="s20-email"><?php esc_html_e( 'Email', 'guru-bhai-khaiwal' ); ?></label><input id="s20-email" type="email" name="s20_email" placeholder="<?php esc_attr_e( 'Email', 'guru-bhai-khaiwal' ); ?>" required /></p>
		<p><label class="screen-reader-text" for="s20-message"><?php esc_html_e( 'Message', 'guru-bhai-khaiwal' ); ?></label><textarea id="s20-message" name="s20_message" rows="5" placeholder="<?php esc_attr_e( 'Message', 'guru-bhai-khaiwal' ); ?>" required></textarea></p>
		<p><button type="submit" class="s20-btn s20-btn--block"><?php echo esc_html( s20_get_option( 'contact_form_button_text', 'Send' ) ); ?></button></p>
	</form>
	<?php
}

/**
 * Handle submission.
 */
function s20_handle_contact_form() {
    if('POST'!==($_SERVER['REQUEST_METHOD']??''))wp_die('POST required.','',array('response'=>405));
    $bucket='s20_contact_'.hash_hmac('sha256',(string)($_SERVER['REMOTE_ADDR']??'unknown'),wp_salt());
    $attempts=(int)get_transient($bucket);
    if($attempts>=3)wp_die('Please wait five minutes before trying again.','',array('response'=>429));
    set_transient($bucket,$attempts+1,5*MINUTE_IN_SECONDS);

	$redirect = isset( $_POST['s20_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['s20_redirect'] ) ) : home_url( '/' );
	if ( ! wp_http_validate_url( $redirect ) || 0 !== strpos( $redirect, home_url() ) ) {
		$redirect = home_url( '/' );
	}

	$ok = isset( $_POST['s20_contact_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['s20_contact_nonce'] ) ), 's20_contact_form' );
	$honeypot = isset( $_POST['s20_website'] ) ? sanitize_text_field( wp_unslash( $_POST['s20_website'] ) ) : '';

	$name    = isset( $_POST['s20_name'] ) ? sanitize_text_field( wp_unslash( $_POST['s20_name'] ) ) : '';
	$email   = isset( $_POST['s20_email'] ) ? sanitize_email( wp_unslash( $_POST['s20_email'] ) ) : '';
	$message = isset( $_POST['s20_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['s20_message'] ) ) : '';

	if ( ! $ok || $honeypot || ! $name || ! is_email( $email ) || ! $message || strlen($name)>200 || strlen($message)>10000 ) {
		wp_safe_redirect( add_query_arg( 's20_contact', 'error', $redirect ) );
		exit;
	}

	$to      = s20_get_option( 'contact_form_recipient', '' );
	$to      = is_email( $to ) ? $to : get_option( 'admin_email' );
	$subject = sprintf( '[%s] %s', wp_specialchars_decode( get_bloginfo( 'name' ) ), __( 'New contact form message', 'guru-bhai-khaiwal' ) );
	$body    = sprintf( "Name: %s\nEmail: %s\n\n%s", $name, $email, $message );
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );
	wp_safe_redirect( add_query_arg( 's20_contact', $sent ? 'sent' : 'error', $redirect ) );
	exit;
}
add_action( 'admin_post_s20_contact', 's20_handle_contact_form' );
add_action( 'admin_post_nopriv_s20_contact', 's20_handle_contact_form' );
