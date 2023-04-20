<?php
defined( 'ABSPATH' ) || exit;

/**
 * Korttien elinkaari
 */
class Topten_Lifecycle extends Topten {
	/**
	 * Lokit
	 *
	 * @var object
	 */
	protected $logger = null;

	/**
	 * Postin ID
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Postin päätila, default 'draft'
	 *
	 * @var string
	 */
	protected $primary_status = 'draft';

	/**
	 * Postin toissijainen tila
	 *
	 * @var string
	 */
	protected $secondary_status = '';

	/**
	 * Kortin vanha päätila
	 *
	 * @var string
	 */
	protected $old_primary_status = '';

	/**
	 * Kortin vanha toissijainen tila
	 *
	 * @var string
	 */
	protected $old_secondary_status = '';

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'acf/save_post', array( $this, 'before_save' ), 5, 1 );
		add_action( 'acf/save_post', array( $this, 'after_save' ), 20, 1 );
	}

	/**
	 * Haetaan kortin tiedot ennen tallennusta, hookattu kiinni 'acf/pre_save_post'
	 *
	 * @param int $post_id Postin ID
	 * @see acf/pre_save_post
	 */
	public function before_save( $post_id ) {
		$primary_status           = get_field( 'card_status', $post_id );
		$this->old_primary_status = $primary_status['value'] ?? 'draft';

		$secondary_status       = get_field( 'card_status_' . $this->old_primary_status, $post_id );
		$this->secondary_status = $secondary_status['value'] ?? '';

		json_log( $secondary_status );

	}

	/**
	 * Kortin tallentamisen funktio, hookattu kiinni 'acf/save_post'
	 *
	 * @param int $post_id Postin ID
	 * @see acf/save_post
	 */
	public function after_save( $post_id ) {
		$card_status = get_field( 'card_status', $post_id );

		// Asetetaan postin ID luokan muuttujaan
		$this->post_id = $post_id;

		$type = get_post_type( $this->post_id );

		// Tarkistetaan onko postin tyyppi kortti, jos ei ole, ei tehdä mitään
		if ( ! $this->is_card( $type ) ) {
			return;
		}

		// Päätila (publish / draft), jos tilaa ei jostain syystä ole asetettu, oletetaan että se on draft
		$primary_status       = get_field( 'card_status', $this->post_id );
		$this->primary_status = $primary_status['value'] ?? 'draft';

		$secondary_status       = get_field( 'card_status_' . $this->primary_status, $this->post_id );
		$this->secondary_status = $secondary_status['value'] ?? '';

		$post_array = array(
			'ID' => $this->post_id,
		);

		// Asetetaan postin tila päätilan mukaan. Jos päätila on 'publish', asetetaan postin tila 'publish', muuten 'draft'
		if ( 'publish' === $primary_status ) {
			$post_array['post_status'] = 'publish';
		} else {
			$post_array['post_status'] = 'draft';
		}

		wp_update_post( $post_array );
	}
}
