<?php
$block_id    = $block['id'];
$description = get_field( 'description' );
$rows        = get_field( 'textarea_rows' );

if ( ! $rows ) {
	$rows = 1;
}

$prevent_save = get_field( 'prevent_save' );
?>

<div class="column-item input-wrapper <?php echo $prevent_save ? 'prevent-save' : ''; ?>" style="<?php topten_get_block_width(); ?>">
	<label class="textarea-field" for="<?php echo esc_attr( $block_id ); ?>">
		<span class="label-text">
			<?php echo esc_html( $description ); ?>
		</span>

		<textarea class="<?php echo $prevent_save ? 'prevent-save' : ''; ?>" id="<?php echo esc_attr( $block_id ); ?>" rows="<?php echo esc_attr( $rows ); ?>"></textarea>
	</label>
</div>
