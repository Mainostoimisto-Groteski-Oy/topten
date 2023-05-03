<?php
$block_id = $block['id'];

$description = get_field( 'description' );
?>

<div class="input-wrapper checkboxes-wrapper">
	<?php if ( $description ) : ?>
		<p class="description">
			<?php echo esc_html( $description ); ?>
		</p>
	<?php endif; ?>

	<?php if ( have_rows( 'checkboxes' ) ) : ?>
		<div class="checkboxes">
			<?php while ( have_rows( 'checkboxes' ) ) : ?>
				<?php
				the_row();

				$index = $block_id . ' - ' . get_row_index();

				$label = get_sub_field( 'label' );
				?>
				<label class="checkbox-field" for="<?php echo esc_attr( $index ); ?>">
					<input id="<?php echo esc_attr( $index ); ?>"
						type="checkbox"
						value="<?php esc_html( $label ); ?>" />

					<?php echo esc_html( $label ); ?>
				</label>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
