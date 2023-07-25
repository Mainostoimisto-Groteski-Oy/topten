<?php
defined( 'ABSPATH' ) || exit;

/**
 * User roles
 */
class Topten_Admin_Approval_Municipality extends Topten_Admin_Users {
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
		$administrator->add_cap( 'approve_for_municipality' );
		$administrator->add_cap( 'access_draft_cards' );

		// add_role adds a role only if it doesn't already exist
		// TODO: We should be able to remove this once roles are correctly defined
		remove_role( 'kuntahyvaksyja' );

		// Ammattihyväksyjä
		add_role(
			'kuntahyvaksyja',
			__( 'Kuntahyväksyjä', 'topten' ),
			array(
				'read'                     => true,
				'edit'                     => true,
				'approve_for_municipality' => true, // Can approve cards for municipality
				'access_draft_cards'       => true, // Can access draft cards
			)
		);
	}

	/**
	 * Add menu pages
	 */
	public function add_menu_pages() {
		// Approval page for ammattihyväksyjä
		add_menu_page(
			'Hyväksyntä',
			'Hyväksyntä',
			'approve_for_municipality',
			'hyvaksynta',
			array( $this, 'render_approval_page' ),
		);

		// Hyväksyntä/Tulkintakortit
		add_submenu_page(
			'hyvaksynta',
			'Tulkintakortit',
			'Tulkintakortit',
			'approve_for_municipality',
			'hyvaksynta-tulkintakortit',
			array( $this, 'render_tulkintakortti' ),
		);

		// Hyväksyntä/Ohjekortit
		add_submenu_page(
			'hyvaksynta',
			'Ohjekortit',
			'Ohjekortit',
			'approve_for_municipality',
			'hyvaksynta-ohjekortti',
			array( $this, 'render_ohjekortti' ),
		);

		// Hyväksyntä/Lomakekortit
		add_submenu_page(
			'hyvaksynta',
			'Lomakekortit',
			'Lomakekortit',
			'approve_for_municipality',
			'hyvaksynta-lomakekortti',
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
	 * Render municipality approval page (tulkintakortti)
	 */
	public function render_tulkintakortti() {
		include_once 'pages/approve-for-municipality/approve-tulkintakortti.php';
	}

	/**
	 * Render municipality approval page (ohjekortti)
	 */
	public function render_ohjekortti() {
		include_once 'pages/approve-for-municipality/approve-ohjekortti.php';
	}

	/**
	 * Render municipality approval page (lomakekortti)
	 */
	public function render_lomakekortti() {
		include_once 'pages/approve-for-municipality/approve-lomakekortti.php';
	}

	/**
	 * Render approval tables
	 *
	 * @param string $post_type Post type
	 */
	public function render_table( $post_type ) {
		$user_municipality = get_field( 'user_municipality', 'user_' . get_current_user_id() );

		if ( ! $user_municipality ) {
			echo sprintf( '<p>%s</p>', esc_html__( 'Sinulle ei ole asetettu kuntaa, jolle voit hyväksyä kortteja.', 'topten' ) );

			return;
		}

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'tax_query'      => array(
				array(
					'taxonomy' => 'kunta',
					'field'    => 'term_id',
					'terms'    => $user_municipality,
					'operator' => 'NOT IN',
				),
			),
		);

		$not_approved = new WP_Query( $args );

		$args['tax_query'][0]['operator'] = 'IN';

		$approved = new WP_Query( $args );
		?>
		<div class="topten-table">
			<h2>
				<?php esc_html_e( 'Hyväksymättömät kortit', 'topten' ); ?>
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
						</th>
					</tr>
				</thead>

				<tbody>
					<?php if ( $not_approved->have_posts() ) : ?>
						<?php while ( $not_approved->have_posts() ) : ?>
							<?php
							$not_approved->the_post();

							$approve_url = add_query_arg(
								array(
									'action' => 'tt_approve_card_for_municipality',
									'post'   => get_the_ID(),
								),
								admin_url( 'admin.php' )
							);

							$approve_url = wp_nonce_url( $approve_url, 'tt_approve_card_for_municipality_' . get_the_ID() );
							?>

							<tr>
								<td>
									<?php the_ID(); ?>
								</td>
								<td>
									<a href="<?php the_permalink(); ?>">
										<?php the_title(); ?>
									</a>
								</td>
								<td>
									<a href="<?php echo esc_url( $approve_url ); ?>">
										<?php esc_html_e( 'Ota kortti käyttöön', 'topten' ); ?>
									</a>
								</td>
							</tr>
						<?php endwhile; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="topten-table">
			<h2>
				<?php esc_html_e( 'Hyväksytyt kortit', 'topten' ); ?>
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
						</th>
					</tr>
				</thead>

				<tbody>
					<?php if ( $approved->have_posts() ) : ?>
						<?php while ( $approved->have_posts() ) : ?>
							<?php
							$approved->the_post();

							$disapprove_url = add_query_arg(
								array(
									'action' => 'tt_disapprove_card_for_municipality',
									'post'   => get_the_ID(),
								),
								admin_url( 'admin.php' )
							);

							$disapprove_url = wp_nonce_url( $disapprove_url, 'tt_disapprove_card_for_municipality_' . get_the_ID() );
							?>
							<tr>
								<td>
									<?php the_ID(); ?>
								</td>
								<td>
									<a href="<?php the_permalink(); ?>">
										<?php the_title(); ?>
									</a>
								</td>
								<td>
									<a href="<?php echo esc_url( $disapprove_url ); ?>">
										<?php esc_html_e( 'Poista kortti käytöstä', 'topten' ); ?>
									</a>
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
