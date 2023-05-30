<?php
$text_block  = get_field( 'text_block' );
$image_block = get_field( 'image_block' );
$image_height = $image_block['image_height'];

if( $image_height ) :
	$image_height = 'image-height-' . $image_height;
else :
	$image_height = 'image-height-normal';
endif;

if ( $image_block['order'] ) :
	$order = 'left';
else :
	$order = '';
endif;

?>

<section <?php topten_block_id(); ?> class="text-and-image-block">
	<div class="grid">
		<div class="text-block">
			<?php topten_block_title(); ?>

			<?php if ( ! empty( $text_block['text'] ) ) : ?>
				<?php echo wp_kses_post( $text_block['text'] ); ?>
			<?php endif; ?>

			<?php topten_buttons( $text_block ); ?>
		</div>

		<div class="image-block <?php echo esc_attr( $order ).' '.esc_attr( $image_height ); ?>">
			<?php if ( ! empty( $image_block['image'] ) ) : ?>
				<?php
				$src = $image_block['image']['sizes']['large'];
				$alt = $image_block['image']['alt'];
				?>

				<img class="<?php topten_focal_point( false, $image_block ); ?>" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
			<?php endif; ?>
		</div>
	</div>
</section>
