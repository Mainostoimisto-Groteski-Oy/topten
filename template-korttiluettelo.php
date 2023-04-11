<?php
/**
 * Template name: Korttiluettelo
 *
 * Template for Korttiluettelo pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

 get_header();
 /*
 $args = array(
	'post_type'	=> array('ohjekortti', 'tulkintakortti', 'lomakekortti'),
	'amount'	=> 1,
	'status'    => 'publish',
);
$card = get_posts($args);
*/
$tulkintakortit = get_posts(array(
	'post_type' => 'tulkintakortti',
	'amount'	=> -1,
	'status'	=> 'publish',
	),
);
$ohjekortit = get_posts(array(
	'post_type' => 'ohjekortti',
	'amount'	=> -1,
	'status'	=> 'publish',
	),
);
$lomakekortit = get_posts(array(
	'post_type' => 'lomakekortti',
	'amount'	=> -1,
	'status'	=> 'publish',
	),
);

?>

	<main id="primary" class="site-main">
		<?php
		if(!is_front_page()) : ?>
			<section class="page-title">
				<div class="grid">
					<?php the_title( '<h1 class="entry-title h3">', '</h1>' ); ?>
				</div>
			</section>
		<?php
	
		if ( function_exists('yoast_breadcrumb') ) : ?>
			<section class="page-breadcrumbs">
					<div class="grid">
						<?php yoast_breadcrumb( '<p id="breadcrumbs">','</p>' ); ?>
					</div>
			</section>
			<?php endif; ?>
		<?php endif; ?>
		<section class="cards">
			<div class="search-and-filter">

			</div>
			<div class="list">

			</div>
		</section>
	</main>
<?php get_footer(); ?>

 