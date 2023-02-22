<?php
$text_block = get_field( 'text_block' );
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
				<?php echo $text_block['text']; ?>
			<?php endif; ?>

			<?php groteski_buttons( $text_block ); ?>
		</div>

		<div class="image-block <?php echo $order; ?>">
			<?php if ( ! empty( $image_block['image'] ) ) : ?>
				<?php $src = esc_url( $image_block['image']['sizes']['large'] );
				$alt = esc_attr( $image_block['image']['alt'] ); ?>

				<img class="<?php groteski_focal_point( false, $image_block ); ?>" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" />
			<?php endif; ?>
		</div>
	</div>
</section>
