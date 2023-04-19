<?php
defined( 'ABSPATH' ) || exit;

/**
 * Käyttäjäroolit
 */
class Topten_User_Roles extends Topten {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_roles' ) );
		add_action( 'admin_menu', array( $this, 'hide_menu_items' ) );
	}

	/**
	 * Lisää käyttäjäroolit
	 */
	public function add_roles() {
		$administrator = get_role( 'administrator' );

		$administrator->add_cap( 'edit_tulkintakorttis' );
		// $administrator->add_cap( 'read_tulkintakortti' );

		// remove_role( 'taso-1' );

		add_role(
			'taso-1',
			__( 'Taso 1', 'topten' ),
			array(
				'read'                 => true,
				'edit'                 => true,
				'edit_tulkintakorttis' => true,
				'read_tulkintakortti'  => true,
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

		if ( ! current_user_can( 'edit_tulkintakorttis' ) ) {
			remove_menu_page( 'edit.php?post_type=tulkintakortti' );
		}

		// if ( ! current_user_can( 'edit_lomakekortti' ) ) {
		// remove_menu_page( 'edit.php?post_type=lomakekortti' );
		// }
	}
}
