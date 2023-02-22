<?php
	$columns = get_field( 'columns' );
	$columns = $columns . '-columns';
?>
<section <?php groteski_block_id(); ?> class="logos-block">
	<div class="grid">
		<div class="text-block">
			<?php groteski_block_title(); ?>

			<?php if ( get_field( 'text' ) ) : ?>
				<?php the_field( 'text' ); ?>
			<?php endif; ?>
		</div>

		<?php if ( have_rows( 'logos' ) ) : ?>
			<div class="logos-grid <?php echo $columns; ?>">
				<?php while ( have_rows( 'logos' ) ) : the_row(); ?>
					<?php
					$logo = get_sub_field( 'logo' );

					if ( $logo ) :
						$src = esc_url( $logo['sizes']['medium'] );
						$alt = esc_attr( $logo['alt'] );

						$img = sprintf( '<img src="%s" alt="%s">', $src, $alt );

						$link = get_sub_field( 'link' );

						if ( $link ) :
							$href = esc_url( $link['url'] );
							$title = esc_attr( $link['title'] );
							$target = esc_attr( $link['target'] );

							echo sprintf( '<a class="logo" href="%s" title="%s" target="%s">%s</a>', $href, $title, $target, $img );
						else :
							echo sprintf( '<div class="logo">%s</div>', $img );
						endif;
					endif;
					?>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>