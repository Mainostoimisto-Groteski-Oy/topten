<section <?php groteski_block_id(); ?> class="embed-block">
	<div class="grid">
		<?php if ( get_field( 'embed' ) ) : ?>
			<?php the_field( 'embed' ); ?>
		<?php endif; ?>
	</div>
</section>