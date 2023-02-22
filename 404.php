<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Topten
 */

get_header();
?>

<div class="grid">
	<main id="primary" class="site-main">
		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title h2">
					<?php esc_html_e( 'Sivua ei löytynyt.', 'topten' ); ?>
				</h1>
			</header>

			<div class="page-content">

				<a href="<?php echo esc_url( home_url() ); ?>">
					<span>
						<?php esc_html_e( 'Palaa etusivulle.', 'topten' ); ?>
					</span>
				</a>
			</div>
		</section>
	</main>
</div>

<?php
get_footer();
