<?php
defined( 'ABSPATH' ) || exit;

/**
 * Pluginin pääluokka
 */
class Topten {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		require_once 'classes/class-rest.php';

		new Topten_REST();
	}

	/**
	 * Lataa textdomainin
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'topten', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}
