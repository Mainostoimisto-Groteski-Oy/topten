<?php
/**
 * Template name: FAQ
 *
 * Template for FAQ page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Topten
 */

get_header();

?>

<main id="primary" class="site-main" data-template="<?php echo esc_html( get_field( 'card_status_type' ) ); ?>">

	<section id="card-accordion" class="accordion-block">
		<div class="grid">
			<h1 class="h3 title"><?php esc_html_e( 'Usein kysytyt kysymykset', 'topten' ); ?></h1>
			<div class="content">
				<?php if ( get_field( 'tulkinta_guide', 'options' ) ) : ?>
				<button id="first" class="accordion-title" type="button" aria-expanded="false" aria-controls="first-text">
					<h2 class="h3"><?php esc_html_e( 'Lomakekortit', 'topten' ); ?></h2>
					<span class="material-symbols" aria-hidden="true">
						keyboard_double_arrow_down
					</span>
				</button>
				<div id="first-text" class="accordion-text no-image">

				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php topten_breadcrumbs(); ?>

	<?php the_content(); ?>

</main>

<?php
get_footer();
