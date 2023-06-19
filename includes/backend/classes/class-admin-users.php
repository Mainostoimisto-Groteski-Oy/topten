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
		require_once 'class-admin-approval-municipality.php';
		require_once 'class-admin-approval-profession.php';

		// new Topten_Admin_Approval_Municipality();
		new Topten_Admin_Approval_Profession();

		// add_action( 'admin_menu', array( $this, 'hide_menu_items' ) );

		add_action( 'admin_bar_menu', array( $this, 'hide_admin_bar_items' ), 999 );
	}

	/**
	 * Hide admin bar items based on user role
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance
	 */
	public function hide_admin_bar_items( $wp_admin_bar ) {
		$user = wp_get_current_user();

		$roles = $user->roles;

		if ( in_array( 'ammattihyvaksyja', $roles, true ) ) {
			$wp_admin_bar->remove_node( 'edit' );
		}
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
}
