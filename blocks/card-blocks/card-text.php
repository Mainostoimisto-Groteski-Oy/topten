<?php 
if ( get_field( 'description' ) ) : ?>
	<div class="desc">
		<?php the_field( 'description' ); ?>
	</div>
	<?php
endif;
?>
<div class="text">
	<?php the_field( 'text' ); ?>
</div>
