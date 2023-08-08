<?php
$block_id = $block['id'];

$description = get_field( 'description' );
?>

<div class="input-wrapper" style="<?php topten_get_block_width(); ?>">
	<label class="textarea-field" for="<?php echo esc_attr( $block_id ); ?>">
		<?php echo esc_html( $description ); ?>

		<textarea id="<?php echo esc_attr( $block_id ); ?>"></textarea>
	</label>
</div>
