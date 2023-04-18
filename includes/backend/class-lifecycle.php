<?php
defined( 'ABSPATH' ) || exit;

/**
 * Korttien elinkaari
 */
class Topten_Lifecycle {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'acf/save_post', array( $this, 'on_save' ), 20 );
	}

	/**
	 * Kortin tallentamisen funktio, hookattu kiinni 'acf/save_post'
	 *
	 * @param int $post_id Postin ID
	 * @see acf/save_post
	 */
	public function on_save( $post_id ) {
		$card_types = array(
			'tulkintakortti',
			'ohjekortti',
			'lomakekortti',
		);

		$type = get_post_type( $post_id );

		// Tarkistetaan onko postin tyyppi kortti, jos ei ole, ei tehdä mitään
		if ( ! in_array( $type, $card_types, true ) ) {
			return;
		}

		// Päätila (publish / draft), jos tilaa ei jostain syystä ole asetettu, oletetaan että se on draft
		$primary_status = get_field( 'card_status', $post_id );
		$primary_status = $primary_status['value'] ?? 'draft';

		$secondary_status = get_field( 'card_status_' . $primary_status, $post_id );

		$post_array = array(
			'ID' => $post_id,
		);

		// Asetetaan postin tila päätilan mukaan
		if ( 'publish' === $primary_status ) {
			$post_array['post_status'] = 'publish';
		} else {
			$post_array['post_status'] = 'draft';
		}

		wp_update_post( $post_array );
	}
}
