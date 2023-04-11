<?php
$title_tag = get_field( 'title_tag' ) ?: 'h2';

$title = get_field( 'title' );

if(get_field('description')) : ?>
    <div class="desc">
        <?php the_field('description'); ?>
    </div>
    <?php
endif;

if ( $title ) :
	echo sprintf( '<div class="title-wrapper"><%1$s class="title">%2$s</%1$s></div>', esc_attr( $title_tag ), wp_kses_post( get_field( 'title' ) ) );
endif;
