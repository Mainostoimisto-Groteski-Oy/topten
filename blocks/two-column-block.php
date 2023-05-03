<?php
	$left_block  = get_field( 'left_block' );
	$right_block = get_field( 'right_block' );
?>

<section <?php topten_block_id(); ?> class="two-column-block">
	<div class="grid">
		<?php topten_block_title(); ?>

		<div class="left-block bg-<?php echo esc_attr( $left_block['generated_background_color'] ); ?> color-<?php  echo esc_attr( $left_block['generated_text_color'] ); ?>">
			<?php if ( ! empty( $left_block['title'] ) ) : ?>
				<h3 class="block-title h3">
					<?php echo wp_kses_post( $left_block['title'] ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $left_block['text'] ) ) : ?>
				<?php echo wp_kses_post( $left_block['text'] ); ?>
			<?php endif; ?>

			<?php topten_buttons( $left_block ); ?>
		</div>

		<div class="right-block bg-<?php echo esc_attr( $right_block['generated_background_color'] ); ?> color-<?php  echo esc_attr( $right_block['generated_text_color'] ); ?>">
			<?php if ( ! empty( $right_block['title'] ) ) : ?>
				<h3 class="block-title h3">
					<?php echo wp_kses_post( $right_block['title'] ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $right_block['text'] ) ) : ?>
				<?php echo wp_kses_post( $right_block['text'] ); ?>
			<?php endif; ?>

			<?php topten_buttons( $right_block ); ?>
		</div>
	</div>
</section>
