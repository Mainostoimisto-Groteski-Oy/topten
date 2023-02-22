<section <?php topten_block_id(); ?> class="accordion-block">
	<div class="grid">
		<?php topten_block_title(); ?>

		<?php if ( have_rows( 'accordion' ) ) : ?>
			<div class="content">
				<?php 
				while ( have_rows( 'accordion' ) ) :
					the_row(); 
					if ( 'false' !== get_sub_field( 'show_guide' ) ) : 
						?>
						<?php $prefix = get_sub_field( 'show_guide' ); ?>

						<button type="button"
						class="accordion-title"
						aria-expanded="false"
						aria-controls="accordion-text-<?php echo esc_attr( $prefix ); ?>"
						id="accordion-title-<?php echo esc_attr( $prefix ); ?>">
							<span class="h3">
								<?php the_field( $prefix . '_guide_title', 'options' ); ?>
							</span>

							<span class="material-symbols" aria-hidden="true">
								keyboard_double_arrow_down
							</span>
						</button>

						<div class="accordion-text"
						id="accordion-text-<?php echo esc_attr( $prefix ); ?>">
							<div class="text-wrapper">
							<?php the_field( $prefix . '_guide_before', 'options' ); ?>
							<?php 
							if ( get_field( $prefix . '_guide', 'options' ) ) :
								if ( have_rows( 'guide', 'options' ) ) :
									?>
										<div class="tulkinnat">
										<?php
										while ( have_rows( 'guide', 'options' ) ) :

											the_row();

											$icon  = get_sub_field( 'icon' );
											$color = get_sub_field( 'color' );
											$name  = get_sub_field( 'name' );
											?>
												<div class="tulkinta">
													<p class="<?php echo esc_html( $color ) . ' ' . esc_html( $icon ); ?>">
													<?php echo esc_html( $name ); ?>
													</p>
												</div>
												<?php
											endwhile;
										?>
										</div>
										<?php
									endif;
								endif;
							the_field( $prefix . '_guide_after', 'options' ); 
							?>
							</div>
						</div>
						<?php 
					else : 
						if ( get_sub_field( 'id' ) ) {
							$id = get_sub_field( 'id' );
						} else {
							$id = $block['id'] . '-' . get_row_index(); 
						}
						
						?>

					<button type="button"
						class="accordion-title"
						aria-expanded="false"
						<?php if ( get_sub_field( 'id' ) ) : ?>
							aria-controls="<?php echo 'text-' . esc_attr( $id ); ?>"
							id="<?php echo esc_attr( $id ); ?>">
						<?php else : ?>
							aria-controls="accordion-text-<?php echo esc_attr( $id ); ?>"
							id="accordion-title-<?php echo esc_attr( $id ); ?>">
						<?php endif; ?>
						<span class="h3">
							<?php the_sub_field( 'accordion_title' ); ?>
						</span>

						<span class="material-symbols" aria-hidden="true">
							keyboard_double_arrow_down
						</span>
					</button>

					<div class="accordion-text 
						<?php 
						if ( ! get_sub_field( 'image' ) ) {
							echo 'no-image'; } 
						?>
						<?php if ( get_sub_field( 'id' ) ) : ?>
					" id="<?php echo 'text-' . esc_attr( $id ); ?>">
					<?php else : ?>
					" id="accordion-text-<?php echo esc_attr( $id ); ?>">
					<?php endif; ?>
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
								<ul class="links">
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
										<li>
											<a class="link"
												href="<?php echo esc_url( $link_url ); ?>"
												target="<?php echo esc_attr( $link_target ); ?>"
												aria-label="<?php echo esc_attr( $link_title ); ?>">
												<span class="link-text">
												<?php the_sub_field( 'button_text' ); ?>
												</span>
												<span class="material-symbols" aria-hidden="true">
													keyboard_double_arrow_right
												</span>
											</a>
										</li>
									<?php endif; ?>
								<?php endwhile; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

