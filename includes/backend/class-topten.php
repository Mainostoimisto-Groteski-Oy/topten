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

		require_once 'class-user-roles.php';
		require_once 'class-lifecycle.php';

		new Topten_User_Roles();
		new Topten_Lifecycle();
	}

	/**
	 * Lisää actionit ja filtterit
	 */
	public function add_actions() {
		// Init
		add_action( 'init', array( $this, 'register_cpts' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// reorder_columns filtterit
		add_filter( 'manage_tulkintakortti_posts_columns', array( $this, 'reorder_columns' ), 20, 1 );
		add_filter( 'manage_ohjekortti_posts_columns', array( $this, 'reorder_columns' ), 20, 1 );
		add_filter( 'manage_lomakekortti_posts_columns', array( $this, 'reorder_columns' ), 20, 1 );

		// set_sortable_columns filtterit
		add_filter( 'manage_edit-tulkintakortti_sortable_columns', array( $this, 'set_sortable_columns' ) );
		add_filter( 'manage_edit-ohjekortti_sortable_columns', array( $this, 'set_sortable_columns' ) );
		add_filter( 'manage_edit-lomakekortti_sortable_columns', array( $this, 'set_sortable_columns' ) );

		// add_custom_columns filtterit
		add_filter( 'manage_tulkintakortti_posts_columns', array( $this, 'add_custom_columns' ) );
		add_filter( 'manage_ohjekortti_posts_columns', array( $this, 'add_custom_columns' ) );
		add_filter( 'manage_lomakekortti_posts_columns', array( $this, 'add_custom_columns' ) );

		// add_custom_column_data actionit
		add_action( 'manage_tulkintakortti_posts_custom_column', array( $this, 'add_custom_column_data' ), 10, 2 );
		add_action( 'manage_ohjekortti_posts_custom_column', array( $this, 'add_custom_column_data' ), 10, 2 );
		add_action( 'manage_lomakekortti_posts_custom_column', array( $this, 'add_custom_column_data' ), 10, 2 );
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
	 * Muokkaa sarakkeiden järjestystä
	 *
	 * @param string[] $columns Sarakkeet
	 */
	public function reorder_columns( $columns ) {
		$ordered_columns = array();

		foreach ( $columns as $index => $value ) {
			// Siirretään omat sarakkeet ennen kirjoittajaa
			if ( 'author' === $index ) {
				$ordered_columns['status']          = 'status';
				$ordered_columns['modified_author'] = 'modified_author';
				$ordered_columns['modified']        = 'modified';
			}

			$ordered_columns[ $index ] = $value;
		}

		return $ordered_columns;
	}

	/**
	 * Asettaa järjestettävät sarakkeet
	 *
	 * @param string[] $columns Sarakkeet
	 */
	public function set_sortable_columns( $columns ) {
		$columns['modified'] = 'modified';

		return $columns;
	}

	/**
	 * Lisää omat sarakkeet
	 *
	 * @param string[] $columns Sarakkeet
	 */
	public function add_custom_columns( $columns ) {
		$columns['status']          = esc_html__( 'Tila', 'topten' );
		$columns['modified_author'] = esc_html__( 'Muokkaaja', 'topten' );
		$columns['modified']        = esc_html__( 'Muokattu', 'topten' );

		return $columns;
	}

	/**
	 * Lisää data omiin sarakkeisiin
	 *
	 * @param string $column Sarakkeen nimi
	 * @param int    $post_id Postin ID
	 */
	public function add_custom_column_data( $column, $post_id ) {
		if ( 'status' === $column ) {
			echo esc_html( topten_get_post_status( $post_id ) );
		}

		if ( 'modified_author' === $column ) {
			$last_id = get_post_meta( get_post()->ID, '_edit_last', true );

			if ( $last_id ) {
				$last_user = get_user_by( 'id', $last_id );

				if ( $last_user ) {
					echo esc_html( $last_user->display_name );
				}
			}
		}

		if ( 'modified' === $column ) {
			echo esc_html( get_the_modified_date( 'j.n.Y H:i', $post_id ) );
		}
	}
}
