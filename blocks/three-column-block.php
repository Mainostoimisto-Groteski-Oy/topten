<?php
$left_block = get_field( 'left_block' );
$center_block = get_field( 'center_block' );
$right_block = get_field( 'right_block' );
?>

<section <?php groteski_block_id(); ?> class="three-column-block">
	<div class="grid">
		<?php groteski_block_title(); ?>

		<div class="left-block">
			<?php if ( ! empty( $left_block['title'] ) ) : ?>
				<h3 class="block-title h3">
					<?php echo $left_block['title']; ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $left_block['text'] ) ) : ?>
				<?php echo $left_block['text']; ?>
			<?php endif; ?>

			<?php groteski_buttons( $left_block ); ?>
		</div>

		<div class="center-block">
			<?php if ( ! empty( $center_block['title'] ) ) : ?>
				<h3 class="block-title h3">
					<?php echo $center_block['title']; ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $center_block['text'] ) ) : ?>
				<?php echo $center_block['text']; ?>
			<?php endif; ?>

			<?php groteski_buttons( $center_block ); ?>
		</div>

		<div class="right-block">
			<?php if ( ! empty( $right_block['title'] ) ) : ?>
				<h3 class="block-title h3">
					<?php echo $right_block['title']; ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $right_block['text'] ) ) : ?>
				<?php echo $right_block['text']; ?>
			<?php endif; ?>

			<?php groteski_buttons( $right_block ); ?>
		</div>
	</div>
</section>