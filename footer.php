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

$columns = array( 'left', 'middle', 'right' );
?>
	<footer id="colophon" class="site-footer">
		<?php if ( get_field( 'prefooter', 'options' ) ) : ?>
			<section class="banner-block black">
				<div class="grid banner">
					<div class="content with-text">
						<?php if ( get_field( 'prefooter_text', 'options' ) ) : ?>
							<div class="text">
								<p><?php the_field( 'prefooter_text', 'options' ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
		
		<div class="grid logos">
			<div class="site-branding">
				<?php the_custom_logo(); ?>
			</div>
			<?php if ( get_field( 'show_rty_logo', 'options' ) ) : ?>
				<div class="rty-branding">
					<?php $logo = get_field( 'rty_logo', 'options' ); ?>
					<img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>" width="<?php echo $logo['sizes']['medium-width']; ?>" height="<?php echo $logo['sizes']['medium-height']; ?>" />
				</div>
			<?php endif; ?>
		</div>
		
		<div class="grid">
			<?php foreach ( $columns as $column ) : ?>
				<div class="footer <?php echo esc_attr( $column ); ?>">
					<?php
					if ( get_field( 'footer_' . $column, 'options' ) ) {
						the_field( 'footer_' . $column, 'options' );
					}

					if ( have_rows( 'footer_buttons_' . $column, 'options' ) ) {
						echo '<div class="buttons">';

						while ( have_rows( 'footer_buttons_' . $column, 'options' ) ) {
							the_row();

							$button = get_sub_field( 'button' );

							if ( $button ) {
								$href   = esc_url( $button['url'] );
								$title  = esc_attr( $button['title'] );
								$target = esc_attr( $button['target'] );

								echo sprintf( '<a class="button" href="%s" title="%s" target="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
							}
						}

						echo '</div>';
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
