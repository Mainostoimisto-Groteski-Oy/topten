<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin approve cards class
 * Handles approving cards for professions, creating user roles and adding menu pages
 *
 * @since 1.0.0
 *
 * @package Topten\Admin\Users
 */
class Topten_Admin_Users extends Topten_Admin {
	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_roles' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
	}

	/**
	 * Add custom user roles
	 * Currently adds "sisallon_tarkastaja" role
	 *
	 * @since 1.0.0
	 */
	public function add_roles() {
		$administrator = get_role( 'administrator' );

		// Add custom capabilities to administrator role (WP doesn't add them automatically)
		$administrator->add_cap( 'approve_cards' );
		$administrator->add_cap( 'access_draft_cards' );

		// Sisällön tarkastaja
		add_role(
			'sisallon_tarkastaja',
			__( 'Sisällön tarkastaja', 'topten' ),
			array(
				'read'                 => true,
				'edit_tulkintakorttis' => true,
				'approve_cards'        => true, // Can approve cards for profession
				'access_draft_cards'   => true, // Can access draft cards
			)
		);
	}


	/**
	 * Add menu pages
	 *
	 * @since 1.0.0
	 */
	public function add_menu_pages() {
		// Approval page for ammattihyväksyjä
		add_menu_page(
			'Sisällön tarkastus',
			'Sisällön tarkastus',
			'approve_cards',
			'sisallon_tarkastus',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Tulkintakortit
		add_submenu_page(
			'sisallon_tarkastus',
			'Tulkintakortit',
			'Tulkintakortit',
			'approve_cards',
			'sisallon_tarkastus-tulkintakortit',
			array( $this, 'render_tulkintakortti' ),
		);

		// Hyväksyntä/Ohjekortit
		add_submenu_page(
			'sisallon_tarkastus',
			'Ohjekortit',
			'Ohjekortit',
			'approve_cards',
			'sisallon_tarkastus-ohjekortti',
			array( $this, 'render_ohjekortti' ),
		);

		// Hyväksyntä/Lomakekortit
		add_submenu_page(
			'sisallon_tarkastus',
			'Lomakekortit',
			'Lomakekortit',
			'approve_cards',
			'sisallon_tarkastus-lomakekortti',
			array( $this, 'render_lomakekortti' ),
		);
	}

	/**
	 * Render approval page
	 *
	 * @since 1.0.0
	 */
	public function render_approval_page() {
		include_once 'pages/approval.php';
	}

	/**
	 * Render profession approval page (tulkintakortti)
	 *
	 * @since 1.0.0
	 */
	public function render_tulkintakortti() {
		include_once 'pages/approve-cards/approve-tulkintakortti.php';
	}

	/**
	 * Render profession approval page (ohjekortti)
	 *
	 * @since 1.0.0
	 */
	public function render_ohjekortti() {
		include_once 'pages/approve-cards/approve-ohjekortti.php';
	}

	/**
	 * Render profession approval page (lomakekortti)
	 *
	 * @since 1.0.0
	 */
	public function render_lomakekortti() {
		include_once 'pages/approve-cards/approve-lomakekortti.php';
	}

	/**
	 * Render approval tables
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type
	 */
	public function render_table( $post_type ) {
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
		);

		$cards = new WP_Query( $args );
		?>
		<div class="tt-table">
			<h2>
				<?php esc_html_e( 'Hyväksyntää odottavat kortit', 'topten' ); ?>
			</h2>

			<table class="tt-datatable" id="approve-card">
				<thead>
					<tr>
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
									<a href="<?php the_permalink(); ?>" target="_blank">
										<?php the_title(); ?>
									</a>
								</td>

								<td>
									<textarea id="tt-approval-message-<?php echo esc_attr( get_the_ID() ); ?>"></textarea>
								</td>

								<td style="width: 120px;">
									<button class="tt-controls tt-approve" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
										<?php esc_html_e( 'Hyväksy kortti', 'topten' ); ?>
									</button>
								</td>

								<td style="width: 120px;">
									<button class="tt-controls tt-disapprove" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
										<?php esc_html_e( 'Hylkää kortti', 'topten' ); ?>
									</button>
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
