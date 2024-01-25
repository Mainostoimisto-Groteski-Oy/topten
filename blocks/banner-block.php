<section class="banner-block <?php gro_the_field( 'color' ); ?>">
	<div class="grid">
		<div class="content
		<?php
		if ( get_field( 'text' ) ) {
			echo 'with-text'; }
		?>
		">
			<?php topten_block_title(); ?>
			<?php if ( get_field( 'text' ) ) : ?>
				<div class="text">
					<?php gro_the_field( 'text' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
