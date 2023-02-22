<?php
$left_block   = get_field( 'left_block' );
$center_block = get_field( 'center_block' );
$right_block  = get_field( 'right_block' );
?>

<section <?php topten_block_id(); ?> class="three-column-block">
	<div class="grid-full">
		<?php topten_block_title(); ?>

		<div class="left-block <?php echo esc_attr( $left_block['icon_background_color'] ); ?>">
			<?php if ( ! empty( $left_block['icon'] ) ) : ?>
				<div class="icon">
					<?php
					$skip = false;
					switch ( $left_block['icon'] ) :
						case 'exclamation':
							$icon = get_template_directory_uri() . '/assets/dist/icons/exclamation.png';
							break;
						case 'threedots':
							$skip = true;
							break;
						case 'line':
							$icon = get_template_directory_uri() . '/assets/dist/icons/line.png';
							break;
						case 'cog':
							$icon = get_template_directory_uri() . '/assets/dist/icons/cog.png';
							break;
						case 'megaphone':
							$icon = get_template_directory_uri() . '/assets/dist/icons/megaphone.png';
							break;
						case 'contact':
							$icon = get_template_directory_uri() . '/assets/dist/icons/contact.png';
							break;
						default:
							$icon = '';
							$skip = true;
						endswitch;
					if ( ! $skip ) :
						?>
						<div class="icon-wrapper <?php echo esc_attr( $left_block['icon'] ); ?>">
								<img src="<?php echo esc_url( $icon ); ?>" alt="" />
							</div>
						<?php else : ?>
							<div class="icon-placeholder">
								<span></span>
								<span></span>
								<span></span>
							</div>
						<?php endif; ?>
				</div>
			<?php endif; ?>
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

							echo sprintf( '<li><a class="link" href="%s" title="%s" target="%s"><span class="link-text">%s</span><span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span></a></li>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
						}
					}

					echo '</ul>';
				}
				?>

				<?php topten_buttons( $left_block ); ?>
			</div>
		</div>

		<div class="center-block <?php echo esc_attr( $center_block['icon_background_color'] ); ?>">

			<?php if ( ! empty( $center_block['icon'] ) ) : ?>
				<div class="icon">
					<?php
					$skip = false;
					switch ( $center_block['icon'] ) :
						case 'exclamation':
							$icon = get_template_directory_uri() . '/assets/dist/icons/exclamation.png';
							break;
						case 'threedots':
							$skip = true;
							break;
						case 'line':
							$icon = get_template_directory_uri() . '/assets/dist/icons/line.png';
							break;
						case 'cog':
							$icon = get_template_directory_uri() . '/assets/dist/icons/cog.png';
							break;
						case 'megaphone':
							$icon = get_template_directory_uri() . '/assets/dist/icons/megaphone.png';
							break;
						case 'contact':
							$icon = get_template_directory_uri() . '/assets/dist/icons/contact.png';
							break;
						default:
							$icon = '';
							$skip = true;
						endswitch;
					if ( ! $skip ) :
						?>
							<div class="icon-wrapper <?php echo esc_attr( $center_block['icon'] ); ?>">
								<img src="<?php echo esc_url( $icon ); ?>" alt="" />
							</div>
						<?php else : ?>
							<div class="icon-placeholder">
								<span></span>
								<span></span>
								<span></span>
							</div>
						<?php endif; ?>
				</div>
			<?php endif; ?>

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

					foreach ( $center_block['links'] as $link ) {
						if ( ! empty( $link['link'] ) ) {
							$link   = $link['link'];
							$href   = $link['url'];
							$title  = $link['title'];
							$target = $link['target'];

							echo sprintf( '<li><a class="link" href="%s" title="%s" target="%s"><span class="link-text">%s</span><span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span></a></li>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
						}
					}

					echo '</ul>';
				}
				?>
				<?php topten_buttons( $center_block ); ?>
			</div>
		</div>

		<div class="right-block <?php echo esc_attr( $right_block['icon_background_color'] ); ?>">

			<?php if ( ! empty( $right_block['icon'] ) ) : ?>
				<div class="icon">
					<?php
					$skip = false;
					switch ( $right_block['icon'] ) :
						case 'exclamation':
							$icon = get_template_directory_uri() . '/assets/dist/icons/exclamation.png';
							break;
						case 'threedots':
							$skip = true;
							break;
						case 'line':
							$icon = get_template_directory_uri() . '/assets/dist/icons/line.png';
							break;
						case 'cog':
							$icon = get_template_directory_uri() . '/assets/dist/icons/cog.png';
							break;
						case 'megaphone':
							$icon = get_template_directory_uri() . '/assets/dist/icons/megaphone.png';
							break;
						case 'contact':
							$icon = get_template_directory_uri() . '/assets/dist/icons/contact.png';
							break;
						default:
							$icon = '';
							$skip = true;
						endswitch;
					if ( ! $skip ) :
						?>
							<div class="icon-wrapper <?php echo esc_attr( $right_block['icon'] ); ?>">
								<img src="<?php echo esc_url( $icon ); ?>" alt="" />
							</div>
						<?php else : ?>
							<div class="icon-placeholder">
								<span></span>
								<span></span>
								<span></span>
							</div>
						<?php endif; ?>
				</div>
			<?php endif; ?>

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

							echo sprintf( '<li><a class="link" href="%s" title="%s" target="%s"><span class="link-text">%s</span><span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span></a></li>', esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
						}
					}

					echo '</ul>';
				}
				?>
				<?php topten_buttons( $right_block ); ?>
			</div>
		</div>
	</div>
</section>
