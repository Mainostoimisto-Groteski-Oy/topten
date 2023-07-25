<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

get_header();
?>

	<main id="primary" class="site-main archive-page">
		<?php
		if ( function_exists( 'yoast_breadcrumb' ) ) :
			?>
				<div class="page-breadcrumbs">
					<div class="grid">
					<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php
		// get the id of WordPress home archive page and use it to get the gutenberg blocks
		$home_id = get_option( 'page_for_posts' );
		$blocks  = parse_blocks( get_post_field( 'post_content', $home_id ) );
		// Loop the blocks and output them
		foreach ( $blocks as $index => $block ) :
			echo render_block( $block ); // phpcs:ignore
		endforeach;
		?>

		<div class="content">
			<div class="grid post-wrapper">
				<?php
				if ( have_posts() ) :
					?>


					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						/*
						* Include the Post-Type-specific template for the content.
						* If you want to override this in a child theme, then include a file
						* called content-___.php (where ___ is the Post Type name) and that will be used instead.
						*/
						get_template_part( 'template-parts/content-single-lift' );

					endwhile;
					the_posts_pagination();
				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>
			</div>
		</div>
	</main><!-- #main -->



<?php
// get_sidebar();
get_footer();
