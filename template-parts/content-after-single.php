<?php
/**
 * Template part for displaying posts
 * TODO: Find out why this is named like this?
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	$image = get_the_post_thumbnail_url( $post->ID, 'fullhd' );
	// get the alt if image exists
if ( $image ) {
	$alt   = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
	$class = '';
} else {
	$image = get_template_directory_uri() . '/assets/dist/images/placeholder.png';
	$class = 'placeholder';
	$alt   = '';
}
?>
	<div class="image 
	<?php 
	if ( ! empty( $class ) ) {
		echo esc_attr( $class ); } 
	?>
	">
		<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
	</div>



	<div class="content">
		<header class="entry-header">
			<?php
			if ( 'post' === get_post_type() ) :
				?>
				<div class="entry-meta">
					<?php
					$id = get_the_ID();
					// get the post date by post id
					$date = get_the_date( 'j.n.Y', $id );
					?>
					
					<p class="date">
					<?php 
					if ( ! empty( $date ) ) {
						echo esc_html( $date ); } 
					?>
					</p>
					
				</div><!-- .entry-meta -->
			<?php endif; ?>
			
			<?php the_title( '<h2 class="entry-title h4">', '</h2>' ); ?>

		</header><!-- .entry-header -->

		<div class="entry-content">

			
		<div class="excerpt">
			<?php the_excerpt(); ?>
		</div>

		</div><!-- .entry-content -->

		<div class="links">
			<a class="link" href="<?php the_permalink( $post->ID ); ?>">
				<span class="link-text">
					<?php esc_html_e( 'Lue koko juttu', 'topten' ); ?>
				</span>
			</a>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
