<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Topten
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	$image = get_the_post_thumbnail_url( $post->ID, 'fullhd' );

	if ( $image ) {
		$image_id = attachment_url_to_postid( $image );
		$alt      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		$class    = '';
	} else {
		$image = get_template_directory_uri() . '/assets/dist/images/placeholder.png';
		$class = 'placeholder';
		$alt   = '';
	}
	?>
	<div class="image <?php echo $class ? esc_attr( $class ) : ''; ?>">
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

			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title h4">', '</h2>' );
			endif;
			?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
			if ( is_singular() ) :
				the_content(
					sprintf(
						wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Jatka lukemista <span class="screen-reader-text"> "%s"</span>', 'topten' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						wp_kses_post( get_the_title() )
					)
				);

			else :
				?>
				<div class="excerpt">
					<?php the_excerpt(); ?>
				</div>
				<?php
			endif;
			?>
		</div><!-- .entry-content -->

		<ul class="links">
			<li>
				<a class="link" href="<?php the_permalink( $post->ID ); ?>">
					<span class="link-text">
						<?php esc_html_e( 'Lue koko juttu', 'topten' ); ?>
						<span class="screen-reader-text">
							<?php echo esc_html( get_the_title( $post->ID ) ); ?>
						</span>
					</span>
					<span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span>
				</a>
			</li>
		</ul>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
