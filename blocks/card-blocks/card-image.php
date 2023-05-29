<?php
$image = get_field( 'image' );

if ( ! $image ) {
	return;
}

$src = $image['sizes']['large'];
$alt = $image['alt'];

$caption = get_field( 'caption' );
?>

<?php topten_get_desc(); ?>

<figure class="image-wrapper">
	<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />

	<?php if ( $caption ) : ?>
		<figcaption>
			<?php echo wp_kses_post( $caption ); ?>
		</figcaption>
	<?php endif; ?>
</figure>
