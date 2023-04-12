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
$columns = array('left','middle','right');
?>
	<footer id="colophon" class="site-footer">
		<?php if(get_field('prefooter', 'options')) : ?>
			<section class="banner-block black">
				<div class="grid banner">
					<div class="content with-text">
						<?php if(get_field('prefooter_text', 'options')) : ?>
							<div class="text">
								<p><?php the_field('prefooter_text','options'); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
		<div class="grid">
			<?php foreach($columns as $column) : ?>
			<div class="footer <?php echo $column; ?>">
				<?php
				if(get_field('footer_'.$column, 'options')) {
					the_field('footer_'.$column, 'options');
				}
				if ( have_rows( 'footer_buttons_'.$column ) ) {
					echo '<div class="buttons">';
			
					while ( have_rows( 'footer_buttons_'.$column ) ) {
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
