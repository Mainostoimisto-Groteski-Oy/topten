<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

?>

<button class="save-as-pdf" data-type="tulkintakortti">
	Tallenna PDF
</button>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="grid">
		<?php the_content(); ?>
	</div>
</article>
