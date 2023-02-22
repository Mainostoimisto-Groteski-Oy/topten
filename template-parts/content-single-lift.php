<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */
$background_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$background_url = 'background-image: url(' . esc_url( $background_image ) . ')';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="image">
		<img src="<?php echo get_the_post_thumbnail_url( $post->ID, 'medium' ); ?>" alt=""/>
	</div>

	<div class="content">
		<header class="entry-header">
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h4 class="entry-title">', '</h4>' );
			endif;

			if ( 'post' === get_post_type() ) :
				?>
				<div class="entry-meta">
					<?php echo get_the_date(); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Jatka lukemista <span class="screen-reader-text"> "%s"</span>', 'groteski' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Sivut:', 'groteski' ),
					'after'  => '</div>',
				)
			);
			?>
		</div><!-- .entry-content -->

		<div class="buttons">
			<a class="button" href="<?php the_permalink( $post->ID ); ?>">
				<span class="button-text">
					<?php esc_html_e( 'Lue lisää', 'groteski' ); ?>
				</span>
			</a>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
