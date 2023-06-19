<?php
defined( 'ABSPATH' ) || exit;

/**
 * User roles
 */
class Topten_Admin_Approval_Profession extends Topten_Admin_Users {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_roles' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
	}

	/**
	 * Add custom user roles
	 */
	public function add_roles() {
		$administrator = get_role( 'administrator' );

		// Add custom capabilities to administrator role (WP doesn't add them automatically)
		$administrator->add_cap( 'approve_for_profession' );
		$administrator->add_cap( 'access_draft_cards' );

		// add_role adds a role only if it doesn't already exist
		// TODO: We should be able to remove this once roles are correctly defined
		remove_role( 'ammattihyvaksyja' );

		// Ammattihyväksyjä
		add_role(
			'ammattihyvaksyja',
			__( 'Ammattihyväksyjä', 'topten' ),
			array(
				'read'                   => true,
				'edit_tulkintakorttis'   => true,
				'approve_for_profession' => true, // Can approve cards for profession
			)
		);
	}


	/**
	 * Add menu pages
	 */
	public function add_menu_pages() {
		// Approval page for ammattihyväksyjä
		add_menu_page(
			'Ammattihyväksyntä',
			'Ammattihyväksyntä',
			'approve_for_profession',
			'ammattihyvaksynta',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Tulkintakortit
		add_submenu_page(
			'ammattihyvaksynta',
			'Tulkintakortit',
			'Tulkintakortit',
			'approve_for_profession',
			'ammattihyvaksynta-tulkintakortit',
			array( $this, 'render_tulkintakortti' ),
		);

		// Hyväksyntä/Ohjekortit
		add_submenu_page(
			'ammattihyvaksynta',
			'Ohjekortit',
			'Ohjekortit',
			'approve_for_profession',
			'ammattihyvaksynta-ohjekortti',
			array( $this, 'render_ohjekortti' ),
		);

		// Hyväksyntä/Lomakekortit
		add_submenu_page(
			'ammattihyvaksynta',
			'Lomakekortit',
			'Lomakekortit',
			'approve_for_profession',
			'ammattihyvaksynta-lomakekortti',
			array( $this, 'render_lomakekortti' ),
		);
	}

	/**
	 * Render approval page
	 */
	public function render_approval_page() {
		include_once 'pages/approval.php';
	}

	/**
	 * Render profession approval page (tulkintakortti)
	 */
	public function render_tulkintakortti() {
		include_once 'pages/approve-for-profession/approve-tulkintakortti.php';
	}

	/**
	 * Render profession approval page (ohjekortti)
	 */
	public function render_ohjekortti() {
		include_once 'pages/approve-for-profession/approve-ohjekortti.php';
	}

	/**
	 * Render profession approval page (lomakekortti)
	 */
	public function render_lomakekortti() {
		include_once 'pages/approve-for-profession/approve-lomakekortti.php';
	}

	/**
	 * Render approval tables
	 *
	 * @param string $post_type Post type
	 */
	public function render_table( $post_type ) {
		$user_profession = get_field( 'user_profession', 'user_' . get_current_user_id() );

		if ( ! $user_profession ) {
			echo sprintf( '<p>%s</p>', esc_html__( 'Sinulle ei ole asetettu ammattia, jolle voit hyväksyä kortteja.', 'topten' ) );

			return;
		}

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'card_status',
					'value'   => 'draft',
					'compare' => '=',
				),
				array(
					'key'     => 'card_status_draft',
					'value'   => array( 'pending_approval' ),
					'compare' => '=',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'kortin_kategoria',
					'field'    => 'term_id',
					'terms'    => $user_profession,
					'operator' => 'IN',
				),
			),
		);

		$cards = new WP_Query( $args );
		?>
		<div class="topten-table">
			<h2>
				<?php esc_html_e( 'Hyväksyntää odottavat kortit', 'topten' ); ?>
			</h2>

			<table class="topten-datatable">
				<thead>
					<tr>
						<th>
							<?php esc_html_e( 'ID', 'topten' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Kortin nimi', 'topten' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Viesti', 'topten' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Hyväksy', 'topten' ); ?>
						</th>

						<th>
							<?php esc_html_e( 'Hylkää', 'topten' ); ?>
						</th>
					</tr>
				</thead>

				<tbody>
					<?php if ( $cards->have_posts() ) : ?>
						<?php while ( $cards->have_posts() ) : ?>
							<?php $cards->the_post(); ?>

							<tr>
								<td>
									<?php the_ID(); ?>
								</td>

								<td>
									<a href="<?php the_permalink(); ?>" target="_blank">
										<?php the_title(); ?>
									</a>
								</td>

								<td>
									<textarea id="topten-message-<?php echo esc_attr( get_the_ID() ); ?>"></textarea>
								</td>

								<td>
									<button class="topten-approve" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
										<?php esc_html_e( 'Hyväksy kortti', 'topten' ); ?>
									</button>
								</td>

								<td>

								</td>
							</tr>
						<?php endwhile; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
