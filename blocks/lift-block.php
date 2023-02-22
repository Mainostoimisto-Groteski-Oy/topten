<?php
$background_url = '';
$background_image = get_field( 'background_image' );

if ( $background_image ) {
	$background_url = 'background-image: url(' . esc_url( $background_image['sizes']['qhd'] ) . ')';
} else {
	$background_url = '';
}
?>

<section <?php groteski_block_id(); ?> class="lift-block?> <?php groteski_focal_point(); ?>" style="<?php echo $background_url; ?>">
	<div class="grid">
		<?php groteski_block_title(); ?>

		<?php groteski_buttons(); ?>
	</div>
</section>