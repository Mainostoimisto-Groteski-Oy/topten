<?php
defined( 'ABSPATH' ) || exit;

/**
 * Pluginin pääluokka
 */
class Topten {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Lisää actionit ja filtterit
	 */
	public function add_actions() {
		// Init
		add_action( 'init', array( $this, 'register_cpts' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// add_post_status_column filtterit
		add_filter( 'manage_tulkintakortti_posts_columns', array( $this, 'add_post_status_column' ) );
		add_filter( 'manage_ohjekortti_posts_columns', array( $this, 'add_post_status_column' ) );
		add_filter( 'manage_lomakekortti_posts_columns', array( $this, 'add_post_status_column' ) );

		// add_post_status_to_column actionit
		add_action( 'manage_tulkintakortti_posts_custom_column', array( $this, 'add_post_status_to_column' ), 10, 2 );
		add_action( 'manage_ohjekortti_posts_custom_column', array( $this, 'add_post_status_to_column' ), 10, 2 );
		add_action( 'manage_lomakekortti_posts_custom_column', array( $this, 'add_post_status_to_column' ), 10, 2 );
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
	 * Lisää tilakolumnin korttilistaukseen
	 *
	 * @param string[] $columns Kolumnit
	 */
	public function add_post_status_column( $columns ) {
		$columns['status'] = esc_html__( 'Tila', 'topten' );

		// Siirretään status-kolumni kirjoittajaa ennen
		$ordered_columns = array();

		foreach ( $columns as $index => $value ) {
			if ( 'author' === $index ) {
				$ordered_columns['status'] = 'status';
			}

			$ordered_columns[ $index ] = $value;
		}

		return $ordered_columns;
	}

	/**
	 * Lisää kortin tilan kolumniin
	 *
	 * @param string $column Kolumnin nimi
	 * @param int    $post_id Postin ID
	 */
	public function add_post_status_to_column( $column, $post_id ) {
		if ( 'status' === $column ) {
			echo esc_html( topten_get_post_status( $post_id ) );
		}
	}
}
