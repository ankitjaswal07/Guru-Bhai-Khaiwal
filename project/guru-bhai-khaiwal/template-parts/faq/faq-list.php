<?php
/**
 * FAQ accordion list.
 *
 * @param array $args { items => array, limit => int }
 * @package Guru Bhai Khaiwal
 */

defined( 'ABSPATH' ) || exit;

$args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'items' => null, 'limit' => 0 ) );
$items = is_array( $args['items'] ) ? $args['items'] : s20_get_repeater( 'faq_items', s20_default_faq_items() );
$items = array_values( array_filter( $items, function ( $item ) { return ! empty( $item['question'] ) && ( ! isset( $item['active'] ) || $item['active'] ); } ) );
if ( $args['limit'] > 0 ) {
	$items = array_slice( $items, 0, (int) $args['limit'] );
}
if ( empty( $items ) ) {
	return;
}
$first_open  = s20_option_enabled( 'faq_first_open', true );
$single_open = s20_option_enabled( 'faq_single_open', false );
static $s20_faq_instance = 0;
$s20_faq_instance++;
?>
<div class="s20-faq" data-s20-faq data-single-open="<?php echo $single_open ? '1' : '0'; ?>">
	<?php foreach ( $items as $i => $item ) : ?>
		<?php $id = 's20-faq-' . $s20_faq_instance . '-' . ( $i + 1 ); ?>
		<details class="s20-faq-item" id="<?php echo esc_attr( $id ); ?>"<?php echo ( 0 === $i && $first_open ) ? ' open' : ''; ?>>
			<summary class="s20-faq-question">
				<span class="s20-faq-question-text"><?php s20_text( $item['question'] ); ?></span>
				<span class="s20-faq-icon" aria-hidden="true"><?php s20_the_icon( 'plus', 's20-faq-plus' ); ?><?php s20_the_icon( 'minus', 's20-faq-minus' ); ?></span>
			</summary>
			<div class="s20-faq-answer">
				<?php
				$answer = isset( $item['answer'] ) ? (string) $item['answer'] : '';
				echo wp_kses_post( false === strpos( $answer, '<p' ) ? wpautop( $answer ) : $answer );
				?>
			</div>
		</details>
	<?php endforeach; ?>
</div>
