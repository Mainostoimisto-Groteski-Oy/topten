<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Topten
 */

$columns = array( 'left', 'middle', 'right' );
?>
	<footer id="colophon" class="site-footer">
		<?php if ( get_field( 'prefooter', 'options' ) ) : ?>
			<section class="banner-block black">
				<div class="grid banner">
					<div class="content with-text">
						<?php if ( get_field( 'prefooter_text', 'options' ) ) : ?>
							<div class="text">
								<p><?php gro_the_field( 'prefooter_text', 'options' ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<div class="grid logos">
			<div class="site-branding">
				<?php
					$site_logo = esc_url( wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) );
					$alt       = get_post_meta( get_theme_mod( 'custom_logo' ), '_wp_attachment_image_alt', true );
				?>
				<a class="custom-logo-link" href="<?php echo esc_url( home_url() ); ?>" rel="home">
					<img src="<?php echo esc_url( $site_logo ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
				</a>
			</div>
			<?php if ( get_field( 'show_rty_logo', 'options' ) ) : ?>
				<div class="rty-branding">
					<?php $logo = get_field( 'rty_logo', 'options' ); ?>
					<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" width="<?php echo esc_attr( $logo['sizes']['medium-width'] ); ?>" height="<?php echo esc_attr( $logo['sizes']['medium-height'] ); ?>" />
				</div>
			<?php endif; ?>
		</div>

		<div class="grid">
			<?php foreach ( $columns as $column ) : ?>
				<div class="footer <?php echo esc_attr( $column ); ?>">
					<?php
					if ( get_field( 'footer_' . $column . '_title', 'options' ) ) {
						echo "<h3 class='h4 title'>" . esc_html( get_field( 'footer_' . $column . '_title', 'options' ) ) . '</h3>';
					}
					if ( get_field( 'footer_' . $column, 'options' ) ) {
						gro_the_field( 'footer_' . $column, 'options' );
					}

					if ( have_rows( 'footer_buttons_' . $column, 'options' ) ) {
						if ( $column === 'right' ) {
							$wrapper = 'buttons';
						} else {
							$wrapper = 'links large';
						}
						echo '<ul class="' . esc_attr( $wrapper ) . '">';

						while ( have_rows( 'footer_buttons_' . $column, 'options' ) ) {
							the_row();

							$button = get_sub_field( 'button' );

							if ( $button ) {
								$href   = esc_url( $button['url'] );
								$title  = esc_attr( $button['title'] );
								$target = esc_attr( $button['target'] );

								if ( $column === 'right' ) {
									$class = 'button';
								} else {
									$class = 'link';
								}
								if ( get_sub_field( 'button_icon' ) ) {
									$subclass = get_sub_field( 'button_icon' );

								} else {
									$subclass = '';
								}
								if ( 'right' === $column ) {
									if ( 'back' === $subclass ) {
										echo sprintf( '<li><a class="%s %s" href="%s" title="%s" target="%s"><span class="material-symbols" aria-hidden="true">arrow_right_alt</span><span class="link-text">%s</span></a></li>', esc_attr( $class ), esc_attr( $subclass ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
									} elseif ( 'rss' === $subclass ) {
										echo sprintf( '<li><a class="%s %s" href="%s" title="%s" target="%s"><span class="material-symbols" aria-hidden="true">rss_feed</span><span class="link-text">%s</span></a></li>', esc_attr( $class ), esc_attr( $subclass ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
									} else {
										echo sprintf( '<li><a class="%s %s" href="%s" title="%s" target="%s"><span class="link-text">%s</span></a></li>', esc_attr( $class ), esc_attr( $subclass ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
									}
								} else {
									echo sprintf( '<li><a class="%s %s" href="%s" title="%s" target="%s"><span class="link-text">%s</span><span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span></a></li>', esc_attr( $class ), esc_attr( $subclass ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
								}
							}
						}

						echo '</ul>';
					}

					if ( get_field( 'footer_after_' . $column, 'options' ) ) :
						?>
						<div class="text-after-links">
							<?php gro_the_field( 'footer_after_' . $column, 'options' ); ?>
						</div>
						<?php
					endif;
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
