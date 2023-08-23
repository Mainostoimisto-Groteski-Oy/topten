<?php

/**
 * Topten Ajax
 */
class Topten_Ajax extends Topten {
	/**
	 * Card codes table
	 *
	 * @var string
	 */
	protected $card_table_name = 'topten_card_codes';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->maybe_create_table();

		add_action( 'wp_ajax_topten_generate_pdf', array( $this, 'generate_pdf_ajax' ) );
		add_action( 'wp_ajax_nopriv_topten_generate_pdf', array( $this, 'generate_pdf_ajax' ) );

		// Save card to database
		add_action( 'wp_ajax_topten_save_card', array( $this, 'save_card_ajax' ) );
		add_action( 'wp_ajax_nopriv_topten_save_card', array( $this, 'save_card_ajax' ) );

		// Load card from database
		add_action( 'wp_ajax_topten_load_card', array( $this, 'load_card_ajax' ) );
		add_action( 'wp_ajax_nopriv_topten_load_card', array( $this, 'load_card_ajax' ) );
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
	 * Maybe create database table for cards
	 */
	public function maybe_create_table() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . $this->card_table_name;

		// Taulussa on ID, aika, tyyppi ja viesti
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			code varchar(32) NOT NULL,
			data longtext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		maybe_create_table( $table_name, $sql );
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

	/**
	 * Generate card code
	 *
	 * @return string Card code
	 */
	protected function generate_code(): string {
		$x    = time() * wp_rand( 1111, 9999 );
		$x    = substr( $x, 0, 32 );
		$code = md5( $x );

		return $code;
	}

	/**
	 * Save card to database
	 */
	public function save_card_ajax() {
		check_ajax_referer( 'nonce', 'nonce' );

		$data = isset( $_POST['data'] ) ? $this->sanitize_array( $_POST['data'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		global $wpdb;

		$table_name = $wpdb->prefix . $this->card_table_name;

		$code = $this->generate_code();

		$wpdb->insert(
			$table_name,
			array(
				'time' => current_time( 'mysql' ),
				'code' => $code,
				'data' => maybe_serialize( $data ),
			)
		);

		wp_send_json_success( $code );
	}

	/**
	 * Load card from database
	 */
	public function load_card_ajax() {
		check_ajax_referer( 'nonce', 'nonce' );

		$code = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';

		if ( ! $code ) {
			wp_send_json_error( __( 'Anna koodi', 'topten' ) );
		}

		global $wpdb;

		$table_name = $wpdb->prefix . $this->card_table_name;

		$card = $wpdb->get_row( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare( 'SELECT data FROM %i WHERE code = %s', $table_name, $code )
		);

		if ( ! $card ) {
			wp_send_json_error( __( 'Syöttämääsi koodia ei löytynyt.', 'topten' ) );
		}

		$response = maybe_unserialize( $card->data );

		wp_send_json_success( $response );
	}
}
