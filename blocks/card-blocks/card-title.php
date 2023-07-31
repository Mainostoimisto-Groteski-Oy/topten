<?php
global $block_title_ids;

$title_tag       = get_field( 'title_tag' ) ?: 'h2';
$title           = get_field( 'title' );
$ignore_toc      = get_field( 'ignore_toc' );
$show_toc_number = get_field( 'show_toc_number' );
$id              = sanitize_title( $title );
$title_number    = '';

if ( ! isset( $block_title_ids[ $id ] ) ) {
	$block_title_ids[ $id ] = array(
		'id'    => $id,
		'count' => 0,
	);
} else {
	$block_title_ids[ $id ]['count']++;

	$id = $id . '-' . $block_title_ids[ $id ]['count'];
}

if ( ! $ignore_toc && $show_toc_number ) {
	$title_number = topten_get_title_number( $id );
}
?>

<div class="title-wrapper column" style="<?php topten_get_block_width(); ?>">
	<?php topten_get_desc(); ?>

	<?php echo sprintf( '<%1$s class="title" id="%3$s">%4$s %2$s</%1$s>', esc_attr( $title_tag ), wp_kses_post( $title ), esc_attr( $id ), esc_html( $title_number ) ); ?>
</div>
