<?php
$text_block = get_field( 'text_block' );
$order      = get_field( 'order' );

?>

<section <?php topten_block_id(); ?> class="text-and-form-block">
	<div class="grid">
		<div class="text-block">
			<?php topten_block_title(); ?>

			<?php if ( ! empty( $text_block['text'] ) ) : ?>
				<?php echo wp_kses_post( $text_block['text'] ); ?>
			<?php endif; ?>

			<?php topten_buttons( $text_block ); ?>
		</div>

		<div class="form-block <?php echo esc_attr( $order ); ?>">
			<?php if ( ! empty( get_field( 'form' ) ) ) : ?>
				<?php $formID = get_field( 'form' ); ?>
				<?php echo do_shortcode( '[gravityform id="' . esc_attr( $formID ) . '" title="false" description="false" ajax="true"]' ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
