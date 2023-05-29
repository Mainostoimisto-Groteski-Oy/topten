<?php topten_get_desc(); ?>

<?php
$text     = get_field( 'text' );
$tulkinta = get_field( 'tulkinta' );

if ( $tulkinta && 'none' !== $tulkinta['value'] ) :
	?>
	<div class="tulkinta">
		<p class="<?php echo esc_html( $tulkinta['value'] ); ?>">
			<?php echo esc_html( $tulkinta['label'] ); ?>
		</p>
	</div>
<?php endif; ?>

<div class="text-wrapper <?php echo $tulkinta ? esc_html( $tulkinta['value'] ) : ''; ?>">
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
