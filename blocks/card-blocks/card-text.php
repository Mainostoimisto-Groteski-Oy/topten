<?php if ( get_field( 'description' ) ) : ?>
	<h2 class="desc">
		<?php the_field( 'description' ); ?>
	</h2>
<?php endif; ?>

<?php $text = get_field( 'text' ); ?>

<div class="text-wrapper">
	<?php
	echo wp_kses(
		$text,
		array(
			'a'      => array(
				'href',
				'title',
			),
			'b'      => array(),
			'br'     => array(),
			'em'     => array(),
			'h1'     => array(),
			'h2'     => array(),
			'h3'     => array(),
			'h4'     => array(),
			'h5'     => array(),
			'h6'     => array(),
			'i'      => array(),
			'img'    => array(
				'alt',
				'src',
			),
			'li'     => array(),
			'ol'     => array(),
			'p'      => array(),
			'strong' => array(),
			'ul'     => array(),
		)
	);
	?>
</div>
