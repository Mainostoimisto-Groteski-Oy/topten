<?php
defined( 'ABSPATH' ) || exit;

/**
 * Topten main class
 */
class Topten {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		require_once 'classes/class-ajax.php';

		new Topten_Ajax();
	}

	/**
	 * Load textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'topten', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}
