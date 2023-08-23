<?php

/**
 * Topten Ajax
 */
class Topten_Ajax extends Topten {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_topten_generate_pdf', array( $this, 'generate_pdf_ajax' ) );
		add_action( 'wp_ajax_nopriv_topten_generate_pdf', array( $this, 'generate_pdf_ajax' ) );
	}

	/**
	 * Sanitize array
	 *
	 * @param array $array Array to sanitize
	 */
	protected function sanitize_array( &$array ) {
		foreach ( $array as &$value ) {
			if ( is_array( $value ) ) {
				$this->sanitize_array( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}

		return $array;
	}

	/**
	 * PDF Ajax endpoint
	 */
	public function generate_pdf_ajax() {
		check_ajax_referer( 'nonce', 'nonce' );

		$title       = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$article_url = isset( $_POST['article_url'] ) ? esc_url( sanitize_text_field( $_POST['article_url'] ) ) : '';
		// $data        = isset( $_POST['data'] ) ? $this->sanitize_array( $_POST['data'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$data = isset( $_POST['data'] ) ? $_POST['data'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		require_once 'class-pdf.php';

		$pdf = new Topten_PDF( 'P', 'mm', 'A4', $title, $article_url );
		$pdf->generate_pdf( $data );

		wp_send_json_success( base64_encode( $pdf->Output( 's' ) ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
}
