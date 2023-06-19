<?php
if ( ! current_user_can( 'approve_for_profession' ) ) {
	wp_die( esc_html__( 'Sinulla ei ole oikeuksia tälle sivulle.', 'topten' ) );
}
?>

<h1>
	<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
</h1>

<?php $this->render_table( 'lomakekortti' ); ?>
