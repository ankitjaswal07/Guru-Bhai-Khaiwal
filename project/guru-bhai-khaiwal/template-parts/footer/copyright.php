<?php
/**
 * Copyright bar.
 *
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$s20_copy  = s20_placeholders( s20_get_option( 'copyright_text', '© {year} {site} | All Rights Reserved.' ) );
$s20_extra = s20_get_option( 'footer_extra_text', '' );
?>
<div class="s20-footer-bottom">
	<div class="s20-container">
		<?php if ( $s20_extra ) : ?><p class="s20-footer-extra"><?php s20_text( nl2br( $s20_extra ) ); ?></p><?php endif; ?>
		<p class="s20-copyright"><?php s20_text( $s20_copy ); ?></p>
	</div>
</div>
