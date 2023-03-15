<?php
$select         = get_field( 'hero_type' );
$background_url = '';

if ( 'image' === $select ) {
	$background_image = get_field( 'background_image' );

	if ( $background_image ) {
		$background_url = 'background-image: url(' . esc_url( $background_image['sizes']['qhd'] ) . ')';
	} else {
		$background_url = '';
	}
}

$class = 'hero-block hero-' . $select;
?>

<section <?php groteski_block_id(); ?> class="<?php echo esc_attr( $class ); ?> <?php groteski_focal_point(); ?>">
	<?php if ( 'video' === $select ) : ?>
		<?php $background_video = get_field( 'background_video' ); ?>

		<video autoplay loop muted preload="auto" width="100%" height="100%">
			<?php if ( ! empty( $background_video['webm'] ) ) : ?>
				<source src="<?php echo esc_url( $background_video['webm']['url'] ); ?>" type="<?php echo esc_attr( $background_video['webm']['mime_type'] ); ?>">
			<?php endif; ?>

			<?php if ( ! empty( $background_video['mp4'] ) ) : ?>
				<source src="<?php echo esc_url( $background_video['mp4']['url'] ); ?>" type="<?php echo esc_attr( $background_video['mp4']['mime_type'] ); ?>">
			<?php endif; ?>
		</video>
	<?php endif; ?>

	<?php if ( 'image' === $select || 'video' === $select ) : ?>
		<div class="hero-content-wrapper">
			<div class="grid">
				<div class="text-block">
					<?php groteski_block_title(); ?>

					<?php groteski_buttons(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
	if ( 'carousel' === $select ) :
		if ( have_rows( 'slides' ) ) :
			?>
			<div class="splide carousel" aria-labelledby="<?php echo esc_attr( $block['id'] . '_title' ); ?>">
				<div class="splide__track">
					<ul class="splide__list">
						<?php
						while ( have_rows( 'slides' ) ) :
							the_row();
							?>
							<?php $image = get_sub_field( 'image' ); ?>

							<?php if ( $image ) : ?>
								<?php
								$src = esc_url( $image['sizes']['qhd'] );
								$alt = esc_attr( $image['alt'] );
								?>

								<li class="splide__slide">
									<img class="<?php groteski_focal_point( true ); ?>"
										src="<?php echo esc_url( $src ); ?>"
										alt="<?php echo esc_attr( $alt ); ?>" />
								</li>
							<?php endif; ?>
						<?php endwhile; ?>
					</ul>
				</div>
			</div>

			<div class="hero-content-wrapper">
				<div class="grid">
					<div class="text-block">
						<?php groteski_block_title(); ?>

						<?php groteski_buttons(); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</section>

<?php if ( get_field( 'show_breadcrumb', 'options' ) ) : ?>
	<div id="hero-breadcrumb" class="breadcrumb">
		<div class="grid">
			<?php echo do_shortcode( '[wpseo_breadcrumb]' ); ?>
		</div>
	</div>
<?php endif; ?>
