<section <?php groteski_block_id(); ?> class="text-block">
	<div class="grid">
		<?php groteski_block_title(); ?>

		<?php if ( get_field( 'ingress' ) ) : ?>
            <div class="ingress">
			    <?php the_field( 'ingress' ); ?>
            </div>
		<?php endif; ?>

		<?php if ( get_field( 'text' ) ) : ?>
            <div class="text">
			    <?php the_field( 'text' ); ?>
            </div>
		<?php endif; ?>

		<?php groteski_buttons(); ?>
	</div>
</section>