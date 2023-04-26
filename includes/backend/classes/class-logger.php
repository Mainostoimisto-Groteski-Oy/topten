<?php
defined( 'ABSPATH' ) || exit;
/**
 * Topten logger
 *
 * MAYBE NOT ACTUALLY REQUIRED
 */
class Topten_Logger extends Topten {
	/**
	 * Taulun nimi
	 *
	 * @var string
	 */
	protected $card_table_name = 'topten_logs_cards';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->maybe_create_table();
	}

	/**
	 * Luo taulun lokeille jos sitä ei ole olemassa
	 */
	public function maybe_create_table() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . $this->card_table_name;

		// Taulussa on ID, aika, tyyppi ja viesti
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			message text NOT NULL,
			action varchar(20) NOT NULL,
			user_name varchar(20) NOT NULL,
			user_id mediumint(9) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		maybe_create_table( $table_name, $sql );
	}

	/**
	 * Kirjaa lokin tietokantaan
	 *
	 * @param string $message Viesti
	 * @param string $action Toiminto
	 * @param string $user_name Käyttäjän nimi
	 * @param int    $user_id Käyttäjän ID
	 */
	public function insert( $message, $action, $user_name, $user_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->card_table_name;

		$wpdb->insert(
			$table_name,
			array(
				'time'      => current_time( 'mysql' ),
				'message'   => $message,
				'action'    => $action,
				'user_name' => $user_name,
				'user_id'   => $user_id,
			)
		);
	}
}
