<?php
$block_anchor = get_field( 'block_id' );
$image        = get_field( 'image' );

if ( ! $image ) {
	return;
}

$src = $image['sizes']['large'];
$alt = $image['alt'];

$caption = get_field( 'caption' );
?>


<div class="column-item image-wrapper" style="<?php topten_get_block_width(); ?>" data-block-id="<?php echo esc_attr( $block_anchor ); ?>">
	<?php topten_get_desc(); ?>

	<figure>
		<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />

		<?php if ( $caption ) : ?>
			<figcaption>
				<?php echo wp_kses_post( $caption ); ?>
			</figcaption>
		<?php endif; ?>
	</figure>
</div>
