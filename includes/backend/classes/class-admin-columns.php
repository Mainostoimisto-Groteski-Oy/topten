<?php

/**
 * Topten admin columns
 */
class Topten_Admin_Columns extends Topten {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Lisää actionit ja filtterit
	 */
	private function add_actions() {
		// Kopioi kortti
		add_filter( 'post_row_actions', array( $this, 'copy_card_action' ), 10, 2 );
		add_filter( 'admin_action_topten_copy_card', array( $this, 'copy_card' ) );

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
	 * Muokkaa sarakkeiden järjestystä
	 *
	 * @param string[] $columns Sarakkeet
	 */
	public function reorder_columns( $columns ) {
		$ordered_columns = array();

		foreach ( $columns as $index => $value ) {
			// Siirretään omat sarakkeet ennen kirjoittajaa
			if ( 'author' === $index ) {
				$ordered_columns['version']         = 'version';
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
		$columns['version']         = esc_html__( 'Versio', 'topten' );
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
		if ( 'version' === $column ) {
			$version = get_field( 'version', $post_id ) ?: 'A';

			echo esc_html( $version );
		}

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

	/**
	 * Kopioi -nappi korttien listaukseen
	 *
	 * @param string[] $actions Olemassa olevat toiminnot
	 * @param WP_Post  $post    Post
	 */
	public function copy_card_action( $actions, $post ) {
		$type = get_post_type( $post );

		if ( ! $this->is_card( $type ) ) {
			return $actions;
		}

		$url = admin_url( 'admin.php?action=topten_copy_card&post=' . $post->ID );
		$url = wp_nonce_url( $url, 'topten_copy_card_' . $post->ID );


		$actions[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Kopioi', 'topten' ) . '</a>';

		return $actions;
	}

	/**
	 * Kopioi kortin uudeksi luonnokseksi
	 */
	public function copy_card() {
		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'topten_copy_card' !== $_GET['action'] ) ) {
			return;
		}

		$post_id = intval( $_GET['post'] );

		check_admin_referer( 'topten_copy_card_' . $post_id );

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

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

		// Kopioi kentät
		$fields = get_fields( $post_id );

		if ( $fields ) {
			foreach ( $fields as $key => $value ) {
				update_field( $key, $value, $new_post_id );
			}
		}

		// Nosta versionumerota
		$version = get_field( 'version', $new_post_id );

		if ( $version ) {
			// Jos versio on asetettu, nostetaan se yhdellä
			$version = ++$version;
		} else {
			// Jos versiota ei ole asetettu, asetetaan se A:ksi
			$version = 'A';
		}

		update_field( 'version', $version, $new_post_id );

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
}
