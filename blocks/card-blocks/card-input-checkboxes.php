<?php
$block_id = $block['id'];

$description = get_field( 'description' );

$required = get_field( 'required' );

$direction = get_field( 'direction' );
if ( ! $direction ) {
	$direction = 'horizontal';
}
?>

<div class="column-item input-wrapper checkboxes-wrapper <?php echo esc_attr( $direction ); ?>" style="<?php topten_get_block_width(); ?>">
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
					<input id="<?php echo esc_attr( $index ); ?>"
						type="checkbox"
						value="<?php esc_attr( $label ); ?>"
						<?php echo $required ? 'required' : ''; ?> />

					<span>
						<?php echo esc_html( $label ); ?>
					</span>
				</label>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
