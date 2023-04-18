<?php if ( get_field( 'description' ) ) : ?>
	<h2 class="desc">
		<?php the_field( 'description' ); ?>
	</h2>
<?php endif; ?>

<?php
$image = get_field( 'image' );

if ( $image ) :
	$src = $image['sizes']['medium'];
	$alt = $image['alt'];

	echo sprintf( '<div class="image-wrapper"><img src="%1$s" alt="%2$s" /></div>', esc_url( $src ), esc_attr( $alt ) );
endif;
?>
