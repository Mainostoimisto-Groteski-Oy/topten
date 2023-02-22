<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Topten
 */

get_header();
?>

	<main id="primary" class="site-main" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( 'tulkintakortti' === get_post_type() || 'ohjekortti' === get_post_type() || 'lomakekortti' === get_post_type() ) {
				get_template_part( 'template-parts/content-card' );
			} else {
				get_template_part( 'template-parts/content', get_post_type() );
			}
		endwhile; // End of the loop.
		?>
	</main><!-- #main -->

<?php
// get_sidebar();
get_footer();
