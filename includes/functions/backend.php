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

/**
 * Käynnistää backendin
 */
function topten_backend_init() {
	require_once get_template_directory() . '/includes/backend/class-topten.php';

	new Topten();
}

/**
 * Palauttaa postin tilan
 *
 * @param int $post_id Postin ID, default get_the_id()
 * @return string
 */
function topten_get_post_status( $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}

	$status = get_field( 'card_status', $post_id );

	if ( $status ) {
		return $status['label'];
	}

	return 'Luonnos';
}
