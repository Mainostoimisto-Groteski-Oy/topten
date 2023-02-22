<?php
/**
 * Topten Admin Cards class
 * Handles card related actions and filters
 *
 * @since 1.0.0
 *
 * @package Topten\Admin\Cards
 */
class Topten_Admin_Cards extends Topten_Admin {
	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Card primary status, default 'draft'
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $primary_status = 'draft';

	/**
	 * Card secondary status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $secondary_status = '';

	/**
	 * Card old primary status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $old_primary_status = '';

	/**
	 * Card old secondary status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $old_secondary_status = '';

	/**
	 * Card old version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $old_version = '';

	/**
	 * Class constructor
	 * Requires the Lifecycle class and inits hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->add_hooks();

		require_once 'class-admin-cards-lifecycle.php';

		new Topten_Admins_Cards_Lifecycle();
	}

	/**
	 * Init class hooks
	 *
	 * @since 1.0.0
	 */
	private function add_hooks() {
		// Add link to row actions
		add_filter( 'post_row_actions', array( $this, 'copy_card_action' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'copy_card_language_action' ), 10, 2 );

		// Copy card action
		add_action( 'admin_action_tt_copy_card', array( $this, 'copy_card' ) );
		add_action( 'admin_action_tt_copy_card_language', array( $this, 'copy_card_language' ) );

		// Approve card
		add_action( 'wp_ajax_tt_approve_card', array( $this, 'approve_card' ) );

		// Disapprove card
		add_action( 'wp_ajax_tt_disapprove_card', array( $this, 'disapprove_card' ) );

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
	 * @since 1.0.0
	 *
	 * @param string[] $columns Columns
	 */
	public function add_custom_columns( $columns ) {
		$columns['version']         = esc_html__( 'Versio', 'topten' );
		$columns['versions']        = esc_html__( 'Versiot', 'topten' );
		$columns['language']        = esc_html__( 'Kieli', 'topten' );
		$columns['other_languages'] = esc_html__( 'Kieliversiot', 'topten' );
		$columns['status']          = esc_html__( 'Tila', 'topten' );
		$columns['modified']        = esc_html__( 'Muokattu', 'topten' );
		$columns['modified_author'] = esc_html__( 'Muokkaaja', 'topten' );
		$columns['published']       = esc_html__( 'Julkaistu', 'topten' );

		return $columns;
	}

	/**
	 * Add data to custom columns
	 *
	 * @since 1.0.0
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

		// Versions column, show links to old versions
		if ( 'versions' === $column ) {
			$versions = get_field( 'versions', $post_id );

			if ( ! $versions ) {
				return;
			}

			$versions       = array_unique( $versions );
			$versions       = array_values( $versions );
			$versions_array = array();

			foreach ( $versions as $index => $version ) {
				// Check if version actually exists
				if ( ! get_post( $version ) ) {
					continue;
				}

				$edit_link = get_edit_post_link( $version );

				$version_number = get_field( 'version', $version ) ?: '(?)';

				$output = sprintf( '<a class="version-number" href="%s">%s</a>', esc_url( $edit_link ), esc_html( $version_number ) );

				$versions_array[ $version_number ] = $output;
			}

			// Sort versions array
			ksort( $versions_array );

			// Output versions
			foreach ( $versions_array as $version ) {
				echo $version; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
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
	 * @since 1.0.0
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
				$ordered_columns['version']         = 'version';
				$ordered_columns['versions']        = 'versions';
				$ordered_columns['language']        = 'language';
				$ordered_columns['other_languages'] = 'other_languages';
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type to check (slug)
	 */
	public function is_card( $post_type ) {
		return in_array( $post_type, $this->card_types, true );
	}

	/**
	 * Copy button to card actions
	 *
	 * @since 1.0.0
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
			admin_url( 'admin.php' ),
		);

		$url = wp_nonce_url( $url, 'tt_copy_card_' . $post->ID );

		$actions[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Kopioi', 'topten' ) . '</a>';

		return $actions;
	}

	/**
	 * Copy as language version to card actions
	 *
	 * @since 1.0.0
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
			admin_url( 'admin.php' ),
		);

		$url = wp_nonce_url( $url, 'tt_copy_card_language_' . $post->ID );

		$actions[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Kopioi kieliversioksi', 'topten' ) . '</a>';

		return $actions;
	}

	/**
	 * Copy card as a new draft
	 *
	 * @since 1.0.0
	 */
	public function copy_card() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_die( 'User not logged in', 'User not logged in', array( 'response' => 401 ) );
		}

		// Check if we have a post ID and action tt_copy_card
		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'tt_copy_card' !== $_GET['action'] ) ) {
			wp_die( 'Action missing', 'Action missing', array( 'response' => 401 ) );
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
			wp_die( 'Unauthorized', 'Unauthorized', array( 'response' => 401 ) );
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

		// Copy taxonomies from source post
		$taxonomies = get_object_taxonomies( $post );

		// Update taxonomies
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );

				wp_set_object_terms( $new_post_id, $terms, $taxonomy, false );
			}
		}

		// Copy fields from source post
		$fields = get_fields( $post_id );

		if ( $fields ) {
			foreach ( $fields as $key => $value ) {
				update_field( $key, $value, $new_post_id );
			}
		}

		// Source post version
		$version = get_field( 'version', $new_post_id );

		// Set version to post meta as old_version
		update_post_meta( $new_post_id, 'old_version', $version );

		// If version is set, increment it by one, else set it to A
		if ( $version ) {
			if ( '-' === $version ) {
				$version = 'A';
			} else {
				$version = ++$version;
			}
		} else {
			$version = 'A';
		}

		update_field( 'version', $version, $new_post_id );

		// Source versions
		$versions = get_field( 'versions', $post_id );

		if ( $versions ) {
			$versions[] = $post_id;
			$versions[] = $new_post_id;
		} else {
			$versions = array( $new_post_id, $post_id );
		}

		$versions = array_unique( $versions );

		// Update all versions
		foreach ( $versions as $version_id ) {
			update_field( 'versions', $versions, $version_id );
		}

		// Set primary status to draft
		update_field( 'card_status', 'draft', $new_post_id );

		// Set draft status to draft
		update_field( 'card_status_draft', 'draft', $new_post_id );

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
	 *
	 * @since 1.0.0
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

		// Copy taxonomies from source post
		$taxonomies = get_object_taxonomies( $post );

		// Update taxonomies
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );

				wp_set_object_terms( $new_post_id, $terms, $taxonomy, false );
			}
		}

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
	 * Handle card approval
	 *
	 * @since 1.0.0
	 */
	public function approve_card() {
		// Check nonce
		check_ajax_referer( 'nonce', 'nonce' );

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Sinun täytyy kirjautua sisään hyväksyäksesi kortteja', 'topten' ),
					'error_code' => '',
				)
			);
		}

		// Check if user has permission to approve the card
		if ( ! current_user_can( 'approve_cards' ) ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Sinulla ei ole oikeuksia hyväksyä kortteja', 'topten' ),
					'error_code' => '',
				)
			);
		}

		// Check if we have a post ID
		if ( ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Jotain meni vikaan. Yritä kohta uudestaan.', 'topten' ),
					/* translators: %s = Error message */
					'error_code' => sprintf( __( 'Parametri puuttuu: %s', 'topten' ), 'approved' ),
				)
			);
		}

		$post_id = intval( $_POST['post_id'] );

		$post = get_post( $post_id );

		// Check if card exists
		if ( ! $post ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Korttia ei löytynyt', 'topten' ),
					'error_code' => '',
				)
			);
		}

		// Approvers message
		$message = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';

		// Notify committer of approval about the approval (via email)
		$this->notify_committer( true, $post_id, $message );

		// Set card status to approved
		update_field( 'card_status', 'draft', $post_id );
		update_field( 'card_status_draft', 'approved', $post_id );

		wp_send_json_success(
			array(
				'message'    => esc_html__( 'Kortti hyväksytty', 'topten' ),
				'error_code' => '',
			)
		);
	}

	/**
	 * Handle card disapproval
	 *
	 * @since 1.0.0
	 */
	public function disapprove_card() {
		// Check nonce
		check_ajax_referer( 'nonce', 'nonce' );

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Sinun täytyy kirjautua sisään hyväksyäksesi kortteja', 'topten' ),
					'error_code' => '',
				)
			);
		}

		// Check if user has permission to approve the card
		if ( ! current_user_can( 'approve_cards' ) ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Sinulla ei ole oikeuksia hyväksyä kortteja', 'topten' ),
					'error_code' => '',
				)
			);
		}

		// Check if we have a post ID
		if ( ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Jotain meni vikaan. Yritä kohta uudestaan.', 'topten' ),
					/* translators: %s = Error message */
					'error_code' => sprintf( __( 'Parametri puuttuu: %s', 'topten' ), 'approved' ),
				)
			);
		}

		$post_id = intval( $_POST['post_id'] );

		$post = get_post( $post_id );

		// Check if card exists
		if ( ! $post ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Korttia ei löytynyt', 'topten' ),
					'error_code' => '',
				)
			);
		}

		// Approvers message
		$message = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';

		// Notify committer of approval about the approval (via email)
		$this->notify_committer( false, $post_id, $message );

		// Set card status back to draft
		update_field( 'card_status', 'draft', $post_id );
		update_field( 'card_status_draft', 'draft', $post_id );

		wp_send_json_success(
			array(
				'message'    => esc_html__( 'Kortti hylätty', 'topten' ),
				'error_code' => '',
			)
		);
	}

	/**
	 * Notify committer of approval about the approval (via email)
	 *
	 * @todo Error handling if committer is not set
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $approved          Was the card approved or not
	 * @param int    $post_id          Post ID
	 * @param string $approver_message Approver message
	 */
	protected function notify_committer( $approved, $post_id, $approver_message ) {
		$committer = get_post_meta( $post_id, 'committer', true );
		$committer = get_user_by( 'id', $committer );

		if ( ! $committer ) {
			return;
		}

		$email = $committer->user_email;

		if ( $approved ) {
			$subject = esc_html__( 'Korttisi on hyväksytty', 'topten' );

			$message = '<p>';

			/* translators: %s: Card title */
			$message .= sprintf( __( 'Korttisi "%s" on hyväksytty.', 'topten' ), get_the_title( $post_id ) );
			$message .= '</p>';

			if ( $approver_message ) {
				$message .= '<p>' . __( 'Hyväksyjän viesti:', 'topten' ) . '</p>';
				$message .= '<p>' . $approver_message . '</p>';
			}
		} else {
			$subject = esc_html__( 'Korttisi on hylätty', 'topten' );

			$message = '<p>';

			/* translators: %s: Card title */
			$message .= sprintf( __( 'Korttisi "%s" on hylätty.', 'topten' ), get_the_title( $post_id ) );
			$message .= '</p>';

			if ( $approver_message ) {
				$message .= '<p>' . __( 'Hylkääjän viesti:', 'topten' ) . '</p>';
				$message .= '<p>' . $approver_message . '</p>';
			}
		}

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);

		wp_mail( $email, $subject, $message, $headers );
	}

	/**
	 * Is card pending approval?
	 *
	 * @since 1.0.0
	 *
	 * @param null|int $post_id Card ID, defaults to current post.
	 */
	public function is_pending_approval( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$card_statuses = $this->get_card_statuses( $post_id );

		if ( 'draft' === $card_statuses['primary'] && 'pending_approval' === $card_statuses['secondary'] ) {
			return true;
		}

		return false;
	}
}
