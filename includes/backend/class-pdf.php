<?php
/**
 * Class createPDF
 *
 * Perustuu http://www.fpdf.org/en/script/script53.php
 */
class Topten_PDF {
	/**
	 * REST API namespace
	 *
	 * @var string
	 */
	protected $namespace = 'topten/v1';

	/**
	 * Sallitut tagit
	 *
	 * @var string[]
	 */
	protected $allowed_tags = array(
		'a',
		'b',
		'br',
		'div',
		'em',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'i',
		'img',
		'li',
		'ol',
		'p',
		'span',
		'strong',
		'ul',
	);

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
						'callback' => array( $this, 'endpoint' ),
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
	public function endpoint( $request ) {
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
		$html        = strip_tags( htmlspecialchars( $request->get_param( 'html' ) ), $this->allowed_tags );

		// Luodaan PDF luokka
		require_once get_template_directory() . '/includes/fpdf/fpdfa.php';

		// Määritetään FPDF:n fonttien polku oikeaksi
		define( 'FPDF_FONTPATH', get_template_directory() . '/fonts' ); // phpcs:ignore

		$pdf = new FPDFA( 'P', 'mm', 'A4', $title, $article_url );

		$pdf->AddFont( 'Roboto', '', 'Roboto-Regular.php' );
		$pdf->SetFont( 'Roboto', '', 12 );

		$pdf->AddPage();

		$this->write_html( $pdf, $html );

		$response = new WP_REST_Response( base64_encode( $pdf->Output( 's' ) ) );

		return rest_ensure_response( $response );
	}

	/**
	 * Kirjoittaa HTML-koodin PDF-tiedostoon
	 *
	 * @param FPDF $pdf PDF-objekti
	 */
	private function doc_loop( &$pdf, $node ) {
		foreach ( $node->childNodes as $child ) {
			switch ( $child->nodeName ) {
				case 'h1':
					$pdf->Ln( 5 );
					$pdf->SetFontSize( 24 );
					break;
				case 'h2':
					$pdf->Ln( 5 );
					$pdf->SetFontSize( 50 );
					break;
			}

			$pdf->Write( 10, 'tes111t' );

			// if ( $node->hasChildNodes() ) {
			// $this->doc_loop( $child );
			// }
		}
	}

	/**
	 *
	 */
	private function write_html( &$pdf, $html ) {
		$html = strip_tags( $html, $this->allowed_tags );

		$doc = new DOMDocument( '1.0', 'UTF-8' );

		@$doc->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD ); // phpcs:ignore

		$this->doc_loop( $pdf, $doc );
	}

}
