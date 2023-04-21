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

		// Korttien elinkaari
		new Topten_Admins_Cards_Lifecycle();
	}

	/**
	 * Lisää actionit ja filtterit
	 */
	private function add_actions() {
		// Kopioi kortti linkit
		add_filter( 'post_row_actions', array( $this, 'copy_card_action' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'copy_card_language_action' ), 10, 2 );

		// Kopioi kortti
		add_filter( 'admin_action_topten_copy_card', array( $this, 'copy_card' ) );
		add_filter( 'admin_action_topten_copy_card_language', array( $this, 'copy_card_language' ) );

		// Ota kortti käyttöön kunnassa
		add_filter( 'admin_action_topten_activate_card', array( $this, 'activate_card' ) );

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

		add_action( 'pre_get_posts', array( $this, 'sort_cards_by_status' ) );
	}

	/**
	 * Muokkaa sarakkeiden järjestystä
	 *
	 * @param string[] $columns Sarakkeet
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
		);

		$ordered_columns = array();

		foreach ( $columns as $index => $value ) {
			if ( in_array( $index, $hidden_columns, true ) ) {
				continue;
			}

			// Siirretään omat sarakkeet ennen kirjoittajaa
			if ( 'author' === $index ) {
				$ordered_columns['language']        = 'language';
				$ordered_columns['other_languages'] = 'other_languages';
				$ordered_columns['version']         = 'version';
				$ordered_columns['status']          = 'status';
				$ordered_columns['modified']        = 'modified';
				$ordered_columns['modified_author'] = 'modified_author';
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
		$columns['status']   = 'status';
		$columns['modified'] = 'modified';

		return $columns;
	}

	/**
	 * Statuskolumnin järjestys
	 *
	 * @param WP_Query $query Kysely
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
	 * Lisää omat sarakkeet
	 *
	 * @param string[] $columns Sarakkeet
	 */
	public function add_custom_columns( $columns ) {
		$columns['language']        = esc_html__( 'Kieli', 'topten' );
		$columns['other_languages'] = esc_html__( 'Kieliversiot', 'topten' );
		$columns['version']         = esc_html__( 'Versio', 'topten' );
		$columns['status']          = esc_html__( 'Tila', 'topten' );
		$columns['modified']        = esc_html__( 'Muokattu', 'topten' );
		$columns['modified_author'] = esc_html__( 'Muokkaaja', 'topten' );

		return $columns;
	}

	/**
	 * Lisää data omiin sarakkeisiin
	 *
	 * @param string $column Sarakkeen nimi
	 * @param int    $post_id Postin ID
	 */
	public function add_custom_column_data( $column, $post_id ) {
		if ( 'language' === $column ) {
			$language = topten_get_card_language( $post_id );

			echo esc_html( $language );
		}

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

		if ( 'version' === $column ) {
			$version = get_field( 'version', $post_id ) ?: 'A';

			echo esc_html( $version );
		}

		if ( 'status' === $column ) {
			echo esc_html( topten_get_post_secondary_status( $post_id ) );
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
	 * Tarkistaa onko postityyppi kortti
	 *
	 * @param string $post_type Postityyppi
	 */
	public function is_card( $post_type ) {
		return in_array( $post_type, $this->card_types, true );
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
	 * Kopioi kieliversioksi -nappi korttien listaukseen
	 *
	 * @param string[] $actions Olemassa olevat toiminnot
	 * @param WP_Post  $post    Post
	 */
	public function copy_card_language_action( $actions, $post ) {
		$type = get_post_type( $post );

		if ( ! $this->is_card( $type ) ) {
			return $actions;
		}

		$url = add_query_arg(
			array(
				'action' => 'topten_copy_card_language',
				'post'   => $post->ID,
			),
			admin_url( 'admin-post.php' ),
		);

		$url = wp_nonce_url( $url, 'topten_copy_card_language_' . $post->ID );

		$actions[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Kopioi kieliversioksi', 'topten' ) . '</a>';

		return $actions;
	}

	/**
	 * Kopioi kortin uudeksi luonnokseksi
	 */
	public function copy_card() {
		if ( ! is_user_logged_in() ) {
			return;
		}

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

		// Ohjataan uuteen luonnokseen
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
	 * Kopioi kortin uudeksi kieliverisoksi
	 */
	public function copy_card_language() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'topten_copy_card_language' !== $_GET['action'] ) ) {
			return;
		}

		$post_id = intval( $_GET['post'] );

		check_admin_referer( 'topten_copy_card_language_' . $post_id );

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


		// Lähdeversion kieli
		$source_language = get_field( 'language', $post_id );

		json_log( $source_language );

		$source_language = $source_language['value'] ?? 'fi';

		// Vaihdetaan kieli
		if ( 'fi' === $source_language ) {
			$new_language = 'sv';
		} else {
			$new_language = 'fi';
		}

		update_field( 'language', $new_language, $new_post_id );

		// Asetetaan lähdeversio kieliversioksi
		update_field( 'version_' . $source_language, $post_id, $new_post_id );

		// Asetetaan kieliversio lähdeversion kieliversioksi
		update_field( 'version_' . $new_language, $new_post_id, $post_id );

		// Ohjataan uuteen luonnokseen
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
	 *
	 */
	public function activate_card() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! isset( $_GET['post'] ) || ( ! isset( $_GET['action'] ) || 'topten_activate_card' !== $_GET['action'] ) ) {
			wp_die( esc_html__( 'Jotain meni vikaan. Yritä uudestaan', 'topten' ) );
		}

		$post_id = intval( $_GET['post'] );

		// Tarkistetaan nonce
		check_admin_referer( 'topten_activate_card_' . $post_id );

		// Tarkistetaan käyttäjän oikeudet
		if ( ! current_user_can( 'activate_for_municipality' ) ) {
			wp_die( esc_html__( 'Sinulla ei ole oikeuksia aktivoida kortteja', 'topten' ) );
		}

		$post = get_post( $post_id );

		// Tarkistetaan onko kortti olemassa
		if ( ! $post ) {
			wp_die( esc_html__( 'Korttia ei löytynyt', 'topten' ) );
		}

		// Haetaan hyväksyjän kunta ja asetetaan se kortille
		$user_municipality = get_field( 'user_municipality', 'user_' . get_current_user_id() );
		$set_term          = wp_set_object_terms( $post_id, $user_municipality, 'kunta', true );

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