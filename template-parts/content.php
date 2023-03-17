<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="grid">
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</div>
</article>
