<?php if ( get_field( 'description' ) ) : ?>
	<h2 class="desc">
		<?php the_field( 'description' ); ?>
	</h2>
<?php endif; ?>

<?php
$title_tag = get_field( 'title_tag' ) ?: 'h2';

$title = get_field( 'title' );

if ( $title ) :
	echo sprintf( '<div class="title-wrapper"><%1$s class="title">%2$s</%1$s></div>', esc_attr( $title_tag ), wp_kses_post( get_field( 'title' ) ) );
endif;
