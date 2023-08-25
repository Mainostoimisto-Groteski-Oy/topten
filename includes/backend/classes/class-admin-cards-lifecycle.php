<?php
defined( 'ABSPATH' ) || exit;

/**
 * Korttien elinkaari
 */
class Topten_Admins_Cards_Lifecycle extends Topten_Admin_Cards {
	/**
	 * Looger
	 *
	 * @var object
	 */
	protected $logger = null;

	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Card primary status, default 'draft'
	 *
	 * @var string
	 */
	protected $primary_status = 'draft';

	/**
	 * Card secondary status
	 *
	 * @var string
	 */
	protected $secondary_status = '';

	/**
	 * Card old primary status
	 *
	 * @var string
	 */
	protected $old_primary_status = '';

	/**
	 * Card old secondary status
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
	 * Get card data before saving and set it to class variables
	 * Hooked to 'acf/pre_save_post'
	 *
	 * @param int $post_id Post ID
	 * @see acf/pre_save_post
	 */
	public function before_save( $post_id ) {
		$primary_status           = get_field( 'card_status', $post_id );
		$this->old_primary_status = $primary_status['value'] ?? 'draft';

		$secondary_status           = get_field( 'card_status_' . $this->old_primary_status, $post_id );
		$this->old_secondary_status = $secondary_status['value'] ?? '';
	}

	/**
	 * Do stuff after saving card
	 * Hooked to 'acf/save_post'
	 *
	 * @param int $post_id Post ID
	 * @see acf/save_post
	 */
	public function after_save( $post_id ) {
		$type = get_post_type( $post_id );

		// Check if post type is card
		if ( ! $this->is_card( $type ) ) {
			return;
		}

		// Set post ID to class variable
		$this->post_id = $post_id;

		// Primary status (publish / draft), if status is not set for some reason, assume it's draft
		$primary_status       = get_field( 'card_status', $this->post_id );
		$this->primary_status = $primary_status['value'] ?? 'draft';

		// Secondary status
		$secondary_status       = get_field( 'card_status_' . $this->primary_status, $this->post_id );
		$this->secondary_status = $secondary_status['value'] ?? '';

		// Post array
		$post_array = array(
			'ID' => $this->post_id,
		);

		// Set post status based on primary status. If primary status is 'publish', set post status to 'publish', otherwise 'draft'
		if ( 'publish' === $this->primary_status && 'publish' !== $this->old_primary_status ) {
			$post_array['post_status']   = 'publish';
			$post_array['post_date']     = wp_date( 'Y-m-d H:i:s' );
			$post_array['post_date_gmt'] = gmdate( 'Y-m-d H:i:s' );
		} elseif ( 'deleted' === $this->primary_status ) {
			$post_array['post_status'] = 'deleted';
		} else {
			$post_array['post_status'] = 'draft';
		}

		if ( 'draft' === $this->primary_status ) {
			if ( 'pending_approval' === $this->secondary_status && 'pending_approval' !== $this->old_secondary_status ) {
				update_post_meta( $this->post_id, 'committer', get_current_user_id() );
			}
		}

		wp_update_post( $post_array );
	}
}
