<?php
$title_tag = get_field( 'title_tag' ) ?: 'h2';

$title = get_field( 'title' );

if ( $title ) :
	echo sprintf( '<%1$s class="card-title">%2$s</%1$s>', esc_attr( $title_tag ), wp_kses_post( get_field( 'title' ) ) );
endif;
