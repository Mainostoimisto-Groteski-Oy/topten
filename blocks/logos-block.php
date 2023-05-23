<?php 
if(empty(topten_block_title(false)) && empty(get_field('description')) || empty(get_field('logos'))) {
	$rows = 1;
	$gap = 'auto';
} else {
	$rows = 2;
	$gap = 'none';
}

?>
<section <?php topten_block_id(); ?> class="logos-block">
	<div class="grid rows-<?php echo esc_attr($rows); ?> gap-<?php echo esc_attr($gap); ?>">
		<?php if(!empty(topten_block_title(false)) || !empty(get_field('description'))) : ?>
			
			<div class="text-block">

				<?php if(!empty(topten_block_title(false))) : ?>
					<?php topten_block_title(); ?>
				<?php endif; ?>

				<?php if ( get_field( 'description' ) ) : ?>
					<div class="description">
						<?php the_field( 'description' ); ?>
					</div>
				<?php endif; ?>

			</div>

		<?php endif; ?>
				
		<?php if ( have_rows( 'logos' ) ) : ?>
			
			<div class="logos">

				<?php
				while ( have_rows( 'logos' ) ) :
					
					the_row();

					

					$logo = get_sub_field( 'logo' );
					$name = get_sub_field( 'name' );
					if ( $logo ) :
						$src = esc_url( $logo['sizes']['medium'] );
						$alt = esc_attr( $logo['alt'] );

						$img = sprintf( '<img src="%s" alt="%s">', $src, $alt );

						$link = get_sub_field( 'link' );

						if ( $link ) :
							$href   = esc_url( $link['url'] );
							$title  = esc_attr( $link['title'] );
							$target = esc_attr( $link['target'] );

							echo sprintf( '<a class="logo" href="%s" title="%s" target="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $img ) );
						else :
							echo sprintf( '<div class="logo">%s</div>', wp_kses_post( $img ) );
						endif;
					elseif ( $name && !$logo ) :
						$text = get_sub_field( 'name' );
						$link = get_sub_field( 'link' );
						if ( $link ) :
							$href   = esc_url( $link['url'] );
							$title  = esc_attr( $link['title'] );
							$target = esc_attr( $link['target'] );

							echo sprintf( '<a class="logo text" href="%s" title="%s" target="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $text ) );
						else :
							echo sprintf( '<div class="logo text">%s</div>', wp_kses_post( $text ) );	
						endif;
					endif;
				endwhile;
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
