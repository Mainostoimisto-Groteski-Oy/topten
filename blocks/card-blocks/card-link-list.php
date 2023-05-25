<?php topten_get_desc(); ?>

<?php if ( have_rows( 'link_list' ) ) : ?>
	<ul class="link-list">
		<?php
		while ( have_rows( 'link_list' ) ) :
			the_row();

			$link = get_sub_field( 'link' );

			if ( ! $link ) :
				continue;
			endif;

			$href  = $link['url'];
			$title = $link['title'];
			?>

			<li>
				<a href="<?php esc_url( $href ); ?>" aria-label="<?php esc_attr( $title ); ?>">
					<?php echo esc_url( $href ); ?>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>
<?php endif; ?>
