<section <?php topten_block_id(); ?> class="accordion-block">
	<div class="grid">
		<?php topten_block_title(); ?>

		<?php if ( have_rows( 'accordion' ) ) : ?>
			<div class="content">
				<?php 
				while ( have_rows( 'accordion' ) ) :
					the_row(); 
					?>
					<?php $id = $block['id'] . '-' . get_row_index(); ?>

					<button type="button"
						class="accordion-title"
						aria-expanded="false"
						aria-controls="accordion-text-<?php echo esc_attr( $id ); ?>"
						id="accordion-title-<?php echo esc_attr( $id ); ?>">
						<span class="h4">
							<?php the_sub_field( 'accordion_title' ); ?>
						</span>

						<span class="material-symbols" aria-hidden="true">
							arrow_drop_down
						</span>
					</button>

					<div class="accordion-text 
					<?php 
					if ( ! get_sub_field( 'image' ) ) {
						echo 'no-image'; } 
					?>
					" id="accordion-text-<?php echo esc_attr( $id ); ?>">
						<?php 
						if ( get_sub_field( 'image' ) ) :
							$image = get_sub_field( 'image' );
							$url   = $image['url'];
							$title = $image['title'];
							$alt   = $image['alt'];
							$large = $image['sizes']['large'];
							$full  = $image['sizes']['fullhd'];
							?>
							<div class="image-wrapper">
								<a href="<?php echo esc_url( $full ); ?>" aria-label="<?php echo esc_html( $title ); ?>">
									<img src="<?php echo esc_url( $large ); ?>" alt="<?php echo esc_html( $alt ); ?>"/>
								</a>
							</div>
						<?php endif; ?>
						<div class="text-wrapper">
							<?php the_sub_field( 'text' ); ?>

							<?php if ( have_rows( 'buttons' ) ) : ?>
								<div class="links">
								<?php 
								while ( have_rows( 'buttons' ) ) : 
									the_row(); 
									$link = get_sub_field( 'button' );
									if ( $link ) : 
										$link_url    = $link['url'];
										$link_text   = $link['title'];
										$link_title  = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
										
										?>

										<a class="link"
											href="<?php echo esc_url( $link_url ); ?>"
											target="<?php echo esc_attr( $link_target ); ?>"
											aria-label="<?php echo esc_attr( $link_title ); ?>">
											<span class="link-text">
											<?php the_sub_field( 'button_text' ); ?>
											</span>
										</a>
									<?php endif; ?>
								<?php endwhile; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

