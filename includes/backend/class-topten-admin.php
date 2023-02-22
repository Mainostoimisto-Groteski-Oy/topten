<?php
defined( 'ABSPATH' ) || exit;

/**
 * Topten admin class, used in backend
 * Loads textdomain and includes other classes
 *
 * @since 1.0.0
 *
 * @package Topten\Admin
 */
class Topten_Admin {
	/**
	 * Cards class instance
	 *
	 * @since 1.0.0
	 *
	 * @var \Topten_Admin_Cards
	 */
	protected $cards = null;

	/**
	 * Topten_Admin_Users class instance
	 *
	 * @since 1.0.0
	 *
	 * @var \Topten_Admin_Users
	 */
	protected $users = null;

	/**
	 * Card post types
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $card_types = array(
		'tulkintakortti',
		'ohjekortti',
		'lomakekortti',
	);

	/**
	 * Class constructor
	 * Includes other classes and inits hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once 'classes/class-admin-users.php';
		require_once 'classes/class-admin-cards.php';

		$this->users = new Topten_Admin_Users();
		$this->cards = new Topten_Admin_Cards();

		$this->init_hooks();
	}

	/**
	 * Init hooks
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_cpts' ) );
		add_action( 'init', array( $this, 'register_post_statuses' ) );

		add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );

		// Edit page class
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ), 9999, 1 );
	}

	/**
	 * Load textdomain
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'topten', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register custom post types for cards
	 *
	 * @since 1.0.0
	 */
	public function register_cpts() {
		/**
		 * Tulkintakortti
		 */
		register_post_type(
			'tulkintakortti',
			array(
				'label'        => esc_html__( 'Tulkintakortit', 'topten' ),
				'labels'       => array(
					'name'          => esc_html__( 'Tulkintakortit', 'topten' ),
					'singular_name' => esc_html__( 'Tulkintakortti', 'topten' ),
				),
				'public'       => true,
				'map_meta_cap' => true,
				'menu_icon'    => 'dashicons-media-document',
				'rewrite'      => array(
					'slug'       => 'tulkintakortti',
					'with_front' => false,
				),
				'show_in_rest' => true,
				'supports'     => array(
					'title',
					'editor',
					'thumbnail',
					'revisions',
					'author',
				),
				'capabilities' => array(
					'edit_others_posts' => 'edit_tulkintakorttis',
				// 'delete_post' => 'read_tulkintakortti',
				),
			)
		);

		/**
		 * Ohjekortti
		 */
		register_post_type(
			'ohjekortti',
			array(
				'label'        => esc_html__( 'Ohjekortit', 'topten' ),
				'labels'       => array(
					'name'          => esc_html__( 'Ohjekortit', 'topten' ),
					'singular_name' => esc_html__( 'Ohjekortti', 'topten' ),
				),
				'public'       => true,
				'map_meta_cap' => true,
				'menu_icon'    => 'dashicons-media-document',
				'rewrite'      => array(
					'slug'       => 'ohjekortti',
					'with_front' => false,
				),
				'show_in_rest' => true,
				'supports'     => array(
					'title',
					'editor',
					'thumbnail',
					'revisions',
					'author',
				),
			)
		);

		/**
		 * Lomakekortti
		 */
		register_post_type(
			'lomakekortti',
			array(
				'label'        => esc_html__( 'Lomakekortit', 'topten' ),
				'labels'       => array(
					'name'          => esc_html__( 'Lomakekortit', 'topten' ),
					'singular_name' => esc_html__( 'Lomakekortti', 'topten' ),
				),
				'public'       => true,
				'menu_icon'    => 'dashicons-media-document',
				'map_meta_cap' => true,
				'rewrite'      => array(
					'slug'       => 'lomakekortti',
					'with_front' => false,
				),
				'show_in_rest' => true,
				'supports'     => array(
					'title',
					'editor',
					'thumbnail',
					'revisions',
					'author',
				),
			)
		);
	}

	/**
	 * Register custom post statuses (e.g. deleted)
	 *
	 * @since 1.0.0
	 */
	public function register_post_statuses() {
		// Status: deleted
		// Used for soft deleting posts, doesn't actually delete anything
		// Basically just a different name for draft
		register_post_status(
			'deleted',
			array(
				'label'                     => esc_html__( 'Poistettu', 'topten' ),
				'public'                    => false,
				'private'                   => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				// translators: %s: post count.
				'label_count'               => _n_noop( 'Poistettu <span class="count">(%s)</span>', 'Poistettu <span class="count">(%s)</span>', 'topten' ),
			)
		);
	}

	/**
	 * Add post status to WP admin post list
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $states Existing post states
	 * @param WP_Post  $post Post object
	 */
	public function display_post_states( $states, $post ) {
		if ( ! $this->cards->is_card( $post->post_type ) ) {
			return $states;
		}

		if ( 'deleted' === $post->post_status ) {
			$states['deleted'] = esc_html__( 'Poistettu', 'topten' );
		} elseif ( 'publish' === $post->post_status ) {
			$states['publish'] = esc_html__( 'Julkaistu', 'topten' );
		}

		return $states;
	}

	/**
	 * Get card statuses as an array of primary and secondary status
	 * Contains value for each status
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Card ID
	 */
	public function get_card_statuses( $post_id ) {
		// Set default for card statuses, if status is not set, assume that card is a draft
		$card_statuses = array(
			'primary'   => 'draft',
			'secondary' => 'draft',
		);

		$card_status = get_field( 'card_status', $post_id );

		if ( $card_status ) {
			$card_status_secondary = get_field( 'card_status_' . $card_status['value'], $post_id );

			$card_statuses['primary']   = $card_status['value'];
			$card_statuses['secondary'] = $card_status_secondary['value'];
		}

		return $card_statuses;
	}

	/**
	 * Get card statuses as an array of primary and secondary status
	 * Contains label and value for each status
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Card ID
	 */
	public function get_card_statuses_array( $post_id ) {
		// Set default for card statuses, if status is not set, assume that card is a draft
		$card_statuses = array(
			'primary'   => array(
				'label' => 'Luonnos',
				'value' => 'draft',
			),
			'secondary' => array(
				'label' => 'Luonnos',
				'value' => 'draft',
			),
		);

		$card_status = get_field( 'card_status', $post_id );

		if ( $card_status ) {
			$card_status_secondary = get_field( 'card_status_' . $card_status['value'], $post_id );

			$card_statuses['primary']   = $card_status;
			$card_statuses['secondary'] = $card_status_secondary;
		}

		return $card_statuses;
	}

	/**
	 * Add class to card editing page based on post status
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Existing classes
	 */
	public function admin_body_class( $classes ) {
		global $pagenow;

		if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) {
			$post_type = get_post_type( get_the_ID() );

			if ( in_array( $post_type, $this->card_types, true ) ) {
				if ( $this->cards->is_pending_approval( get_the_ID() ) ) {
					$classes .= ' tt-editor-disabled';
				}
			}
		}

		return $classes;
	}
}
