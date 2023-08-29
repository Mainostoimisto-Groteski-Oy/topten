<?php
$first_block  = get_field( 'first_block' );
$second_block = get_field( 'second_block' );
$third_block  = get_field( 'third_block' );
$fourth_block = get_field( 'fourth_block' );

?>

<section <?php topten_block_id(); ?> class="four-column-block
<?php
if ( get_field( 'decorate_titles' ) ) {
	echo 'decorate'; }
?>
">
	<div class="grid">
		<div class="block-title">
			<?php topten_block_title(); ?>
		</div>

		<div class="first-block column">
			<div class="wrapper">
				<?php if ( ! empty( $first_block['title'] ) ) : ?>
					<div class="title-wrapper">
						<h3 class="block-title h4">
							<?php echo wp_kses_post( $first_block['title'] ); ?>
						</h3>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $first_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $first_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $first_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $first_block['links'] as $link ) {
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

				<?php topten_buttons( $first_block ); ?>
			</div>
		</div>

		<div class="second-block column">
			<div class="wrapper">
				<?php if ( ! empty( $second_block['title'] ) ) : ?>
					<div class="title-wrapper">
						<h3 class="block-title h4">
							<?php echo wp_kses_post( $second_block['title'] ); ?>
						</h3>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $second_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $second_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $second_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $second_block['links'] as $link ) {
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
				<?php topten_buttons( $second_block ); ?>
			</div>
		</div>

		<div class="third-block column">
			<div class="wrapper">
				<?php if ( ! empty( $third_block['title'] ) ) : ?>
					<div class="title-wrapper">
						<h3 class="block-title h4">
							<?php echo wp_kses_post( $third_block['title'] ); ?>
						</h3>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $third_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $third_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $third_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $third_block['links'] as $link ) {
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
				<?php topten_buttons( $third_block ); ?>
			</div>
		</div>

		<div class="fourth-block column">
			<div class="wrapper">
				<?php if ( ! empty( $fourth_block['title'] ) ) : ?>
					<div class="title-wrapper">
						<h3 class="block-title h4">
							<?php echo wp_kses_post( $fourth_block['title'] ); ?>
						</h3>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $fourth_block['text'] ) ) : ?>
					<?php echo wp_kses_post( $fourth_block['text'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $fourth_block['links'] ) ) {
					echo '<ul class="links">';

					foreach ( $fourth_block['links'] as $link ) {
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
				<?php topten_buttons( $fourth_block ); ?>
			</div>
		</div>

	</div>
</section>
