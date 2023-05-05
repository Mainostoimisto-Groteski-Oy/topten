<?php topten_get_desc(); ?>

<?php $text = get_field( 'text' ); ?>

<div class="text-wrapper pdf-skip">
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
