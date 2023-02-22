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

	if ( get_post_thumbnail_id( $post->ID ) ) {
		$src      = esc_url( get_the_post_thumbnail_url( $post->ID, 'large' ) );
		$class    = '';
		$image_id = get_post_thumbnail_id( ( $post->ID ) );
		$alt      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	} else {
		$src   = get_template_directory_uri() . '/assets/dist/images/topten-placeholder-small.png';
		$class = 'placeholder';
		$alt   = '';
	}
	?>

<?php if ( get_field( 'article_image_icon', $post->ID ) ) : ?>

<div class="image icon">
	<div class="icon-wrapper">
	<img src="<?php echo esc_url( $src ); ?>" alt=""/>
	</div>
</div>

<?php else : ?>

<div class="image <?php echo esc_attr( $class ); ?>">
	<img src="<?php echo esc_url( $src ); ?>" alt=""/>
</div>

<?php endif; ?>

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
						if ( $date ) :
							echo esc_html( $date );
						endif;
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

		<ul class="links">
			<li>
				<a class="link" href="<?php the_permalink( $post->ID ); ?>">
					<span class="link-text">
						<?php esc_html_e( 'Lue koko juttu', 'topten' ); ?>
					</span>
					<span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span>
				</a>
			</li>
		</ul>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
