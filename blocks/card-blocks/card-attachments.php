<?php topten_get_desc(); ?>

<?php if ( have_rows( 'attachments' ) ) : ?>
	<ul class="attachments" style="<?php topten_get_block_width(); ?>">
		<?php
		while ( have_rows( 'attachments' ) ) :
			the_row();

			$attachment = get_sub_field( 'attachment' );

			if ( ! $attachment ) :
				continue;
			endif;

			$href  = $attachment['url'];
			$title = $attachment['title'];
			?>

			<li>
				<a href="<?php esc_url( $href ); ?>" aria-label="<?php esc_attr( $title ); ?>">
					<?php echo esc_url( $href ); ?>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>
<?php endif; ?>
