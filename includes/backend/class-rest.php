<?php

/**
 * Topten REST API
 */
class Topten_REST {
	/**
	 * REST API namespace
	 *
	 * @var string
	 */
	protected $namespace = 'topten/v1';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->init_rest_api();
	}

	/**
	 * Init rest api
	 */
	public function init_rest_api() {
		add_action(
			'rest_api_init',
			function() {
				register_rest_route(
					$this->namespace,
					'/pdf',
					array(
						'methods'  => 'POST',
						'callback' => array( $this, 'pdf_endpoint' ),
					)
				);
			}
		);
	}

	/**
	 * Luo PDF-tiedoston HTML-koodista
	 *
	 * @param WP_REST_Request $request Request
	 */
	public function pdf_endpoint( $request ) {
		// Tarkistetaan nonce
		if ( ! wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			$response = new WP_Error(
				'Invalid nonce',
				'Invalid nonce',
				array(
					'status' => 400,
				)
			);

			return rest_ensure_response( $response );
		}

		// Sanitoidaan parametrit
		$title       = sanitize_text_field( $request->get_param( 'title' ) );
		$article_url = esc_url( $request->get_param( 'article_url' ) );
		$data        = $request->get_param( 'data' );

		array_walk_recursive( $data, 'sanitize_text_field' );

		require_once get_template_directory() . '/includes/backend/class-pdf.php';

		$pdf = new Topten_PDF( 'P', 'mm', 'A4', $title, $article_url );

		$pdf->generate_pdf( $data );

		// Palautetaan PDF base64-koodattuna
		$response = new WP_REST_Response( base64_encode( $pdf->Output( 's' ) ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		return rest_ensure_response( $response );
	}
}
