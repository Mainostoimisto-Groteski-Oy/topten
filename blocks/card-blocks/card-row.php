<?php
$columns = get_field( 'columns' ) ?: 1;

$template = array();

for ( $i = 0; $i < $columns; $i++ ) {
	$template[] = array( 'acf/sarake' );
}

$allowed_blocks = array(
	'acf/sarake',
);

$width_selector = $columns . '_column_width';

$column_widths = get_field( $width_selector );
$column_widths = json_decode( $column_widths );

$css_vars = '';

foreach ( $column_widths as $index => $column_width ) {
	++$index;
	$css_vars .= '--column-' . $index . ': ' . $column_width . ';';
}

echo $css_vars;

?>

<section class="row-block">
	<div class="grid" style="--columns: <?php echo esc_attr( $columns ); ?>;">
		<InnerBlocks template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" templateLock="all" />
	</div>
</section>
