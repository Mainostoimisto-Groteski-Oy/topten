<?php
$text_block  = get_field( 'text_block' );
$image_block = get_field( 'image_block' );

if ( $image_block['order'] ) :
	$order = 'left';
else :
	$order = '';
endif;
?>

<section <?php groteski_block_id(); ?> class="text-and-image-block">
	<div class="grid">
		<div class="text-block">
			<?php groteski_block_title(); ?>

			<?php if ( ! empty( $text_block['text'] ) ) : ?>
				<?php echo wp_kses_post( $text_block['text'] ); ?>
			<?php endif; ?>

			<?php groteski_buttons( $text_block ); ?>
		</div>

		<div class="image-block <?php echo esc_attr( $order ); ?>">
			<?php if ( ! empty( $image_block['image'] ) ) : ?>
				<?php
				$src = $image_block['image']['sizes']['large'];
				$alt = $image_block['image']['alt'];
				?>

				<img class="<?php groteski_focal_point( false, $image_block ); ?>" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
			<?php endif; ?>
		</div>
	</div>
</section>
