<?php
$block_id = $block['id'];

$description = get_field( 'description' );

$required = get_field( 'required' );
?>

<div class="input-wrapper radios-wrapper column" style="<?php topten_get_block_width(); ?>">
	<?php if ( $description ) : ?>
		<p class="description">
			<?php echo esc_html( $description ); ?>

			<?php if ( $required ) : ?>
				<span class="required">
					*
				</span>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if ( have_rows( 'radios' ) ) : ?>
		<div class="radios">
			<?php while ( have_rows( 'radios' ) ) : ?>
				<?php
				the_row();

				$index = $block_id . ' - ' . get_row_index();

				$label = get_sub_field( 'label' );
				?>
				<label class="radio-field" for="<?php echo esc_attr( $index ); ?>">
					<input id="<?php echo esc_attr( $index ); ?>"
						name="<?php echo esc_attr( $block_id ); ?>"
						type="radio"
						value="<?php esc_html( $label ); ?>"
						<?php echo $required ? 'required' : ''; ?> />

					<?php echo esc_html( $label ); ?>
				</label>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
