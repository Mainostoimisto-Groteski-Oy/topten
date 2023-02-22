<?php
$block_id    = get_field( 'block_id' );
$description = get_field( 'description' );
$required    = get_field( 'required' );
$direction   = get_field( 'direction' );

if ( ! $direction ) {
	$direction = 'horizontal';
}

$prevent_save = get_field( 'prevent_save' );
?>

<div class="column-item input-wrapper checkboxes-wrapper <?php echo esc_attr( $direction ); ?> <?php echo $prevent_save ? 'prevent-save' : ''; ?>" style="<?php topten_get_block_width(); ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">
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

	<?php if ( have_rows( 'checkboxes' ) ) : ?>
		<div class="checkboxes">
			<?php while ( have_rows( 'checkboxes' ) ) : ?>
				<?php
				the_row();

				$index = $block_id . '-' . get_row_index();

				$label = get_sub_field( 'label' );
				?>
				<label class="checkbox-field" for="<?php echo esc_attr( $index ); ?>">
					<input class="<?php echo $prevent_save ? 'prevent-save' : ''; ?>"
						id="<?php echo esc_attr( $index ); ?>"
						type="checkbox"
						value="<?php esc_attr( $label ); ?>"
						<?php echo $required ? 'required' : ''; ?> />

					<span class="label-text">
						<?php echo esc_html( $label ); ?>
					</span>
				</label>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
