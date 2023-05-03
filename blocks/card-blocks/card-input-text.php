<?php
$block_id = $block['id'];

$description = get_field( 'description' );
?>

<div class="input-wrapper">
	<label class="text-field" for="<?php echo esc_attr( $block_id ); ?>">
		<?php echo esc_html( $description ); ?>

		<input id="<?php echo esc_attr( $block_id ); ?>" type="text" />
	</label>
</div>
