<?php
$block_id = get_field( 'block_id' );

$description = get_field( 'description' );

$required = get_field( 'required' );

$direction       = get_field( 'direction' );
$text_before     = get_field( 'text_before_radios' );
$responsive_only = get_field( 'text_before_radios_responsive_only' );

if ( ! $direction ) {
	$direction = 'horizontal';
}

$prevent_save = get_field( 'prevent_save' );
?>

<div class="column-item input-wrapper radios-wrapper <?php echo esc_attr( $direction ); ?> <?php echo $prevent_save ? 'prevent-save' : ''; ?>" style="<?php topten_get_block_width(); ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">
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
			<?php if ( get_field( 'text_before_radios' ) ) : ?>
				<span class="text-before <?php echo $responsive_only ? 'responsive-only' : ''; ?>">
					<?php gro_the_field( 'text_before_radios' ); ?>
				</span>
			<?php endif; ?>

			<?php while ( have_rows( 'radios' ) ) : ?>
				<?php
				the_row();

				$index = $block_id . '-' . get_row_index();

				$label = get_sub_field( 'label' );
				?>
				<label class="radio-field" for="<?php echo esc_attr( $index ); ?>">
					<input class="<?php echo $prevent_save ? 'prevent-save' : ''; ?>"
						id="<?php echo esc_attr( $index ); ?>"
						name="<?php echo esc_attr( $block_id ); ?>"
						type="radio"
						value="<?php esc_html( $label ); ?>"
						<?php echo $required ? 'required' : ''; ?> />

					<span class="label-text">
						<?php echo esc_html( $label ); ?>
					</span>
				</label>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
