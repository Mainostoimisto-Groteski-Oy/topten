<?php
$image = get_field( 'image' );

if ( get_field( 'description' ) ) : ?>
	<div class="desc">
		<?php the_field( 'description' ); ?>
	</div>
	<?php
endif;

if ( $image ) :
	$src = $image['sizes']['medium'];
	$alt = $image['alt'];

	echo sprintf( '<div class="image-wrapper"><img src="%1$s" alt="%2$s" /></div>', esc_url( $src ), esc_attr( $alt ) );
endif;
