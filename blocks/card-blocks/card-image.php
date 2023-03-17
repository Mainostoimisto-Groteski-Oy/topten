<?php
$image = get_field( 'image' );

if ( $image ) :
	$src = $image['sizes']['medium'];
	$alt = $image['alt'];

	echo sprintf( '<img src="%1$s" alt="%2$s" />', esc_url( $src ), esc_attr( $alt ) );
endif;
