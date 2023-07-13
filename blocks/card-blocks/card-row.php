<?php
// $columns = get_field( 'columns' ) ?: 1;

global $card_allowed_blocks;

$allowed_blocks = $card_allowed_blocks;

foreach ( $allowed_blocks as $index => $allowed_block ) {
	if ( 'acf/rivi' === $allowed_block || 'acf/sarake' === $allowed_block ) {
		unset( $allowed_blocks[ $index ] );
	}
}

$allowed_blocks = array_values( $allowed_blocks );

// $template = array();

// for ( $i = 0; $i < $columns; $i++ ) {
// $template[] = array( 'acf/sarake' );
// }

// $allowed_blocks = array(
// 'acf/sarake',
// );

// $width_selector = $columns . '_column_width';

// $column_widths = get_field( $width_selector ) ?: '[]';
// $column_widths = json_decode( $column_widths );

// $css_vars = '';

// if ( is_countable( $column_widths ) ) {
// foreach ( $column_widths as $index => $column_width ) {
// ++$index;

// $css_vars .= '--column-' . $index . ': ' . $column_width . 'fr;';
// }
// }
?>

<section class="row-block">
	<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" templateLock="false" />
</section>
