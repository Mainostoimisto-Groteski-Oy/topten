<?php
$block_id = $block['id'];

$description = get_field( 'description' );
?>

<div class="input-wrapper">
	<label class="textarea-field" for="<?php echo esc_attr( $block_id ); ?>">
		<?php echo esc_html( $description ); ?>

		<textarea id="<?php echo esc_attr( $block_id ); ?>"></textarea>
	</label>
</div>
