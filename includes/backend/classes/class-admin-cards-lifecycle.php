<?php
defined( 'ABSPATH' ) || exit;

/**
 * Card lifecycle
 *
 * @since 1.0.0
 *
 * @package Topten\Admin\Cards
 */
class Topten_Admins_Cards_Lifecycle extends Topten_Admin_Cards {
	/**
	 * Class constructor
	 * Mainly inits hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init hooks
	 */
	public function init_hooks() {
		add_action( 'acf/save_post', array( $this, 'before_save' ), 5, 1 );
		add_action( 'acf/save_post', array( $this, 'after_save' ), 20, 1 );

		add_action( 'acf/pre_save_block', array( $this, 'pre_save_block' ), 99, 1 );
	}


	/**
	 * Get card data before saving and set it to class variables
	 * Hooked to 'acf/pre_save_post'
	 *
	 * @see https://www.advancedcustomfields.com/resources/acf-save_post/
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 */
	public function before_save( $post_id ) {
		// Set post ID to class variable
		$this->post_id = $post_id;

		$primary_status           = get_field( 'card_status', $post_id );
		$this->old_primary_status = $primary_status['value'] ?? 'draft';

		$secondary_status           = get_field( 'card_status_' . $this->old_primary_status, $post_id );
		$this->old_secondary_status = $secondary_status['value'] ?? '';
	}

	/**
	 * Do stuff after saving card
	 * Hooked to 'acf/save_post'
	 *
	 * @see https://www.advancedcustomfields.com/resources/acf-save_post/
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
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

			// Set old versions to 'draft' and update their slugs
			$versions = get_field( 'versions', $this->post_id );

			if ( $versions ) {
				foreach ( $versions as $version ) {
					if ( $version === $this->post_id ) {
						continue;
					}

					update_field( 'card_status', 'publish', $version );
					update_field( 'card_status_publish', 'expired', $version );
					$version_number = get_field( 'version', $version );

					if ( '-' === $version_number ) {
						$version_number = 0;
					}

					// Update slug to include version number
					$slug = sanitize_title( get_the_title( $version ) ) . '-' . $version_number;

					$x = wp_update_post(
						array(
							'ID'        => $version,
							'post_name' => $slug,
						)
					);
				}
			}

			// Update the slug of the published card
			$slug = sanitize_title( get_the_title( $this->post_id ) );

			wp_update_post(
				array(
					'ID'        => $this->post_id,
					'post_name' => $slug,
				)
			);
		} elseif ( 'publish' === $this->primary_status ) {
			$post_array['post_status'] = 'publish';
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

		// This has to be here because url isn't formed before update post
		if ( 'publish' === $this->primary_status && 'publish' !== $this->old_primary_status ) {
			$this->send_mail_to_subscribers( $this->post_id );
		}
	}

	/**
	 * Pre save block
	 * Adds block ID to block attributes and adds data-topten-id to all elements
	 * Hooked to 'acf/save_post'
	 *
	 * @see https://www.advancedcustomfields.com/resources/acf-save_post/
	 *
	 * @param array $attributes Block attributes
	 *
	 * @return array Block attributes
	 */
	public function pre_save_block( $attributes ) {
		// If post ID is not set, do not continue
		if ( ! $this->post_id ) {
			return $attributes;
		}

		// If anchor is not set
		if ( empty( $attributes['anchor'] ) ) {
			return $attributes;
		}

		$type = get_post_type( $this->post_id );

		// Check if post type is card
		if ( ! $this->is_card( $type ) ) {
			return $attributes;
		}

		if ( 'acf/teksti' === $attributes['name'] && isset( $attributes['data']['text'] ) ) {
			$text = wpautop( $attributes['data']['text'] );

			$text = '<div>' . $text . '</div>';

			$dom = new DOMDocument( '1.0', 'UTF-8' );
			@$dom->loadHTML( mb_convert_encoding( $text, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD ); // phpcs:ignore

			$container = $dom->getElementsByTagName( 'div' )->item( 0 );
			$container = $container->parentNode->removeChild( $container );

			while ( $dom->firstChild ) {
				$dom->removeChild( $dom->firstChild );
			}

			while ( $container->firstChild ) {
				$dom->appendChild( $container->firstChild );
			}

			// Add class to all elements
			$xpath = new DOMXPath( $dom );
			$nodes = $xpath->query( '//*' );

			$ids = array();

			foreach ( $nodes as $node ) {
				if ( ! $node->hasAttribute( 'data-topten-id' ) ) {
					$node->setAttribute( 'data-topten-id', uniqid() );
				} else {
					$id = $node->getAttribute( 'data-topten-id' );

					if ( in_array( $id, $ids, true ) ) {
						$id = uniqid();
						$node->setAttribute( 'data-topten-id', $id );
					}

					$ids[] = $id;
				}
			}

			$text = $dom->saveHTML();
			$text = mb_convert_encoding( $text, 'UTF-8', 'HTML-ENTITIES' );

			$attributes['data']['text'] = $text;
		}

		// If block ID is not already set
		if ( empty( $attributes['data']['block_id'] ) ) {
			$attributes['data']['block_id'] = $attributes['anchor'];
		} else {
			$old_version = get_post_meta( $this->post_id, 'old_version', true );
			$version     = get_field( 'version', $this->post_id );

			// If '-' is the old version, card should already have the block IDs set
			if ( '-' !== $old_version ) {
				// If version is not 'A', '-' or '', do not continue
				if ( 'a' === strtolower( $version ) || '-' === $version || '' === $version ) {
					$attributes['data']['block_id'] = $attributes['anchor'];
				}
			}
		}

		return $attributes;
	}

	/**
	 * Send email to subscribers upon post publish
	 *
	 * @todo This will need language versioning at some point
	 *
	 * @since 1.0.0
	 *
	 * @param int $card_id Card ID
	 */
	public function send_mail_to_subscribers( $card_id ) {
		$type = get_post_type( $card_id );

		// Check if post type is card
		if ( ! $this->is_card( $type ) ) {
			return;
		}

		// Email on publish is an acf true/false field that can be set to false to prevent email from being sent
		$email_on_publish = get_field( 'email_on_publish', $card_id );
		if ( ! $email_on_publish ) {
			return;
		}

		// Email has been sent will be set to true after email has been sent and no more emails will be sent if edits (eg. typos) are made to the published card
		// This is probably unnecessary since we check in another function if the card's previous status was not published and this function isn't called otherwise, but just in case
		$email_has_been_sent = get_field( 'email_has_been_sent', $card_id );
		if ( $email_has_been_sent ) {
			return;
		} else {
			update_field( 'email_has_been_sent', true, $card_id );
		}

		// Get list of subscribers
		$subscribers = get_field( 'subscribers', 'options' );

		if ( ! $subscribers ) {
			return;
		}

		// Message will differ a bit depending on whether card is new or updated
		$version = get_field( 'version', $card_id );

		if ( 'A' === $version ) {
			$subject_start = esc_html__( 'Uusi Topten-kortti:', 'topten' );
		} else {
			$subject_start = esc_html__( 'Päivitetty Topten-kortti:', 'topten' );
		}

		$identifier_start = get_field( 'identifier_start', $card_id );
		$identifier_end   = get_field( 'identifier_end', $card_id );
		$card_name        = $identifier_start . ' ' . $identifier_end . ' ' . $version . ' ' . get_the_title( $card_id );
		$subject          = '[Topten] ' . $subject_start . ' ' . $card_name;

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);

		// Email everyone in the list
		foreach ( $subscribers as $subscriber ) {
			$body   = '';
			$to     = $subscriber['email'];
			$secret = 'dGA6jTPZk5D7e2Fg73kEW1V9Kyj867oJ';
			$token  = hash_hmac( 'sha256', $to, $secret );
			$link   = home_url() . '/unsubscribe?email=' . $to . '&token=' . $token;

			if ( 'A' === $version ) {
				$body  = '<p>Hei,</p>';
				$body .= '<p>Uusi Topten-kortti "' . $card_name . '" on julkaistu.</p>';
				$body .= '<p>Pääset tutustumaan korttiin tästä linkistä <a href="' . get_permalink( $card_id ) . '">' . get_permalink( $card_id ) . '</a>.</p>';
				$body .= '<p>Jos et halua saada näitä viestejä, voit perua automaattisen tilauksen <a href="' . $link . '">tästä linkistä</a>.</p>';
			} else {
				$body  = '<p>Hei,</p>';
				$body .= '<p>Topten-kortti "' . $card_name . '" on päivitetty.</p>';
				$body .= '<p>Pääset tutustumaan korttiin tästä linkistä <a href="' . get_permalink( $card_id ) . '">' . get_permalink( $card_id ) . '</a>.</p>';
				$body .= '<p>Jos et halua saada näitä viestejä, voit perua automaattisen tilauksen <a href="' . $link . '">tästä linkistä</a>.</p>';
			}

			wp_mail( $to, $subject, $body, $headers );
		}
	}
}
