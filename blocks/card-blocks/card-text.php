<?php
$block_id = get_field( 'block_id' );

$text     = get_field( 'text' );
$tulkinta = get_field( 'tulkinta' );
$color    = false;

if ( $tulkinta && 'none' !== $tulkinta['value'] ) {
	$color = topten_get_guide_color( $tulkinta['value'] );

	$class  = $color ? ' ' . $color : '';
	$class .= ! empty( $tulkinta['value'] ) ? ' ' . $tulkinta['value'] : '';
}
?>

<div class="column-item card-text-block" style="<?php topten_get_block_width(); ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">
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
				<p class="tulkinta-text <?php echo esc_attr( trim( $class ) ); ?>">
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
					'href'           => array(),
					'title'          => array(),
					'style'          => array(),
					'target'         => array(),
					'data-topten-id' => array(),
				),
				'b'      => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'br'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'em'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'h1'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'h2'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'h3'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'h4'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'h5'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'h6'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'i'      => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'img'    => array(
					'alt'            => array(),
					'src'            => array(),
					'data-topten-id' => array(),
				),
				'li'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'ol'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'p'      => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'strong' => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'ul'     => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'sup'    => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'sub'    => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'table'  => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'tbody'  => array(
					'style'          => array(),
					'data-topten-id' => array(),

				),
				'td'     => array(
					'style'          => array(),
					'colspan'        => array(),
					'rowspan'        => array(),
					'data-topten-id' => array(),
				),
				'tfoot'  => array(
					'style'          => array(),
					'data-topten-id' => array(),
				),
				'th'     => array(
					'style'          => array(),
					'colspan'        => array(),
					'rowspan'        => array(),
					'data-topten-id' => array(),
				),
				'thead'  => array(
					'style'          => array(),
					'data-topten-id' => array(),

				),
				'tr'     => array(
					'style'          => array(),
					'data-topten-id' => array(),

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
