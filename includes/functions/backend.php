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
 * Inits the backend functionality
 *
 * @package Topten
 *
 * @since 1.0.0
 */
function topten_backend_init() {
	require_once get_template_directory() . '/includes/backend/class-topten.php';
	require_once get_template_directory() . '/includes/backend/class-topten-admin.php';

	new Topten();
	new Topten_Admin();
}

/**
 * Returns post status (draft, published, deleted)
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param int $post_id Post ID, default get_the_id()
 *
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

/**
 * Returns post secondary status
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param int $post_id Post ID, default get_the_id()
 *
 * @return string
 */
function topten_get_post_secondary_status( $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}

	$status = get_field( 'card_status', $post_id );

	$status = $status['value'] ?? 'draft';

	if ( 'deleted' === $status ) {
		return 'Poistettu';
	}

	$selector = 'card_status_' . $status;

	$secondary_status = get_field( $selector, $post_id );

	if ( $secondary_status ) {
		return $secondary_status['label'];
	}

	return '';
}

/**
 * Get card language
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param int $post_id Post ID
 *
 * @return string
 */
function topten_get_card_language( $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}

	$language = get_field( 'language', $post_id );

	if ( $language ) {
		return $language['label'];
	}

	return 'Suomi';
}

/**
 * Get card language versions
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param int $post_id Post ID
 *
 * @return array Array of languages
 */
function topten_get_card_language_versions( $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}

	$languages = array(
		'fi' => array(
			'post'         => false,
			'current_lang' => false,
			'lang_name'    => 'Suomi',
		),
		'sv' => array(
			'post'         => false,
			'current_lang' => false,
			'lang_name'    => 'Ruotsi',
		),
		'en' => array(
			'post'         => false,
			'current_lang' => false,
			'lang_name'    => 'Englanti',
		),
	);

	$card_language  = get_field( 'language', $post_id );
	$card_lang_slug = $card_language['value'] ?? 'fi';

	foreach ( $languages as $slug => $language ) {
		if ( $slug === $card_lang_slug ) {
			$languages[ $slug ]['post']         = get_post( $post_id );
			$languages[ $slug ]['current_lang'] = true;
		} else {
			$selector = 'version_' . $slug;

			$languages[ $slug ]['post'] = get_field( $selector, $post_id );
		}
	}

	return $languages;
}
