<?php
defined( 'ABSPATH' ) || exit;

/**
 * Ylläpitopuolen toiminnot
 */
class Topten_Admin {
	/**
	 * Kortit
	 *
	 * @var \Topten_Admin_Cards
	 */
	protected $cards = null;

	/**
	 * Käyttäjät
	 *
	 * @var \Topten_Admin_Users
	 */
	protected $users = null;

	/**
	 * Korttityypit
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
	 */
	public function __construct() {
		require_once 'classes/class-admin-users.php';
		require_once 'classes/class-admin-cards.php';

		$this->users = new Topten_Admin_Users();
		$this->cards = new Topten_Admin_Cards();

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_cpts' ) );
		add_action( 'init', array( $this, 'register_post_statuses' ) );

		add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
	}

	/**
	 * Lataa textdomainin
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'topten', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Rekisteröi CPT:t
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
				// 'capabilities' => array(
				// 'edit_posts'  => 'edit_tulkintakorttis',
				// 'delete_post' => 'read_tulkintakortti',
				// ),
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
	 * Rekisteröi omat post statukset
	 */
	public function register_post_statuses() {
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
	 * Lisää postin statuksen muokkausnäkymään
	 *
	 * @param string[] $states Postin statukset
	 * @param WP_Post  $post Posti
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
}
