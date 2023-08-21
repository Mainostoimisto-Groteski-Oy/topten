<?php
$columns = get_field( 'columns' ) ?: 1;

$template = array();

for ( $i = 0; $i < $columns; $i++ ) {
	$template[] = array( 'acf/sarake' );
}

$allowed_blocks = array(
	'acf/sarake',
);
?>

<section class="row-block">
	<div class="grid">
		<InnerBlocks template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed_blocks ) ); ?>" templateLock="all" />
	</div>
</section>
