<?php
$user_municipality = get_field( 'user_municipality', 'user_' . get_current_user_id() );

if ( $user_municipality ) {
	$args = array(
		'post_type'      => 'tulkintakortti',
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
} else {
	if ( ! current_user_can( 'administrator' ) ) {
		wp_die( esc_html__( 'Sinulla ei ole oikeuksia tälle sivulle.', 'topten' ) );
	}
}
?>

<?php if ( ! $user_municipality && current_user_can( 'administrator' ) ) : ?>
	<h1>
		<?php esc_html_e( 'Huomio!', 'topten' ); ?>
	</h1>

	<p>
		<?php esc_html_e( 'Tämä sivu on tyhjä, koska tunnuksellasi on ylläpitäjän oikeudet, mutta sille ei ole määritetty kuntaa.', 'topten' ); ?>
	</p>

	<p>
		<?php esc_html_e( 'Voit määrittää tunnuksellesi kunnan profiilissasi.', 'topten' ); ?>
	</p>
<?php else : ?>
	<h1>
		<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
	</h1>

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

						$approve_url = add_query_arg(
							array(
								'action' => 'tt_disapprove_card_for_municipality',
								'post'   => get_the_ID(),
							),
							admin_url( 'admin.php' )
						);

						$approve_url = wp_nonce_url( $approve_url, 'tt_disapprove_card_for_municipality_' . get_the_ID() );
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
<?php endif; ?>
