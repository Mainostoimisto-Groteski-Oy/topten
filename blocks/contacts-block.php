<section <?php groteski_block_id(); ?> class="contacts-block">
	<div class="grid">
		<?php groteski_block_title(); ?>

		<?php while ( have_rows( 'contacts' ) ) : the_row(); ?>
			<div class="single-contact">
				<div class="image">
					<?php if ( get_sub_field( 'image' ) ) : ?>
						<?php
						$img = get_sub_field( 'image' );

						$src = esc_url( $img['sizes']['medium'] );
						$alt = esc_url( $img['alt'] ); ?>

						<img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" />
					<?php endif; ?>
				</div>

				<div class="details">
					<?php if ( get_sub_field( 'name' ) ) : ?>
						<div class="name">
							<?php the_sub_field( 'name' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( get_sub_field( 'title' ) ) : ?>
						<div class="titteli">
							<?php the_sub_field( 'title' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( get_sub_field( 'tel_group' ) ) : ?>
						<?php
						$tel_group = get_sub_field( 'tel_group' );

						$tel = $tel_group['tel'] ?? '';
						$icon = $tel_group['icon'] ?? '';
						?>

						<?php if ( $tel ) : ?>
							<div class="contact-info">
								<a class="tel" href="tel:<?php echo $tel; ?>">
									<?php if ( $icon ) : ?>
										<?php echo $icon; ?>
									<?php endif; ?>

									<span class="link-text">
										<?php echo $tel; ?>
									</span>
								</a>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( get_sub_field( 'email_group' ) ) : ?>
						<?php
						$email_group = get_sub_field( 'email_group' );

						$email = $email_group['email'] ?? '';
						$icon = $email_group['icon'] ?? '';
						?>

						<?php if ( $email ) : ?>
							<div class="contact-info">
								<a class="mail" href="mailto:<?php echo $email; ?>">
									<?php if ( $icon ) : ?>
										<?php echo $icon; ?>
									<?php endif; ?>

									<span class="link-text">
										<?php echo $email; ?>
									</span>
								</a>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
</section>