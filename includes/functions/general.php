<?php
/**
 * Geneerisiä funktioita
 *
 * @package Topten
 */

/**
 * Kääntää parametrin JSONiksi ja kirjoittaa sen error_logiin
 *
 * @param any $data_to_log Logitettava data
 * @return void
 */
function json_log( $data_to_log ) { // phpcs:ignore
	error_log( wp_json_encode( $data_to_log ) ); // phpcs:ignore
}
