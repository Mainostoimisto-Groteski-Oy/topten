<?php
$left_block   = get_field( 'left_block' );
$center_block = get_field( 'center_block' );
$right_block  = get_field( 'right_block' );
?>

<section <?php topten_block_id(); ?> class="three-column-block">
	<div class="grid-full">
		<?php topten_block_title(); ?>

		<div class="left-block bg-<?php echo esc_attr( $left_block['generated_background_color'] ); ?> ">
			<div class="wrapper">
				<?php if ( ! empty( $left_block['title'] ) ) : ?>
					<h3 class="block-title h3">
						<?php echo wp_kses_post( $left_block['title'] ); ?>
					</h3>
				<?php endif; ?>

				<?php if ( ! empty( $left_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $left_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $left_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $left_block['links'] as $link ) {
						if ( ! empty( $link['link'] ) ) {
							$link   = $link['link'];
							$href   = $link['url'];
							$title  = $link['title'];
							$target = $link['target'];

							echo sprintf( '<li><a class="link" href="%s" title="%s" target="%s"><span>%s</span></a></li>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
						}
					}

					echo '</ul>';
				}
				?>

				<?php topten_buttons( $left_block, esc_attr( $left_block['generated_background_color'] ) ); ?>
			</div>
		</div>

		<div class="center-block bg-<?php echo esc_attr( $center_block['generated_background_color'] ); ?> ">
			<div class="wrapper">
				<?php if ( ! empty( $center_block['title'] ) ) : ?>
					<h3 class="block-title h3">
						<?php echo wp_kses_post( $center_block['title'] ); ?>
					</h3>
				<?php endif; ?>

				<?php if ( ! empty( $center_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $center_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $center_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $left_block['links'] as $link ) {
						if ( ! empty( $link['link'] ) ) {
							$link   = $link['link'];
							$href   = $link['url'];
							$title  = $link['title'];
							$target = $link['target'];

							echo sprintf( '<li><a class="link" href="%s" title="%s" target="%s"><span>%s</span></a></li>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
						}
					}

					echo '</ul>';
				}
				?>
				<?php topten_buttons( $center_block, esc_attr( $center_block['generated_background_color'] ) ); ?>
			</div>
		</div>

		<div class="right-block bg-<?php echo esc_attr( $right_block['generated_background_color'] ); ?> ">
			<div class="wrapper">
				<?php if ( ! empty( $right_block['title'] ) ) : ?>
					<h3 class="block-title h3">
						<?php echo wp_kses_post( $right_block['title'] ); ?>
					</h3>
				<?php endif; ?>

				<?php if ( ! empty( $right_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $right_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $right_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $right_block['links'] as $link ) {
						if ( ! empty( $link['link'] ) ) {
							$link   = $link['link'];
							$href   = $link['url'];
							$title  = $link['title'];
							$target = $link['target'];

							echo sprintf( '<li><a class="link" href="%s" title="%s" target="%s"><span>%s</span></a></li>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
						}
					}

					echo '</ul>';
				}
				?>
				<?php topten_buttons( $right_block, esc_attr( $right_block['generated_background_color'] ) ); ?>
			</div>
		</div>
	</div>
</section>
