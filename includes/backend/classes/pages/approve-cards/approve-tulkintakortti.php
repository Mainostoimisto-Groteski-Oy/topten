<?php
if ( ! current_user_can( 'approve_cards' ) ) {
	wp_die( esc_html__( 'Sinulla ei ole oikeuksia tälle sivulle.', 'topten' ) );
}
?>

<h1>
	<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
</h1>

<div class="tt-message-row hidden" style="margin-bottom: 15px;"></div>

<?php $this->render_table( 'tulkintakortti' ); ?>
