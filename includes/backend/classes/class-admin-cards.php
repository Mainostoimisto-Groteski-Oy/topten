<?php

/**
 * Topten kortit
 */
class Topten_Admin_Cards extends Topten_Admin {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->add_actions();

		require_once 'class-admin-cards-lifecycle.php';

		new Topten_Admins_Cards_Lifecycle();
	}

	/**
	 * Actions and filters
	 */
	private function add_actions() {
		// Add link to row actions
		add_filter( 'post_row_actions', array( $this, 'copy_card_action' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'copy_card_language_action' ), 10, 2 );

		// Copy card action
		add_filter( 'admin_action_tt_copy_card', array( $this, 'copy_card' ) );
		add_filter( 'admin_action_tt_copy_card_language', array( $this, 'copy_card_language' ) );

		// Approve card for municipality
		add_filter( 'admin_action_tt_approve_card_for_municipality', array( $this, 'approve_card_for_municipality' ) );

		// Disapprove card for municipality
		add_filter( 'admin_action_tt_disapprove_card_for_municipality', array( $this, 'disapprove_card_for_municipality' ) );

		// reorder_columns filters
		add_filter( 'manage_tulkintakortti_posts_columns', array( $this, 'reorder_columns' ), 20, 1 );
		add_filter( 'manage_ohjekortti_posts_columns', array( $this, 'reorder_columns' ), 20, 1 );
		add_filter( 'manage_lomakekortti_posts_columns', array( $this, 'reorder_columns' ), 20, 1 );

		// set_sortable_columns filters
		add_filter( 'manage_edit-tulkintakortti_sortable_columns', array( $this, 'set_sortable_columns' ) );
		add_filter( 'manage_edit-ohjekortti_sortable_columns', array( $this, 'set_sortable_columns' ) );
		add_filter( 'manage_edit-lomakekortti_sortable_columns', array( $this, 'set_sortable_columns' ) );

		// add_custom_columns filters
		add_filter( 'manage_tulkintakortti_posts_columns', array( $this, 'add_custom_columns' ) );
		add_filter( 'manage_ohjekortti_posts_columns', array( $this, 'add_custom_columns' ) );
		add_filter( 'manage_lomakekortti_posts_columns', array( $this, 'add_custom_columns' ) );

		// add_custom_column_data actions
		add_action( 'manage_tulkintakortti_posts_custom_column', array( $this, 'add_custom_column_data' ), 10, 2 );
		add_action( 'manage_ohjekortti_posts_custom_column', array( $this, 'add_custom_column_data' ), 10, 2 );
		add_action( 'manage_lomakekortti_posts_custom_column', array( $this, 'add_custom_column_data' ), 10, 2 );

		add_action( 'pre_get_posts', array( $this, 'sort_cards_by_status' ) );
	}

	/**
	 * Add custom columns
	 *
	 * @param string[] $columns Columns
	 */
	public function add_custom_columns( $columns ) {
		$columns['language']        = esc_html__( 'Kieli', 'topten' );
		$columns['other_languages'] = esc_html__( 'Kieliversiot', 'topten' );
		$columns['version']         = esc_html__( 'Versio', 'topten' );
		$columns['status']          = esc_html__( 'Tila', 'topten' );
		$columns['modified']        = esc_html__( 'Muokattu', 'topten' );
		$columns['modified_author'] = esc_html__( 'Muokkaaja', 'topten' );
		$columns['published']       = esc_html__( 'Julkaistu', 'topten' );

		return $columns;
	}

	/**
	 * Add data to custom columns
	 *
	 * @param string $column Sarakkeen nimi
	 * @param int    $post_id Postin ID
	 */
	public function add_custom_column_data( $column, $post_id ) {
		// Language column, show card language
		if ( 'language' === $column ) {
			$language = topten_get_card_language( $post_id );

			echo esc_html( $language );
		}

		// Other languages column, show links to other language versions
		if ( 'other_languages' === $column ) {
			$languages = topten_get_card_language_versions( $post_id );

			foreach ( $languages as $language ) {
				if ( $language['current_lang'] || ! $language['post'] ) {
					continue;
				}

				$edit_link = get_edit_post_link( $language['post'] );

				echo sprintf( '<a href="%s">%s</a><br>', esc_url( $edit_link ), esc_html( $language['lang_name'] ) );
			}
		}

		// Version column, show card version (A, B, C, etc.)
		if ( 'version' === $column ) {
			$version = get_field( 'version', $post_id ) ?: 'A';

			echo esc_html( $version );
		}

		// Status column, show card status as text (Julkaistu, Luonnos, Poistettu, etc.)
		if ( 'status' === $column ) {
			echo esc_html( topten_get_post_secondary_status( $post_id ) );
		}

		// Modified author column, show name of the user who last modified the card
		if ( 'modified_author' === $column ) {
			$last_id = get_post_meta( get_post()->ID, '_edit_last', true );

			if ( $last_id ) {
				$last_user = get_user_by( 'id', $last_id );

				if ( $last_user ) {
					echo esc_html( $last_user->display_name );
				}
			}
		}

		// Modified column, show date and time when the card was last modified
		if ( 'modified' === $column ) {
			echo esc_html( get_the_modified_date( 'j.n.Y H:i', $post_id ) );
		}

		if ( 'published' === $column ) {
			echo esc_html( get_the_date( 'j.n.Y H:i', $post_id ) );
		}
	}

	/**
	 * Reorder columns
	 *
	 * @param string[] $columns Columns
	 */
	public function reorder_columns( $columns ) {
		$hidden_columns = array(
			'wpseo-score',
			'wpseo-score-readability',
			'wpseo-title',
			'wpseo-metadesc',
			'wpseo-focuskw',
			'wpseo-links',
			'wpseo-linked',
			'date',
		);

		$ordered_columns = array();

		foreach ( $columns as $index => $value ) {
			if ( in_array( $index, $hidden_columns, true ) ) {
				continue;
			}

			// Insert our custom columns before the author column
			if ( 'author' === $index ) {
				$ordered_columns['language']        = 'language';
				$ordered_columns['other_languages'] = 'other_languages';
				$ordered_columns['version']         = 'version';
				$ordered_columns['status']          = 'status';
				$ordered_columns['modified']        = 'modified';
				$ordered_columns['modified_author'] = 'modified_author';
				$ordered_columns['published']       = 'published';
			}

			$ordered_columns[ $index ] = $value;
		}

		return $ordered_columns;
	}

	/**
	 * Set sortable columns
	 *
	 * @param string[] $columns Columns
	 */
	public function set_sortable_columns( $columns ) {
		$columns['status']    = 'status';
		$columns['modified']  = 'modified';
		$columns['published'] = 'published';

		return $columns;
	}

	/**
	 * Add support for sorting cards by status
	 *
	 * @param WP_Query $query Query
	 */
	public function sort_cards_by_status( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'status' !== $orderby ) {
			return;
		}

		$query->set( 'meta_key', 'card_status' );
		$query->set( 'orderby', 'meta_value' );

		return $query;
	}

	/**
	 * Check if post type is a card (is in $this->card_types)
	 *
	 * @param string $post_type Post type to check (slug)
	 */
	public function is_card( $post_type ) {
		return in_array( $post_type, $this->card_types, true );
	}

	/**
	 * Copy button to card actions
	 *
	 * @param string[] $actions Existing actions
	 * @param WP_Post  $post    Post
	 */
	public function copy_card_action( $actions, $post ) {
		$type = get_post_type( $post );

		// Only show copy button for cards
		if ( ! $this->is_card( $type ) ) {
			return $actions;
		}

		// URL is admin.php with action tt_copy_card, the post ID and a nonce
		$url = add_query_arg(
			array(
				'action' => 'tt_copy_card',
				'post'   => $post->ID,
			),
			admin_url( 'admin-post.php' ),
		);

		$url = wp_nonce_url( $url, 'tt_copy_card_' . $post->ID );

		$actions[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Kopioi', 'topten' ) . '</a>';

		return $actions;
	}

	/**
	 * Copy as language version to card actions
	 *
	 * @param string[] $actions Existing actions
	 * @param WP_Post  $post    Post
	 */
	public function copy_card_language_action( $actions, $post ) {
		$type = get_post_type( $post );

		// Only show copy button for cards
		if ( ! $this->is_card( $type ) ) {
			return $actions;
		}

		// URL is admin.php with action tt_copy_card_language, the post ID and a nonce
		$url = add_query_arg(
			array(
				'action' => 'tt_copy_card_language',
				'post'   => $post->ID,
			),
			admin_url( 'admin-post.php' ),
		);

		$url = wp_nonce_url( $url, 'tt_copy_card_language_' . $post->ID );

		$actions[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Kopioi kieliversioksi', 'topten' ) . '</a>';

		return $actions;
	}

	/**
	 * Copy card as a new draft
	 */
	public function copy_card() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Check if we have a post ID and action tt_copy_card
		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'tt_copy_card' !== $_GET['action'] ) ) {
			return;
		}

		$post_id = intval( $_GET['post'] );

		// Check nonce
		check_admin_referer( 'tt_copy_card_' . $post_id );

		// Check if source post exists
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		// Check if user has permission to edit the post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Create a new post, copy post data from the source post
		$post_array = array(
			'post_title'            => get_the_title( $post_id ),
			'comment_status'        => $post->comment_status,
			'ping_status'           => $post->ping_status,
			'post_content'          => $post->post_content,
			'post_content_filtered' => $post->post_content,
			'post_status'           => 'draft',
			'post_type'             => get_post_type( $post_id ),
			'post_mime_type'        => $post->post_mime_type,
			'post_parent'           => $post->post_parent,
		);

		$new_post_id = wp_insert_post( wp_slash( $post_array ) );

		// Copy fields from source post
		$fields = get_fields( $post_id );

		if ( $fields ) {
			foreach ( $fields as $key => $value ) {
				update_field( $key, $value, $new_post_id );
			}
		}

		// Source post version
		$version = get_field( 'version', $new_post_id );

		// If version is set, increment it by one, else set it to A
		if ( $version ) {
			$version = ++$version;
		} else {
			$version = 'A';
		}

		update_field( 'version', $version, $new_post_id );

		// Redirect to the new post
		wp_safe_redirect(
			add_query_arg(
				array(
					'post'   => $new_post_id,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			)
		);
	}

	/**
	 * Copy card as a new language version
	 */
	public function copy_card_language() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Check if we have a post ID and action tt_copy_card_language
		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'tt_copy_card_language' !== $_GET['action'] ) ) {
			return;
		}

		$post_id = intval( $_GET['post'] );

		// Check nonce
		check_admin_referer( 'tt_copy_card_language_' . $post_id );

		// Check if source post exists
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		// Check if user has permission to edit the post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Create a new post, copy post data from the source post
		$post_array = array(
			'post_title'            => get_the_title( $post_id ),
			'comment_status'        => $post->comment_status,
			'ping_status'           => $post->ping_status,
			'post_content'          => $post->post_content,
			'post_content_filtered' => $post->post_content,
			'post_status'           => 'draft',
			'post_type'             => get_post_type( $post_id ),
			'post_mime_type'        => $post->post_mime_type,
			'post_parent'           => $post->post_parent,
		);

		$new_post_id = wp_insert_post( wp_slash( $post_array ) );

		// Copy fields from source post
		$fields = get_fields( $post_id );

		if ( $fields ) {
			foreach ( $fields as $key => $value ) {
				update_field( $key, $value, $new_post_id );
			}
		}

		// Source post language, default to Finnish
		$source_language = get_field( 'language', $post_id );
		$source_language = $source_language['value'] ?? 'fi';

		// Change language to the other one
		if ( 'fi' === $source_language ) {
			$new_language = 'sv';
		} else {
			$new_language = 'fi';
		}

		update_field( 'language', $new_language, $new_post_id );

		// Set source post language version to the new post language version
		update_field( 'version_' . $source_language, $post_id, $new_post_id );

		// Set new post language version to the source post language version
		update_field( 'version_' . $new_language, $new_post_id, $post_id );

		// Redirect to the new post
		wp_safe_redirect(
			add_query_arg(
				array(
					'post'   => $new_post_id,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			)
		);
	}

	/**
	 * Approve card for municipality
	 */
	public function approve_card_for_municipality() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Check if we have a post ID and action tt_approve_card
		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'tt_approve_card_for_municipality' !== $_GET['action'] ) ) {
			wp_die( esc_html__( 'Jotain meni vikaan. Yritä uudestaan', 'topten' ) );
		}

		$post_id = intval( $_GET['post'] );

		// Check nonce
		check_admin_referer( 'tt_approve_card_for_municipality_' . $post_id );

		// Check if user has permission to approve the card
		if ( ! current_user_can( 'approve_for_municipality' ) ) {
			wp_die( esc_html__( 'Sinulla ei ole oikeuksia aktivoida kortteja', 'topten' ) );
		}

		$post = get_post( $post_id );

		// Check if card exists
		if ( ! $post ) {
			wp_die( esc_html__( 'Korttia ei löytynyt', 'topten' ) );
		}

		// Get user municipality
		$user_municipality = get_field( 'user_municipality', 'user_' . get_current_user_id() );

		if ( ! $user_municipality ) {
			wp_die( esc_html__( 'Käyttäjällesi ei ole asetettu kuntaa. Ota yhteys ylläpitoon.', 'topten' ) );
		}

		$set_term = wp_set_object_terms( $post_id, $user_municipality, 'kunta', true );

		$redirect_url = wp_get_referer();

		if ( is_wp_error( $set_term ) ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'success' => 0,
					),
					$redirect_url
				)
			);
		} else {
			wp_safe_redirect(
				add_query_arg(
					array(
						'success' => 1,
					),
					$redirect_url
				)
			);
		}
	}

	/**
	 * Disapprove card for municipality
	 */
	public function disapprove_card_for_municipality() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Check if we have a post ID and action tt_disapprove_card
		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'tt_disapprove_card_for_municipality' !== $_GET['action'] ) ) {
			wp_die( esc_html__( 'Jotain meni vikaan. Yritä uudestaan', 'topten' ) );
		}

		$post_id = intval( $_GET['post'] );

		// Check nonce
		check_admin_referer( 'tt_disapprove_card_for_municipality_' . $post_id );

		// Check if user has permission to approve the card
		if ( ! current_user_can( 'approve_for_municipality' ) ) {
			wp_die( esc_html__( 'Sinulla ei ole oikeuksia aktivoida kortteja', 'topten' ) );
		}

		$post = get_post( $post_id );

		// Check if card exists
		if ( ! $post ) {
			wp_die( esc_html__( 'Korttia ei löytynyt', 'topten' ) );
		}

		// Get user municipality
		$user_municipality = get_field( 'user_municipality', 'user_' . get_current_user_id() );

		if ( ! $user_municipality ) {
			wp_die( esc_html__( 'Käyttäjällesi ei ole asetettu kuntaa. Ota yhteys ylläpitoon.', 'topten' ) );
		}

		$set_term = wp_remove_object_terms( $post_id, $user_municipality, 'kunta' );

		$redirect_url = wp_get_referer();

		if ( is_wp_error( $set_term ) ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'success' => 0,
					),
					$redirect_url
				)
			);
		} else {
			wp_safe_redirect(
				add_query_arg(
					array(
						'success' => 1,
					),
					$redirect_url
				)
			);
		}
	}
}
