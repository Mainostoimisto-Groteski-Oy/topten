<?php
$text_block = get_field( 'text_block' );
$order      = get_field( 'order' );

?>

<section <?php topten_block_id(); ?> class="text-and-form-block">
	<div class="grid">
		<div class="text-block">
			<?php topten_block_title(); ?>

			<?php if ( ! empty( $text_block['text'] ) ) : ?>
				<?php echo wp_kses_post( $text_block['text'] ); ?>
			<?php endif; ?>

			<?php
			if ( $text_block['show_contact'] === true ) :
				$id = $text_block['contact'][0];
				
				$name       = get_field( 'name', $id );
				$title      = get_field( 'title', $id );
				$phone      = get_field( 'phone', $id );
				$email      = get_field( 'email', $id );
				$email_echo = str_replace( '@', '@<wbr/>', esc_html( $email ) );
				if ( get_field( 'image', $id ) ) {
					$image       = get_field( 'image', $id );
					$image_url   = $image['sizes']['medium'];
					$image_alt   = $image['alt'];
					$image_class = '';
					if ( get_field( 'image_is_logo', $id ) ) {
						$image_class = 'logo';
					}
				} else {
					$image_url   = '';
					$image_class = 'placeholder';  
				}
				?>

				<div class="contact-content">
					<div class="image <?php echo esc_html( $image_class ); ?>" >
					<?php if ( ! empty( $image_url ) ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
					<?php else : ?>
						<div class="placeholder">
							<span class="material-symbols" aria-hidden="true">person</span>
							<span class="screen-reader-text"><?php esc_html_e( 'Kuva henkilöstä puuttuu', 'topten' ); ?></span>
						</div>
					<?php endif; ?>
					</div>
					<h2 class="title h4"><?php echo esc_html( $name ); ?></h2>
					<span><?php echo esc_html( $title ); ?></span>
					<span><?php echo esc_html( $phone ); ?></span>
					<?php 
					// email must be escaped before echoing it since we insert a wbr tag in it
					// go away linter
					// @codingStandardsIgnoreLine ?>
					<a class="email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo $email_echo; ?></span></a>
				</div>

			<?php endif; ?>

			<?php topten_buttons( $text_block ); ?>
		</div>

		<div class="form-block <?php echo esc_attr( $order ); ?>">
			<?php if ( ! empty( get_field( 'form' ) ) ) : ?>
				<?php $formID = get_field( 'form' ); ?>
				<?php echo do_shortcode( '[gravityform id="' . esc_attr( $formID ) . '" title="true" description="true" ajax="true"]' ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
