<?php
$text     = get_field( 'text' );
$tulkinta = get_field( 'tulkinta' );

$color       = false;
$bigger_font = get_field( 'bigger_font' );

if ( $tulkinta && 'none' !== $tulkinta['value'] ) :
	$color = topten_get_guide_color( $tulkinta['value'] );

	$class  = $color ? ' ' . $color : '';
	$class .= ! empty( $tulkinta['value'] ) ? ' ' . $tulkinta['value'] : '';
	?>
<?php endif; ?>

<div class="column-item card-text-block <?php echo $bigger_font ? 'bigger-font' : ''; ?> " style="<?php topten_get_block_width(); ?>">
	<?php topten_get_desc(); ?>

	<div class="text-wrapper <?php echo $color ? esc_html( 'bg-' . $color ) : ''; ?> <?php echo $tulkinta ? esc_html( $tulkinta['value'] ) : ''; ?> ">
		<?php if ( $tulkinta && 'none' !== $tulkinta['value'] ) : ?>
			<?php
			switch ( $tulkinta['value'] ) :
				case 'law':
					$icon = '§';
					break;
				case 'info':
					$icon = 'i';
					break;
				case 'person':
					$icon = 'engineering';
					break;
				case 'check':
					$icon = 'done';
					break;
				default:
					$icon = '';
			endswitch;
			?>
			<div class="tulkinta">
				<p class="<?php echo esc_attr( trim( $class ) ); ?>">
					<span class="material-symbols tulkinta-symbol" aria-hidden="true">
						<?php echo esc_html( $icon ); ?>
					</span>

					<?php echo esc_html( $tulkinta['label'] ); ?>
				</p>
			</div>
		<?php endif; ?>

		<?php
		$output = wp_kses(
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
				'table'  => array(
					'style' => array(),
				),
				'tbody'  => array(
					'style' => array(),
				),
				'td'     => array(
					'style' => array(),
				),
				'tfoot'  => array(
					'style' => array(),
				),
				'th'     => array(
					'style' => array(),
				),
				'thead'  => array(
					'style' => array(),
				),
				'tr'     => array(
					'style' => array(),
				),
			)
		);
		?>

		<?php if ( $output ) : ?>
			<?php echo $output; // phpcs:ignore ?>
		<?php else : ?>
			<p class="hidden empty-text">&nbsp;</p>
		<?php endif; ?>
	</div>
</div>
