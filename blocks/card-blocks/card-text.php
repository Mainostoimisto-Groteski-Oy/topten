<?php topten_get_desc(); ?>

<?php
// TODO: värit
$text     = get_field( 'text' );
$tulkinta = get_field( 'tulkinta' );

if ( $tulkinta && 'none' !== $tulkinta['value'] ) :

	$color = topten_get_guide_color( $tulkinta['value'] );
	?>
	<div class="tulkinta">
		<p class="<?php echo $color ? esc_html( $color ) : ''; ?> <?php echo esc_html( $tulkinta['value'] ); ?>">
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
