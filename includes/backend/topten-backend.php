<?php
/**
 * Plugin Name: Topten backend
 * Version: 0.0.1
 * Description: Topten backend
 * Author: Mainostoimisto Groteski Oy
 *
 * @package Topten
 */

defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', 'topten_backend_init' );

/**
 * Käynnistää backendin
 */
function topten_backend_init() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/public-functions.php';
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-topten.php';

	new Topten();
}
