<?php
defined( 'ABSPATH' ) || exit;

/**
 * User roles
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
	 * Add custom user roles
	 */
	public function add_roles() {
		$administrator = get_role( 'administrator' );
		$administrator->add_cap( 'approve_for_municipality' );

		// $administrator->add_cap( 'edit_tulkintakorttis' );
		// $administrator->add_cap( 'read_tulkintakortti' );

		remove_role( 'kuntahyvaksyja' );

		add_role(
			'kuntahyvaksyja',
			__( 'Kuntahyväksyjä', 'topten' ),
			array(
				'read'                     => true,
				'edit'                     => true,
				'approve_for_municipality' => true,
			// 'edit_tulkintakorttis' => true,
			// 'read_tulkintakortti'  => true,
			)
		);
	}

	/**
	 * Hide menu items based on user role
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
	 * Add menu pages
	 */
	public function add_menu_pages() {
		// Page for municipality approval
		add_menu_page(
			'Hyväksyntä',
			'Hyväksyntä',
			'approve_for_municipality', // todo: Change to correct capability
			'hyvaksynta',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Tulkintakortit
		add_submenu_page(
			'hyvaksynta',
			'Tulkintakortit',
			'Tulkintakortit',
			'approve_for_municipality', // todo: Change to correct capability
			'hyvaksynta-tulkintakortit',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Ohjekortit
		add_submenu_page(
			'hyvaksynta',
			'Ohjekortit',
			'Ohjekortit',
			'approve_for_municipality', // todo: Change to correct capability
			'hyvaksynta-ohjekortti',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Lomakekortit
		add_submenu_page(
			'hyvaksynta',
			'Lomakekortit',
			'Lomakekortit',
			'approve_for_municipality', // todo: Change to correct capability
			'hyvaksynta-lomakekortti',
			array( $this, 'render_approval_page' ),
		);
	}

	/**
	 * Render approval page
	 */
	public function render_approval_page() {
		include_once 'pages/approval-page.php';
	}
}
