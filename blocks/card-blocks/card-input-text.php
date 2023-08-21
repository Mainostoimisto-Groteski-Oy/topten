<?php
$block_id = $block['id'];

$description = get_field( 'description' );

$required = get_field( 'required' );

$prefix = get_field( 'prefix' );
$suffix = get_field( 'suffix' );
?>

<div class="column-item input-wrapper centered" style="<?php topten_get_block_width(); ?>">
	<label class="text-field" for="<?php echo esc_attr( $block_id ); ?>">
		<span class="label-text">
			<?php echo esc_html( $description ); ?>

			<?php if ( $required ) : ?>
				<span class="required">
					*
				</span>
			<?php endif; ?>
		</span>

		<div class="input-field-wrapper">
			<?php if ( $prefix ) : ?>
				<span class="prefix">
					<?php echo esc_html( $prefix ); ?>
				</span>
			<?php endif; ?>

			<input id="<?php echo esc_attr( $block_id ); ?>" type="text" <?php echo $required ? 'required' : ''; ?> />

			<?php if ( $suffix ) : ?>
				<span class="suffix">
					<?php echo esc_html( $suffix ); ?>
				</span>
			<?php endif; ?>
		</div>
	</label>
</div>
