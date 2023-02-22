<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Groteski
 */

get_header();
?>

<div class="grid">

	<main id="primary" class="site-main">

		<section class="error-404 not-found">

			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'groteski' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">

				<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'groteski' ); ?></p>

				<a href="<?php echo home_url(); ?>"><span><?php esc_html_e( 'Return to homepage.', 'groteski' ); ?></span></a>

			</div><!-- .page-content -->

		</section><!-- .error-404 -->

	</main><!-- #main -->

</div>

<?php
get_footer();
