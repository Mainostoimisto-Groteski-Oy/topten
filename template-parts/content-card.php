<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

$id               = $post->ID;
$identifier_start = get_field( 'identifier_start', $id );
$identifier_end   = get_field( 'identifier_end', $id );
$type             = get_post_type( $id );
$version          = get_field( 'version', $id );
$post_date        = date( 'j.n.Y', strtotime( $post->post_date ) );
$keywords         = get_the_terms( $id, 'asiasanat' );
$full_name        = $identifier_start . ' ' . $identifier_end . ' ' . $version . ' ' . get_the_title( $id );
$status           = get_field( 'card_status_publish', $id );

if ( is_array( $status ) ) {
	if ( in_array( 'valid', $status, true ) || in_array( 'approved_for_repeal', $status, true ) ) {
		$status        = 'valid';
		$target_url_id = get_field( 'main_card_archive', 'options' );
		$target_url    = get_permalink( $target_url_id );
		$target_title  = get_the_title( $target_url_id );
	} elseif ( in_array( 'expired', $status, true ) || in_array( 'repealed', $status, true ) ) {
		$status        = 'past';
		$target_url_id = get_field( 'expired_card_archive', 'options' );
		$target_url    = get_permalink( $target_url_id );
		$target_title  = get_the_title( $target_url_id );
	} elseif ( in_array( 'future', $status, true ) ) {
		$target_url_id = get_field( 'future_card_archive', 'options' );
		$target_url    = get_permalink( $target_url_id );
		$target_title  = get_the_title( $target_url_id );
	} else {
		$target_url_id = '';
		$target_url    = '';
		$target_title  = '';
	}
} else {
	$target_url_id = '';
	$target_url    = '';
	$target_title  = '';
}
if ( 'tulkintakortti' === $type ) {
	$prefix = 'tulkinta';
} elseif ( 'ohjekortti' === $type ) {
	$prefix = 'ohje';
} elseif ( 'lomakekortti' === $type ) {
	$prefix = 'lomake';
} else {
	$prefix = '';
}
?>

<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
	<section class="page-breadcrumbs">
		<div class="grid">
			<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
		</div>
	</section>
<?php endif; ?>

<?php topten_card_notification( 'single' ); ?>

<?php if ( get_field( $prefix . '_guide', 'options' ) ) : ?>
	<section class="text-block card">
		<div class="grid">
			<h2 class="title h4"><?php the_field( $prefix . '_guide_title', 'options' ); ?></h2>
			<div class="text">
				<?php the_field( $prefix . '_guide_before', 'options' ); ?>
				<?php
				if ( have_rows( 'guide', 'options' ) ) :
					?>
					<div class="tulkinnat">
						<?php
						while ( have_rows( 'guide', 'options' ) ) :

							the_row();

							$icon  = get_sub_field( 'icon' );
							$color = get_sub_field( 'color' );
							$name  = get_sub_field( 'name' );
							?>
							<div class="tulkinta">
								<p class="<?php echo esc_html( $color ) . ' ' . esc_html( $icon ); ?>">
									<?php echo esc_html( $name ); ?>
								</p>
							</div>
							<?php
						endwhile;
						?>
					</div>
					<?php
				endif;
				?>
				<?php the_field( $prefix . '_guide_after', 'options' ); ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<div class="grid toggle">
	<button class="sidebar-toggle" id="toggleSidebar" aria-label="Avaa sivupalkki" aria-controls="sidebar-menu" aria-expanded="false">
		<span class="material-symbols" aria-hidden="true">
			menu
		</span>
		<p class="menu-explanation closed active" aria-hidden="true">
			<?php esc_html_e( 'Avaa sisällysluettelo', 'topten' ); ?>
		</p>
		<p class="menu-explanation open" aria-hidden="true">
			<?php esc_html_e( 'Sulje sisällysluettelo', 'topten' ); ?>
		</p>
	</button>
</div>

<div class="grid sidebar-grid">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="card-content status-<?php echo esc_attr( $status ); ?>">
			<section class="row-block top">
				<div class="grid">
					<?php if ( get_field( 'rty_logo_cards', 'options' ) || get_field( 'topten_logo_cards', 'options' ) ) : ?>

						<div class="column logos">
							<?php
							if ( get_field( 'rty_logo', 'options' ) && get_field( 'rty_logo_cards', 'options' ) ) :
								$image     = get_field( 'rty_logo', 'options' );
								$image_url = $image['sizes']['medium'];
								$image_alt = $image['alt'];
								?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
							<?php endif; ?>

							<?php
							if ( get_field( 'topten_logo', 'options' ) && get_field( 'topten_logo_cards', 'options' ) ) :
								$image     = get_field( 'topten_logo', 'options' );
								$image_url = $image['sizes']['medium'];
								$image_alt = $image['alt'];
								?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
							<?php endif; ?>
						</div>

						<?php
					endif; // end logos
					?>
					<div class="column content">
						<div class="date">
							<p class="small-title date-title">
								<?php esc_html_e( 'Vahvistuspvm', 'topten' ); ?>

								<strong class="smaller">
									<?php echo esc_html( $post_date ); ?>
								</strong>
							</p>
						</div>

						<div class="inner-wrapper">
							<div class="inner-column identifier">
								<p class="small-title">
									<?php esc_html_e( 'Tunniste', 'topten' ); ?>

									<strong>
										<?php echo esc_html( $identifier_start ); ?>

										<?php echo esc_html( $identifier_end ); ?>
									</strong>
								</p>
							</div>

							<div class="inner-column version">
								<p class="small-title">
									<?php echo esc_html_e( 'Muutos', 'topten' ); ?>

									<strong>
										<?php echo esc_html( $version ); ?>
									</strong>
								</p>
							</div>
						</div>

					</div> <!-- content -->
				</div> <!-- wrapper -->
			</section>

			<?php
			// Global variable for preventing duplicate IDs
			$block_title_ids = array();

			// Global variable for title prefix numbers (1., 1.1., 1.1.1., etc.)
			$title_numbers = topten_get_title_numbers();

			the_content(); // Kortin sisältölohkot
			?>
		</div>

		<button type="button" class="button inverted save-as-pdf" data-type="<?php echo esc_attr( $type ); ?>">
			<?php esc_html_e( 'Tulosta kortti', 'topten' ); ?>
		</button>
	</article>

	<aside class="sidebar" id="sidebar-menu">
		<div class="box open">
			<div class="box-title">
				<h3 class="h2">
					<?php esc_html_e( 'Sisällysluettelo', 'topten' ); ?>
				</h3>

				<button class="material-symbols-button"
					aria-label="<?php esc_html_e( 'Avaa valikko', 'topten' ); ?>"
					aria-expanded="true">
					<span class="material-symbols" aria-hidden="true">
						double_arrow
					</span>
				</button>
			</div>

			<div class="box-content" aria-expanded="true">
				<?php topten_get_table_of_contents(); ?>
			</div>
		</div>

		<?php
		if ( ! empty( $keywords ) ) : // Kortin asiasanat
			?>
			<div class="box">
				<div class="box-title">
					<h3 class="h2">
						<?php esc_html_e( 'Asiasanat', 'topten' ); ?>
					</h3>

					<button class="material-symbols-button"
						aria-label="<?php esc_html_e( 'Avaa valikko', 'topten' ); ?>"
						aria-expanded="true">
						<span class="material-symbols" aria-hidden="true">
							double_arrow
						</span>
					</button>
				</div>

				<div class="box-content">
					<ul class="keywords" aria-expanded="false">
						<?php $keywords_count = count( $keywords ) - 1; ?>

						<?php
						foreach ( $keywords as $index => $keyword ) :
							$redirect_url = $target_url . '?keyword=' . $keyword->term_id;
							?>
							<li class="keyword">
								<a class="name" href="<?php echo esc_url( $redirect_url ); ?>">
									<span>
										<?php echo esc_html( $keyword->name ); ?>
									</span>
								</a>

								<?php if ( get_field( 'link', $keyword->taxonomy . '_' . $keyword->term_id ) ) : ?>
									<a class="keyword-link material-symbols"
										href="<?php echo esc_url( get_field( 'link', $keyword->taxonomy . '_' . $keyword->term_id ) ); ?>"
										target="_blank"
										rel="noopener noreferrer">
										info
									</a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( get_field( 'linked_cards' ) ) : ?>
			<div class="box">
				<div class="box-title">
					<h3 class="h2">
						<?php esc_html_e( 'Liittyvät kortit', 'topten' ); ?>
					</h3>

					<button class="material-symbols" aria-expanded="false">double_arrow</button>
				</div>
				<div class="box-content">
					<?php
					$linked_cards = get_field( 'linked_cards' );
					foreach ( $linked_cards as $linked_card ) :
						$linked_card_id     = $linked_card->ID;
						$linked_card_title  = $linked_card->post_title;
						$linked_card_status = get_field( 'card_status_publish', $linked_card_id );
						$linked_card_url    = get_permalink( $linked_card_id );
						if ( 'Voimassa oleva' === $linked_card_status['label'] ) {
							$content = 'Voimassa';
						} elseif ( '2025' === $linked_card_status['label'] ) {
							$content = 'Rakennuslaki 2025';
						} else {
							$content = 'Vanhentunut';
						}
						?>
						<a class="related-card" href="<?php echo esc_url( $linked_card_url ); ?>" class="linked-card">
							<span><?php echo esc_html( $linked_card_title ); ?></span>
						</a>
						<span class="card-status"><?php echo esc_html( $content ); ?></span>
						<?php
					endforeach;
					?>

				</div>
			</div>

		<?php endif; ?>

		<div class="box">
			<div class="box-title">
				<h3 class="h2">
					<?php esc_html_e( 'Anna palautetta', 'topten' ); ?>
				</h3>

				<button class="material-symbols" aria-expanded="false">double_arrow</button>
			</div>

			<div class="box-content">
				<?php echo do_shortcode( '[gravityform id="2" field_values="card_title=' . esc_html( $full_name ) . '" title="false" description="true" ajax="true"]' ); ?>
			</div>
		</div>

		<div class="box return">
			<a href="<?php echo esc_url( $target_url ); ?>">
				<span class="h3"><?php esc_html_e( 'Siirry korttilistaukseen', 'topten' ); ?></span>
			</a>
		</div>
	</aside>
</div>
