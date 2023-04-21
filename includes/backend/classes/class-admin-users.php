<?php
defined( 'ABSPATH' ) || exit;

/**
 * Käyttäjäroolit
 */
class Topten_Admin_Users extends Topten_Admin {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_roles' ) );
		add_action( 'admin_menu', array( $this, 'hide_menu_items' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
	}

	/**
	 * Lisää käyttäjäroolit
	 */
	public function add_roles() {
		$administrator = get_role( 'administrator' );
		$administrator->add_cap( 'activate_for_municipality' );

		// $administrator->add_cap( 'edit_tulkintakorttis' );
		// $administrator->add_cap( 'read_tulkintakortti' );

		remove_role( 'kuntahyvaksyja' );

		add_role(
			'kuntahyvaksyja',
			__( 'Kuntahyväksyjä', 'topten' ),
			array(
				'read'                      => true,
				'edit'                      => true,
				'activate_for_municipality' => true,
			// 'edit_tulkintakorttis' => true,
			// 'read_tulkintakortti'  => true,
			)
		);
	}

	/**
	 * Piilotetaan valikot käyttäjäroolin mukaan
	 */
	public function hide_menu_items() {
		$user = wp_get_current_user();

		// Nämä valikot näytetään vain ylläpitäjille
		// if ( ! current_user_can( 'administrator' ) ) {
		// remove_menu_page( 'edit-comments.php' ); // Kommentit
		// remove_menu_page( 'tools.php' ); // Työkalut
		// remove_menu_page( 'edit.php' ); // Artikkelit
		// }

		// if ( ! user_can( get_current_user(), 'edit_tulkintakortti' ) ) {
		// remove_menu_page( 'edit.php?post_type=tulkintakortti' );
		// }

		// if ( ! current_user_can( 'edit_ohjekortti' ) ) {
		// remove_menu_page( 'edit.php?post_type=ohjekortti' );
		// }

		// if ( ! current_user_can( 'edit_tulkintakorttis' ) ) {
		// remove_menu_page( 'edit.php?post_type=tulkintakortti' );
		// }

		// if ( ! current_user_can( 'edit_lomakekortti' ) ) {
		// remove_menu_page( 'edit.php?post_type=lomakekortti' );
		// }
	}

	/**
	 * Lisää valikkosivut
	 */
	public function add_menu_pages() {
		// Hyväksyntäsivu
		add_menu_page(
			'Hyväksyntä',
			'Hyväksyntä',
			'activate_for_municipality', // todo: Vaihda oikea oikeus
			'hyvaksynta',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Tulkintakortit
		add_submenu_page(
			'hyvaksynta',
			'Tulkintakortit',
			'Tulkintakortit',
			'activate_for_municipality', // todo: Vaihda oikea oikeus
			'hyvaksynta-tulkintakortit',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Ohjekortit
		add_submenu_page(
			'hyvaksynta',
			'Ohjekortit',
			'Ohjekortit',
			'activate_for_municipality', // todo: Vaihda oikea oikeus
			'hyvaksynta-ohjekortti',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Lomakekortit
		add_submenu_page(
			'hyvaksynta',
			'Lomakekortit',
			'Lomakekortit',
			'activate_for_municipality', // todo: Vaihda oikea oikeus
			'hyvaksynta-lomakekortti',
			array( $this, 'render_approval_page' ),
		);
	}

	/**
	 * Renderöi sivun
	 */
	public function render_approval_page() {
		include_once 'pages/approval-page.php';
	}
}
