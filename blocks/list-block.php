<section class="list-block">
	<div class="grid">
		<div class="text">
			<?php topten_block_title(); ?>
			<?php if ( get_field( 'description' ) ) : ?>
				<div class="description"><?php gro_the_field( 'description' ); ?></div>
			<?php endif; ?>
		</div>
		<?php if ( have_rows( 'list' ) ) : ?>
			<ul class="list">
				<?php 
				while ( have_rows( 'list' ) ) :
					the_row(); 
					?>
					<li>
						<h3 class="title h4"><?php gro_the_sub_field( 'name' ); ?></h3>
						<?php 
						if ( get_sub_field( 'link' ) ) :
						
							$link   = get_sub_field( 'link' );
							$href   = esc_url( $link['url'] );
							$title  = esc_attr( $link['title'] );
							$target = esc_attr( $link['target'] );

							?>
						<a class="link" href="<?php echo esc_url( $href ); ?>" target="<?php echo esc_attr( $target ); ?>"><span class="link-text"><?php echo esc_html( $title ); ?></span>	<span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span></a>
							<?php
						endif;
						?>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
