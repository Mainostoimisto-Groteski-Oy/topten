<?php
global $card_allowed_blocks;

$allowed_blocks = $card_allowed_blocks;

foreach ( $allowed_blocks as $index => $allowed_block ) {
	if ( 'acf/rivi' === $allowed_block || 'acf/sarake' === $allowed_block ) {
		unset( $allowed_blocks[ $index ] );
	}
}

$allowed_blocks = array_values( $allowed_blocks );
?>

<div class="column">
	<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" templateLock="false" />
</div>
