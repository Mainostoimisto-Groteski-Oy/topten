<section <?php topten_block_id(); ?> class="contacts-block">
	<div class="grid">
		<?php topten_block_title(); ?>

		<?php
		while ( have_rows( 'contacts' ) ) :
			the_row();
			?>
			<div class="single-contact">
				<div class="image">
					<?php if ( get_sub_field( 'image' ) ) : ?>
						<?php
						$img = get_sub_field( 'image' );

						$src = $img['sizes']['medium'];
						$alt = $img['alt'];
						?>

						<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
					<?php endif; ?>
				</div>

				<div class="details">
					<?php if ( get_sub_field( 'name' ) ) : ?>
						<div class="name">
							<?php gro_the_sub_field( 'name' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( get_sub_field( 'title' ) ) : ?>
						<div class="titteli">
							<?php gro_the_sub_field( 'title' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( get_sub_field( 'tel_group' ) ) : ?>
						<?php
						$tel_group = get_sub_field( 'tel_group' );

						$tel  = $tel_group['tel'] ?? '';
						$icon = $tel_group['icon'] ?? '';
						?>

						<?php if ( $tel ) : ?>
							<div class="contact-info">
								<a class="tel" href="tel:<?php echo esc_attr( $tel ); ?>">
									<?php if ( $icon ) : ?>
										<?php echo wp_kses_post( $icon ); ?>
									<?php endif; ?>

									<span class="link-text">
										<?php echo wp_kses_post( $tel ); ?>
									</span>
								</a>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( get_sub_field( 'email_group' ) ) : ?>
						<?php
						$email_group = get_sub_field( 'email_group' );

						$email = $email_group['email'] ?? '';
						$icon  = $email_group['icon'] ?? '';
						?>

						<?php if ( $email ) : ?>
							<div class="contact-info">
								<a class="mail" href="mailto:<?php echo esc_attr( $email ); ?>">
									<?php if ( $icon ) : ?>
										<?php echo wp_kses_post( $icon ); ?>
									<?php endif; ?>

									<span class="link-text">
										<?php echo wp_kses_post( $email ); ?>
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
