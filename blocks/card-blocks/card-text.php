<?php topten_get_desc(); ?>

<?php
$text     = get_field( 'text' );
$tulkinta = get_field( 'tulkinta' );

$color = false;

if ( $tulkinta && 'none' !== $tulkinta['value'] ) :
	$color = topten_get_guide_color( $tulkinta['value'] );

	$class  = $color ? ' ' . $color : '';
	$class .= ! empty( $tulkinta['value'] ) ? ' ' . $tulkinta['value'] : '';
	?>

	<div class="tulkinta">
		<p class="<?php echo esc_attr( trim( $class ) ); ?>">
			<?php echo esc_html( $tulkinta['label'] ); ?>
		</p>
	</div>
<?php endif; ?>

<div class="text-wrapper <?php echo $color ? esc_html( 'bg-' . $color ) : ''; ?> <?php echo $tulkinta ? esc_html( $tulkinta['value'] ) : ''; ?>">

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
