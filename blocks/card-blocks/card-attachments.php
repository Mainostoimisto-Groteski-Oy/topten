<?php
$block_anchor = get_field( 'block_id' );
?>

<div class="column-item attachments-wrapper" style="<?php topten_get_block_width(); ?>" data-block-id="<?php echo esc_attr( $block_anchor ); ?>">
	<?php topten_get_desc(); ?>

	<?php if ( have_rows( 'attachments' ) ) : ?>
		<ul class="attachments">
			<?php
			while ( have_rows( 'attachments' ) ) :
				the_row();

				$attachment = get_sub_field( 'attachment' );
				$text       = get_sub_field( 'text' );
				$target     = get_sub_field( 'target' );

				if ( ! $attachment ) :
					continue;
				endif;

				$href  = $attachment['url'];
				$title = $attachment['title'];

				$target = $target ? '_blank' : '_self';
				?>

				<li>
					<?php if ( ! empty( $text ) ) : ?>
						<a href="<?php echo esc_url( $href ); ?>"
							aria-label="<?php esc_attr( $text ); ?>"
							target="<?php echo esc_attr( $target ); ?>">
							<?php echo esc_html( $text ); ?>
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( $href ); ?>"
							aria-label="<?php esc_attr( $title ); ?>"
							target="<?php echo esc_attr( $target ); ?>">
							<?php echo esc_url( $href ); ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
	<?php endif; ?>
</div>
