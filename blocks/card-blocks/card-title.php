<?php topten_get_desc(); ?>

<?php
$title_tag = get_field( 'title_tag' ) ?: 'h2';
$title     = get_field( 'title' );

if ( $title ) :
	echo sprintf( '<div class="title-wrapper"><%1$s class="title" id="%3$s">%2$s</%1$s></div>', esc_attr( $title_tag ), wp_kses_post( $title ), esc_attr( sanitize_title( $title ) ) );
endif;
