<section class="lift-group-block">
	<div class="grid-full">
		<?php if ( have_rows( 'lift_group' ) ) : ?>
			<?php 
			while ( have_rows( 'lift_group' ) ) :
				the_row(); 

				$background_image = get_sub_field( 'image' );

			   

				if ( $background_image ) {
					$background_url = 'background-image: url(' . esc_url( $background_image['sizes']['fullhd'] ) . ')';
				} else {
					$background_url = '';
				}
				?>
				<div class="lift" style="<?php echo esc_attr( $background_url ); ?>">
					<div class="text">
						<h2 class="title h4"><?php gro_the_sub_field( 'title' ); ?></h2>
						<p><?php gro_the_sub_field( 'description' ); ?></p>
						<?php 
						if ( get_sub_field( 'link' ) ) : 
								$link   = get_sub_field( 'link' );
								$href   = esc_url( $link['url'] );
								$title  = esc_attr( $link['title'] );
								$target = esc_attr( $link['target'] );
								echo sprintf( '<a class="link" href="%s" title="%s" target="%s"><span class="link-text">%s</span><span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span></a>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
							?>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>
