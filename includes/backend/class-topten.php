<?php
defined( 'ABSPATH' ) || exit;

/**
 * Topten main class used in frontend
 * Loads textdomain and includes other classes
 *
 * @since 1.0.0
 *
 * @package Topten
 */
class Topten {
	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		require_once 'classes/class-ajax.php';

		new Topten_Ajax();
	}

	/**
	 * Load textdomain (topen)
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'topten', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}
