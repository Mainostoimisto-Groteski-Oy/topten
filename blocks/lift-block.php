<?php
$background_url   = '';
$background_image = get_field( 'background_image' );


if ( $background_image ) {
	$background_url = 'background-image: url(' . $background_image['sizes']['qhd'] . ')';
} else {
	$background_url = '';
}
?>

<section <?php topten_block_id(); ?> class="lift-block" >
	<div class="grid <?php topten_focal_point(); ?>" style="<?php echo esc_url( $background_url ); ?>">
		<div class="content">
			<?php topten_block_title(); ?>
			<?php if ( get_field( 'text' ) ) : ?>
				<div class="text">
					<?php the_field( 'text' ); ?>
				</div>
			<?php endif; ?>

			<?php topten_buttons(); ?>
		</div>
	</div>
</section>
