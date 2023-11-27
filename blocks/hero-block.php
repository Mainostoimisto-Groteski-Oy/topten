<?php
$select         = get_field( 'hero_type' );
$background_url = '';

if ( 'image' === $select || 'image-text-two-column' === $select ) {

	$background_image = get_field( 'background_image' );

	if ( $background_image ) {
		$background_url = 'background-image: url(' . esc_url( $background_image['sizes']['qhd'] ) . ')';
	} else {
		$background_url = '';
	}
}

if ( 'image-text' === $select ) {

	$background_image = get_field( 'hero_image' );

	if ( $background_image ) {
		$background_url = 'background-image: url(' . esc_url( $background_image['sizes']['fullhd'] ) . ')';
	} else {
		$background_url = '';
	}
}


$class = 'hero-block hero-' . $select;
?>

<section <?php topten_block_id(); ?> class="<?php echo esc_attr( $class ); ?> <?php topten_focal_point(); ?>">
	<?php if ( ( 'image' === $select || 'image-text-two-column' === $select ) && ! empty( $background_url ) ) : ?>
		<div class="hero-image-wrapper">
			<img src="<?php echo esc_url( $background_image['sizes']['qhd'] ); ?>" alt="<?php echo esc_attr( $background_image['alt'] ); ?>" />
		</div>
	<?php endif; ?>

	<?php if ( 'image' === $select ) : ?>
		<div class="hero-content-wrapper">
			<div class="grid">
				<div class="text-block">
					<?php topten_block_title(); ?>

					<?php topten_buttons(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( 'image-text' === $select ) : ?>
		<div class="hero-content-wrapper">
			<div class="
				<?php
				if ( get_field( 'smaller_grid' ) ) {
					echo 'grid';
				} else {
					echo 'grid-full';}
				?>
				<?php
				if ( get_field( 'hero_order' ) === 'image-right' ) {
						echo 'right'; }
				?>
				">

				<div class="image-block
					<?php
					if ( get_field( 'hero_order' ) === 'image-right' ) {
						echo 'right'; }
					?>
					" style="<?php echo ( 'image-text' === $select && $background_url ) ? esc_attr( $background_url ) : ''; ?>">
				</div>

				<div class="text-block
					<?php
					if ( get_field( 'hero_order' ) === 'image-right' ) {
						echo 'image-right'; }
					?>
					<?php
					if ( get_field( 'less_padding' ) ) {
						echo ' less-padding'; }
					?>
					">
					<?php if ( get_field( 'hero_logo' ) ) : ?>
						<div class="logo">
							<?php $logo = get_field( 'hero_logo' ); ?>
							<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
						</div>
					<?php endif; ?>

					<?php topten_block_title(); ?>

					<?php if ( get_field( 'hero_ingress' ) ) : ?>
						<div class="ingress">
							<?php the_field( 'hero_ingress' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( get_field( 'hero_text' ) ) : ?>
						<div class="text">
							<?php the_field( 'hero_text' ); ?>
						</div>
					<?php endif; ?>

					<?php topten_buttons(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( 'image-text-two-column' === $select ) : ?>
		<div class="hero-content-wrapper">
			<div class="grid-full">
				<div class="text-block">
					<?php if ( get_field( 'hero_logo' ) ) : ?>
						<div class="logo">
							<?php $logo = get_field( 'hero_logo' ); ?>
							<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
						</div>
					<?php endif; ?>

					<?php topten_block_title(); ?>

					<?php if ( get_field( 'hero_ingress' ) ) : ?>
						<div class="ingress">
							<?php the_field( 'hero_ingress' ); ?>
						</div>
					<?php endif; ?>

					<div class="hero-text-content">
						<div class="left">
							<?php if ( get_field( 'hero_text_left' ) ) : ?>
								<div class="text">
									<?php the_field( 'hero_text_left' ); ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="right">
							<?php if ( get_field( 'hero_text_right' ) ) : ?>
								<div class="text">
									<?php the_field( 'hero_text_right' ); ?>
								</div>
							<?php endif; ?>
						</div>

						<?php topten_buttons(); ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>

<?php if ( get_field( 'show_breadcrumb', 'options' ) ) : ?>
	<div id="hero-breadcrumb" class="breadcrumb">
		<div class="grid">
			<?php echo do_shortcode( '[wpseo_breadcrumb]' ); ?>
		</div>
	</div>
<?php endif; ?>
