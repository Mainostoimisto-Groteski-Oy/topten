<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Groteski
 */

?>
	<footer id="colophon" class="site-footer">
		<div class="grid">
			<div class="footer-left">
				<div class="logo">
					<?php
					$logo = get_field( 'footer_logo', 'options' );

					if ( $logo ) :
						$src = $logo['sizes']['thumbnail'];
						$alt = $logo['alt'];
						?>

						<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
					<?php endif; ?>
				</div>
			</div>

			<div class="footer-center">
				<?php if ( have_rows( 'footer_links', 'option' ) ) : ?>
					<ul class="footer-links">
						<?php
						while ( have_rows( 'footer_links', 'option' ) ) :
							the_row();
							?>
							<?php $link = get_sub_field( 'link', 'option' ); ?>

							<?php if ( $link ) : ?>
								<li>
									<?php
									$href   = $link['url'];
									$title  = $link['title'];
									$target = $link['target'];
									?>

									<a href="<?php echo esc_url( $href ); ?>"
										title="<?php echo esc_attr( $title ); ?>"
										target="<?php echo esc_attr( $target ); ?>">
										<span class="link-text">
											<?php echo wp_kses_post( $title ); ?>
										</span>
									</a>
								</li>
							<?php endif; ?>
						<?php endwhile; ?>
					</ul>
				<?php endif; ?>

				<?php if ( have_rows( 'footer_some_links', 'option' ) ) : ?>
					<div class="some-icons">
						<ul>
							<?php
							while ( have_rows( 'footer_some_links', 'option' ) ) :
								the_row();
								?>
								<?php
								if ( get_sub_field( 'some_link', 'option' ) ) :
									$some_link = get_sub_field( 'some_link', 'option' );
									$some_icon = get_sub_field( 'some_icon', 'option' );
									?>

									<li>
										<a class="some-link"
											href="<?php echo esc_url( $some_link['url'] ); ?>"
											title="<?php echo esc_attr( $some_link['title'] ); ?>">

											<span class="some-icon">
												<img src="<?php echo esc_url( $some_icon['sizes']['thumbnail'] ); ?>" />
											</span>

											<span class="link-text">
												<?php echo wp_kses_post( $some_link['title'] ); ?>
											</span>
										</a>
									</li>
								<?php endif; ?>
							<?php endwhile; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>

			<div class="footer-right">
				<?php if ( get_field( 'footer_address_group', 'options' ) ) : ?>
					<?php
					$address_group = get_field( 'footer_address_group', 'options' );

					$address = $address_group['address'] ?? false;
					$icon    = $address_group['icon'] ?? false;
					?>

					<?php if ( $address ) : ?>
						<div class="contact-info">
							<span class="address">
								<?php if ( $icon ) : ?>
									<?php echo wp_kses_post( $icon ); ?>
								<?php endif; ?>

								<span class="link-text">
									<?php echo wp_kses_post( $address ); ?>
								</span>
							</span>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( get_field( 'footer_phonenumber_group', 'options' ) ) : ?>
					<?php
					$tel_group = get_field( 'footer_phonenumber_group', 'options' );

					$tel  = $tel_group['tel'] ?? false;
					$icon = $tel_group['icon'] ?? false;
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

				<?php if ( get_field( 'footer_email_group', 'options' ) ) : ?>
					<?php
					$email_group = get_field( 'footer_email_group', 'options' );

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

			<div class="web-design">
				Webdesign <a href="https://groteski.fi" title="Groteski" target="_blank">Groteski</a>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
