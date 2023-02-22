<?php
$name  = get_field( 'name' );
$title = get_field( 'title' );
$phone = get_field( 'phone' );
$email = get_field( 'email' );
if ( get_field( 'image' ) ) {
	$image       = get_field( 'image' );
	$image_url   = $image['sizes']['medium'];
	$image_alt   = $image['alt'];
	$image_class = '';
	if ( get_field( 'image_is_logo' ) ) {
		$image_class = 'logo';
	}
} else {
	$image_url   = '';
	$image_class = 'placeholder';  
}
?>
<section class="single-contact">
	<div class="grid">
		<div class="contact-content">
			<div class="image <?php echo esc_html( $image_class ); ?>" >
			<?php if ( ! empty( $image_url ) ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
			<?php else : ?>
				<div class="image-placeholder"></div>
			<?php endif; ?>
			</div>
			<h1 class="title h3"><?php echo esc_html( $name ); ?></h1>
			<span><?php echo esc_html( $title ); ?></span>
			<span><?php echo esc_html( $phone ); ?></span>
			<a class="email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></span>
		</div>
	</div>
</section>
