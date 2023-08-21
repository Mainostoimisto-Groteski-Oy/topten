<?php
$block_id    = $block['id'];
$description = get_field( 'description' );
$rows        = get_field( 'textarea_rows' );

if ( ! $rows ) {
	$rows = 1;
}


?>

<div class="column-item input-wrapper" style="<?php topten_get_block_width(); ?>">
	<label class="textarea-field" for="<?php echo esc_attr( $block_id ); ?>">
		<?php echo esc_html( $description ); ?>

		<textarea id="<?php echo esc_attr( $block_id ); ?>" rows="<?php echo esc_attr( $rows ); ?>"></textarea>
	</label>
</div>
