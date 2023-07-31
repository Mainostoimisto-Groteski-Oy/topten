<?php
$text     = get_field( 'text' );
$tulkinta = get_field( 'tulkinta' );

$color = false;

if ( $tulkinta && 'none' !== $tulkinta['value'] ) :
	$color = topten_get_guide_color( $tulkinta['value'] );

	$class  = $color ? ' ' . $color : '';
	$class .= ! empty( $tulkinta['value'] ) ? ' ' . $tulkinta['value'] : '';
	?>
<?php endif; ?>

<div class="card-text-block column" style="<?php topten_get_block_width(); ?>">
	<?php topten_get_desc(); ?>

	<?php
	echo wp_kses(
		$text,
		array(
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'style'  => array(),
				'target' => array(),
			),
			'b'      => array(
				'style' => array(),
			),
			'br'     => array(
				'style' => array(),
			),
			'em'     => array(
				'style' => array(),
			),
			'h1'     => array(
				'style' => array(),
			),
			'h2'     => array(
				'style' => array(),
			),
			'h3'     => array(
				'style' => array(),
			),
			'h4'     => array(
				'style' => array(),
			),
			'h5'     => array(
				'style' => array(),
			),
			'h6'     => array(
				'style' => array(),
			),
			'i'      => array(
				'style' => array(),
			),
			'img'    => array(
				'alt' => array(),
				'src' => array(),
			),
			'li'     => array(
				'style' => array(),
			),
			'ol'     => array(
				'style' => array(),
			),
			'p'      => array(
				'style' => array(),
			),
			'strong' => array(
				'style' => array(),
			),
			'ul'     => array(
				'style' => array(),
			),
			'sup'    => array(
				'style' => array(),
			),
			'sub'    => array(
				'style' => array(),
			),
		)
	);
	?>
</div>
