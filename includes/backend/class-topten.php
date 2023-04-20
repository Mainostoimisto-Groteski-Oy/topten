<?php
defined( 'ABSPATH' ) || exit;

/**
 * Pluginin pääluokka
 */
class Topten {
	/**
	 * Logger
	 *
	 * @var \Topten_Logger
	 */
	protected $logger = null;

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
		add_action( 'init', array( $this, 'register_cpts' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		require_once 'classes/class-logger.php';
		require_once 'classes/class-rest.php';
		require_once 'classes/class-user-roles.php';
		require_once 'classes/class-admin-columns.php';
		require_once 'classes/class-lifecycle.php';

		// Luodaan lokiluokasta muuttuja
		$this->logger = new Topten_Logger();

		new Topten_REST();
		new Topten_User_Roles();
		new Topten_Admin_Columns();
		new Topten_Lifecycle();
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
	 * Tarkistaa onko postityyppi kortti
	 *
	 * @param string $post_type Postityyppi
	 */
	protected function is_card( $post_type ) {
		return in_array( $post_type, $this->card_types, true );
	}
}
