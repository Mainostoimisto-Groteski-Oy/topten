<?php

$type      = get_field( 'type' );
$args      = array(
	'post_type'      => $type,
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'status'         => 'publish',
);
$postslist = get_posts( $args );
if ( get_field( 'carousel' ) ) {
	$class = 'logos-block carousel';
} else {
	$class = 'logos-block';
}
if ( empty( topten_block_title( false ) ) && empty( get_field( 'description' ) ) || empty( $postslist ) ) {
	$rows = 1;
	$gap  = 'auto';
} else {
	$rows = 2;
	$gap  = 'none';
}

?>
<section <?php topten_block_id(); ?> class="<?php echo esc_attr( $class ); ?>">
	<?php if ( get_field( 'carousel' ) ) : ?>
		<?php // this if statement is basically a completely different block from the original implementation ?>
	<div class="grid rows-<?php echo esc_attr( $rows ); ?> gap-<?php echo esc_attr( $gap ); ?>">
		<?php if ( ! empty( topten_block_title( false ) ) || ! empty( get_field( 'description' ) ) ) : ?>

		<div class="text-block">

			<?php if ( ! empty( topten_block_title( false ) ) ) : ?>
				<?php topten_block_title(); ?>
			<?php endif; ?>

			<?php if ( get_field( 'description' ) ) : ?>
				<div class="description">
					<?php the_field( 'description' ); ?>
				</div>
			<?php endif; ?>

		</div>

			<?php if ( $postslist ) : ?>
		<div class="carousel-wrapper">
			<div class="splide carousel logos">
				<div class="splide__track">
					<ul class="splide__list">
						<li class="splide__slide">
							<div class="big-wrapper">
								<?php
								$amount = count( $postslist );

								foreach ( $postslist as $index => $post ) :
									if ( $index % 18 === 0 && 0 !== $index ) :
										?>
									</div>
									</li>

									<li class="splide__slide">
										<div class="big-wrapper">
										<?php
									endif;

									$id = $post->ID;
									setup_postdata( $post );

									$logo = get_field( $type . '_logo', $id );
									$name = get_field( $type . '_nimi', $id );
									?>
									<div class="wrapper
										<?php
										if ( ! $logo ) {
											echo 'text-only'; }
										?>
										">
										<?php
										if ( $logo ) :
											$src = esc_url( $logo['sizes']['medium'] );
											$alt = esc_attr( $logo['alt'] );
											$img = sprintf( '<img src="%s" alt="%s">', $src, $alt );

											$link = get_field( $type . '_url', $id );

											if ( $link ) :
												echo sprintf( '<a class="logo" href="%s" target="_blank">%s<span class="screen-reader-text">%s</span></a>', esc_url( $link ), wp_kses_post( $img ), esc_html__( 'Linkki aukeaa uuteen ikkunaan', 'topten' ) );
												else :
													echo sprintf( '<div class="logo">%s</div>', wp_kses_post( $img ) );
												endif;
										elseif ( $name && ! $logo ) :
											$name = get_field( $type . '_nimi', $id );
											$link = get_field( $type . '_url', $id );
											if ( $link ) :

												echo sprintf( '<a class="logo text" href="%s" target="_blank">%s<span class="screen-reader-text">%s</span></a>', esc_url( $link ), wp_kses_post( $name ), esc_html__( 'Linkki aukeaa uuteen ikkunaan', 'topten' ) );
											else :
												echo sprintf( '<p class="logo text">%s</p>', wp_kses_post( $name ) );
											endif;
										endif;
										?>
									</div>
									<?php
								endforeach;
								wp_reset_postdata();
								?>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>




	<?php else : ?>
		<div class="grid rows-<?php echo esc_attr( $rows ); ?> gap-<?php echo esc_attr( $gap ); ?>">
			<?php if ( ! empty( topten_block_title( false ) ) || ! empty( get_field( 'description' ) ) ) : ?>

				<div class="text-block">

					<?php if ( ! empty( topten_block_title( false ) ) ) : ?>
						<?php topten_block_title(); ?>
					<?php endif; ?>

					<?php if ( get_field( 'description' ) ) : ?>
						<div class="description">
							<?php the_field( 'description' ); ?>
						</div>
					<?php endif; ?>

				</div>

			<?php endif; ?>

			<?php if ( $postslist ) : ?>

				<div class="logos">

					<?php
					foreach ( $postslist as $post ) :
						$id = $post->ID;
						setup_postdata( $post );

						$logo = get_field( $type . '_logo', $id );
						$name = get_field( $type . '_nimi', $id );

						if ( $logo ) :
							$src = esc_url( $logo['sizes']['medium'] );
							$alt = esc_attr( $logo['alt'] );
							$img = sprintf( '<img src="%s" alt="%s">', $src, $alt );

							$link = get_field( $type . '_url', $id );

							if ( $link ) :
								echo sprintf( '<a class="logo" href="%s" target="_blank">%s<span class="screen-reader-text">%s</span></a>', esc_url( $link ), wp_kses_post( $img ), esc_html__( 'Linkki aukeaa uuteen ikkunaan', 'topten' ) );
							else :
								echo sprintf( '<div class="logo">%s</div>', wp_kses_post( $img ) );
							endif;
						elseif ( $name && ! $logo ) :
							$name = get_field( $type . '_nimi', $id );
							$link = get_field( $type . '_url', $id );
							if ( $link ) :

								echo sprintf( '<a class="logo text" href="%s" target="_blank">%s<span class="screen-reader-text">%s</span></a>', esc_url( $link ), wp_kses_post( $name ), esc_html__( 'Linkki aukeaa uuteen ikkunaan', 'topten' ) );
							else :
								echo sprintf( '<p class="logo text">%s</p>', wp_kses_post( $name ) );
							endif;
						endif;
					endforeach;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
