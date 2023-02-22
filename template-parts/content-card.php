<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Topten
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
$versions         = get_field( 'versions', $id );

if ( is_array( $versions ) ) {
	$versions = array_filter(
		$versions,
		function( $version ) {
			global $id;

			return get_field( 'version', $version ) !== get_field( 'version', $id );
		}
	);

	usort(
		$versions,
		function( $a, $b ) {
			$a_version = get_field( 'version', $a );
			$b_version = get_field( 'version', $b );

			if ( $a_version === $b_version ) {
				return 0;
			}

			return $a_version > $b_version ? -1 : 1;
		}
	);
}

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
		$status        = 'future';
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



<section class="card-toggle-container">
	<div class="grid toggle">
		<button class="sidebar-toggle"
			id="toggleSidebar"
			aria-label="<?php echo esc_attr( __( 'Avaa sivupalkki', 'topten' ) ); ?>"
			aria-controls="sidebar-menu"
			aria-expanded="false">
			<span class="material-symbols" aria-hidden="true">
				menu
			</span>

			<p class="menu-explanation closed active" aria-hidden="true">
				<?php esc_html_e( 'Avaa valikko', 'topten' ); ?>
			</p>

			<p class="menu-explanation open" aria-hidden="true">
				<?php esc_html_e( 'Sulje valikko', 'topten' ); ?>
			</p>
		</button>
	</div>
</section>
<section class="single-card-container">
	<div class="grid sidebar-grid">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="card-content-wrapper">
				<div class="card-content status-<?php echo esc_attr( $status ); ?>">
					<section class="row-block top">
						<div class="grid">
							<?php if ( get_field( 'rty_logo_cards', 'options' ) || get_field( 'topten_logo_cards', 'options' ) ) : ?>
								<div class="logos column column-item">
									<?php
									if ( get_field( 'rty_logo', 'options' ) && get_field( 'rty_logo_cards', 'options' ) ) :
										$image     = get_field( 'rty_logo', 'options' );
										$image_url = $image['sizes']['medium'];
										$image_alt = $image['alt'];
										?>
										<img class="rty" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
									<?php endif; ?>

									<?php
									if ( get_field( 'topten_logo', 'options' ) && get_field( 'topten_logo_cards', 'options' ) ) :
										$image     = get_field( 'topten_logo', 'options' );
										$image_url = $image['sizes']['medium'];
										$image_alt = $image['alt'];
										?>
										<img class="topten" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<div class="content column column-item">
								<div class="date">
									<p class="small-title date-title">
										<?php
										if ( 'valid' === $status ) :
											?>
											<?php esc_html_e( 'Vahvistuspvm', 'topten' ); ?>
												<strong class="smaller">
													<?php echo esc_html( $post_date ); ?>
												</strong>
											<?php
										elseif ( 'past' === $status ) :
											?>
											<?php if ( get_field( 'card_valid_start', $id ) && get_field( 'card_valid_end', $id ) ) : ?>

												<?php esc_html_e( 'Voimassaolo', 'topten' ); ?>

												<strong class="smaller">
													<?php the_field( 'card_valid_start', $id ); ?>
												</strong>

												<strong class="smaller">-</strong>

												<strong class="smaller">
													<?php the_field( 'card_valid_end', $id ); ?>
												</strong>
											<?php endif; ?>
										<?php else : ?>
											<?php if ( get_field( 'card_future_date', $id ) ) : ?>
												<?php esc_html_e( 'Astuu voimaan', 'topten' ); ?>
												<strong class="smaller">
													<?php echo esc_html( get_field( 'card_future_date', $id ) ); ?>
												</strong>
											<?php endif; ?>
										<?php endif; ?>
									</p>
								</div>

								<div class="inner-wrapper ident">
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
							</div>
						</div> <!-- content -->
					</section>

					<?php
					// Global variable for preventing duplicate IDs
					$block_title_ids = array();

					// Global variable for title prefix numbers (1., 1.1., 1.1.1., etc.)
					$title_numbers = topten_get_title_numbers();

					the_content(); // Kortin sisältölohkot
					?>
				</div>
			</div>


		</article>

		<aside class="sidebar" id="sidebar-menu">
			<div class="boxes">
				<?php if ( ! get_field( 'hide_toc' ) && false !== topten_get_table_of_contents( false ) ) : ?>
					<div class="box open">
						<div class="box-title">
							<h3 class="h4">
								<?php esc_html_e( 'Sisällysluettelo', 'topten' ); ?>
							</h3>

							<button class="material-symbols-button"
								aria-label="<?php esc_html_e( 'Avaa tai piilota sisällysluettelo', 'topten' ); ?>"
								aria-expanded="true"
								aria-controls="tableOfContents">
								<span class="material-symbols" aria-hidden="true">
									double_arrow
								</span>
							</button>
						</div>

						<div class="box-content" id="tableOfContents" aria-expanded="true">
							<?php topten_get_table_of_contents( true ); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( 'lomakekortti' === $type && 'valid' === $status && ! get_field( 'disable_lomake_actions', $id ) ) : ?>
					<div class="box card-actions">
						<div class="box-title save">
							<span class="material-symbols actions" aria-hidden="true">file_download_done</span>
							<h3 class="h4">
								<?php esc_html_e( 'Tallenna esitäytetty lomake', 'topten' ); ?>
							</h3>
							<button class="material-symbols-button"
								aria-label="<?php esc_html_e( 'Avaa tai piilota tallennusvalikko', 'topten' ); ?>"
								aria-expanded="false"
								aria-controls="keywordList">
								<span class="material-symbols" aria-hidden="true">
									double_arrow
								</span>
							</button>
						</div>
						<div class="box-content">
							<?php if ( get_field( 'lomake_save_text', 'options' ) ) : ?>
								<p>
									<?php the_field( 'lomake_save_text', 'options' ); ?>
								</p>
							<?php endif; ?>
							<button type="button" class="button save-card" aria-haspopup="dialog" aria-expanded="false">
								<?php esc_html_e( 'Tallenna lomake', 'topten' ); ?>
							</button>
						</div>
					</div>

					<div class="box card-actions">
						<div class="box-title load">
							<span class="material-symbols actions" aria-hidden="true">edit_document</span>
							<h3 class="h4">
								<?php esc_html_e( 'Jatka lomakkeen täyttämistä', 'topten' ); ?>
							</h3>

							<button class="material-symbols-button"
								aria-label="<?php esc_html_e( 'Avaa tai piilota lataa -valikko', 'topten' ); ?>"
								aria-expanded="false"
								aria-controls="keywordList">
								<span class="material-symbols" aria-hidden="true">
									double_arrow
								</span>
							</button>

						</div>

						<div class="box-content">
							<?php if ( get_field( 'lomake_load_text', 'options' ) ) : ?>
								<p>
									<?php the_field( 'lomake_load_text', 'options' ); ?>
								</p>
							<?php endif; ?>
							<label class="card-code-label" for="card-code">
								<span class="label-text">
									<?php esc_html_e( 'Syötä saamasi koodi tähän', 'topten' ); ?>
								</span>
								<span class="material-symbols status success">check</span>
								<span class="material-symbols status error">error</span>
								<p class="errormsg"></p>
								<div class="input-with-button">
									<input class="card-code-input"
										id="card-code"
										type="text"
										placeholder="<?php esc_attr_e( 'Syötä saamasi koodi tähän', 'topten' ); ?>" />

									<button type="button" class="button load-card">
										<?php esc_html_e( 'Lähetä', 'topten' ); ?>
									</button>
								</div>
							</label>
						</div>
					</div>

					<div class="box card-actions">

						<div class="box-title clear">
							<span class="material-symbols actions" aria-hidden="true">delete_forever</span>
							<h3 class="h4">
								<?php esc_html_e( 'Tyhjennä lomake', 'topten' ); ?>
							</h3>

							<button class="material-symbols-button"
								aria-label="<?php esc_html_e( 'Avaa tai piilota poista -valikko', 'topten' ); ?>"
								aria-expanded="false"
								aria-controls="keywordList">
								<span class="material-symbols" aria-hidden="true">
									double_arrow
								</span>
							</button>

						</div>

						<div class="box-content">
							<button type="button" class="button clear-input" data-type="<?php echo esc_attr( $type ); ?>">
								<?php esc_html_e( 'Tyhjennä lomake', 'topten' ); ?>
							</button>
						</div>

					</div>

				<?php endif; ?>

				<?php if ( ! empty( $keywords ) ) : ?>
					<div class="box">
						<div class="box-title keywords">
							<h3 class="h4">
								<?php esc_html_e( 'Asiasanat', 'topten' ); ?>
							</h3>

							<button class="material-symbols-button"
								aria-label="<?php esc_html_e( 'Avaa tai piilota asiasanavalikko', 'topten' ); ?>"
								aria-expanded="true"
								aria-controls="keywordList">
								<span class="material-symbols" aria-hidden="true">
									double_arrow
								</span>
							</button>
						</div>

						<div class="box-content">
							<ul class="keywords" id="keywordList" aria-expanded="false">

								<?php
								foreach ( $keywords as $index => $keyword ) :
									$redirect_url = $target_url . '?keyword=' . $keyword->term_id;
									?>
									<li class="keyword
									<?php
									if ( ! empty( term_description( $keyword->term_id ) ) ) :
										?>
									has-description <?php endif; ?>"
									data-id="<?php echo esc_attr( $keyword->term_id ); ?>">


										<?php // always show name ?>
										<span class="name">
											<?php echo esc_html( $keyword->name ); ?>
										</span>

										<?php // if desc exists, create container for it ?>
										<?php if ( ! empty( term_description( $keyword->term_id ) ) ) : ?>
											<div class="keyword-description-container" id="desc-<?php echo esc_attr( $keyword->term_id ); ?>">
												<div class="keyword-description-wrapper">
													<button class="close-button keyword" aria-role="button" aria-label="<?php esc_html_e( 'Sulje asiasanan kuvaus', 'topten' ); ?>">
														<span class="material-symbols" aria-hidden="true">close</span>
													</button>
													<?php echo term_description( $keyword->term_id ); ?>
													<?php // if a link is set, create one it in case user has not added it in the description ?>
													<?php if ( get_field( 'link', $keyword->taxonomy . '_' . $keyword->term_id ) ) : ?>
														<a class="keyword-link"
														href="<?php echo esc_url( get_field( 'link', $keyword->taxonomy . '_' . $keyword->term_id ) ); ?>"
														target="_blank"
														rel="noopener noreferrer"
														><span>
															<?php esc_html_e( 'Lisätietoja', 'topten' ); ?>
														</span>
														<span class="screen-reader-text">
															<?php esc_html_e( 'Avaa uudessa välilehdessä', 'topten' ); ?>
														</span>
														</a>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( term_description( $keyword->term_id ) ) ) : ?>
											<div class="keyword-info">
												<span class="icon" aria-hidden="true">i</span>
											</div>
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
							<h3 class="h4">
								<?php esc_html_e( 'Liittyvät kortit', 'topten' ); ?>
							</h3>

							<button aria-expanded="false"
							aria-controls="linkedCards"
							aria-label="<?php esc_html_e( 'Avaa tai piilota liittyvät kortit', 'topten' ); ?>"><span class="material-symbols" aria-hidden="true">double_arrow</span></button>
						</div>
						<div class="box-content" id="linkedCards">
							<?php
							$linked_cards = get_field( 'linked_cards' );
							foreach ( $linked_cards as $linked_card ) :
								$linked_card_id     = $linked_card->ID;
								$linked_card_title  = $linked_card->post_title;
								$linked_card_status = get_field( 'card_status_publish', $linked_card_id );
								$linked_card_url    = get_permalink( $linked_card_id );
								if ( 'Voimassa oleva' === $linked_card_status['label'] ) {
									$content = esc_html__( 'Voimassa', 'topten' );
								} elseif ( '2025' === $linked_card_status['label'] ) {
									$content = esc_html__( 'Rakentamislaki 2025', 'topten' );
								} else {
									$content = esc_html__( 'Vanhentunut', 'topten' );
								}
								?>
								<span class="card-status"><?php echo esc_html( $content ); ?></span>
								<a class="related-card" href="<?php echo esc_url( $linked_card_url ); ?>" class="linked-card">
									<span><?php echo esc_html( $linked_card_title ); ?></span>
								</a>

								<?php
							endforeach;
							?>

						</div>
					</div>
				<?php endif; ?>

				<?php if ( $versions ) : ?>
					<div class="box">
						<div class="box-title">
							<h3 class="h4">
								<?php esc_html_e( 'Kortin muutokset', 'topten' ); ?>
							</h3>

							<button aria-expanded="false"
								aria-controls="feedback"
								aria-label="<?php esc_html_e( 'Avaa tai piilota kortin muutokset', 'topten' ); ?>">
								<span class="material-symbols" aria-hidden="true">
									double_arrow
								</span>
							</button>
						</div>

						<div class="box-content" id="changes">
							<?php $diff_url = site_url( '/vertaa?card_id=' . get_the_ID() ); ?>

							<a class="button action" href="<?php echo esc_url( $diff_url ); ?>">
								<span class="link-text">
									<?php esc_html_e( 'Vertaa korttia edelliseen versioon', 'topten' ); ?>
								</span>
							</a>

							<p>
								<?php esc_html_e( 'Tästä kortista on olemassa aiemmin luodut versiot:', 'topten' ); ?>
							</p>

							<?php foreach ( $versions as $old_version ) : ?>
								<?php
								// Check if the version exists
								if ( ! get_post( $old_version ) ) {
									continue;
								}

								$href           = get_permalink( $old_version );
								$title          = get_the_title( $old_version );
								$version_number = get_field( 'version', $old_version );
								?>
								<a href="<?php echo esc_url( $href ); ?>" title="<?php echo esc_attr( $title ); ?>">
									<?php
									printf(
										/* translators: %s: version number */
										esc_html__( 'Siirry korttiarkistoon tutkailemaan versiota %s', 'topten' ),
										esc_html( $version_number )
									);
									?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( 'valid' === $status || 'future' === $status ) : ?>
				<div class="box feedback">
					<div class="box-title">
						<h3 class="h4">
							<?php esc_html_e( 'Anna palautetta tästä kortista', 'topten' ); ?>
						</h3>

						<button
						aria-expanded="false"
						aria-controls="feedback"
						aria-label="<?php esc_html_e( 'Avaa tai piilota palautelomake', 'topten' ); ?>"><span class="material-symbols" aria-hidden="true">double_arrow</span></button>
					</div>

					<div class="box-content" id="feedback">
						<?php echo do_shortcode( '[gravityform id="2" field_values="card_title=' . esc_html( $full_name ) . '" title="false" description="true" ajax="true"]' ); ?>
					</div>
				</div>
				<?php endif; ?>


				<div class="box links">
					<div class="buttons">

						<?php if ( $versions ) : ?>
							<?php $diff_url = site_url( '/vertaa?card_id=' . get_the_ID() ); ?>

							<a class="button" href="<?php echo esc_url( $diff_url ); ?>">
								<span class="link-text">
									<?php esc_html_e( 'Vertaa korttia edelliseen versioon', 'topten' ); ?>
								</span>
							</a>
						<?php endif; ?>

						<?php if ( ! get_field( 'disable_pdf' ) && 'valid' === $status ) : ?>
							<button type="button" class="action button save-as-pdf" data-type="<?php echo esc_attr( $type ); ?>">
								<span class="link-text"><?php esc_html_e( 'Tulosta kortti', 'topten' ); ?></span>
							</button>
						<?php endif; ?>

						<a class="button doubleback" href="<?php echo esc_url( $target_url ); ?>">
							<span class="material-symbols" aria-hidden="true">
								keyboard_double_arrow_left
							</span>

							<span class="link-text">
								<?php esc_html_e( 'Siirry korttilistaukseen', 'topten' ); ?>
							</span>
						</a>

						<?php $faq_url = get_permalink( get_field( 'faq_page', 'options' ) ); ?>

						<a class="button doubleback" href="<?php echo esc_url( $faq_url ); ?>">
							<span class="material-symbols" aria-hidden="true">
								keyboard_double_arrow_left
							</span>

							<span class="link-text">
								<?php esc_html_e( 'Usein kysytyt kysymykset', 'topten' ); ?>
							</span>
						</a>
					</div>
				</div>


			</div>
		</aside>
	</div>

	<div class="save-card-modal modal">
		<div class="modal-content"
			tabindex="0"
			role="dialog"
			aria-labelledby="save-card-modal-title"
			aria-modal="true">
			<?php if ( get_field( 'lomake_modal_title', 'options' ) ) : ?>
				<h2 id="save-card-modal-title" class="h4 modal-title">
					<?php the_field( 'lomake_modal_title', 'options' ); ?>
				</h2>
			<?php endif; ?>
			<?php if ( get_field( 'lomake_modal_text_before', 'options' ) ) : ?>
				<p>
					<?php the_field( 'lomake_modal_text_before', 'options' ); ?>
				</p>
			<?php endif; ?>

			<div class="copy-card-code-wrapper" aria-live="polite" aria-busy="false">
				<label for="card-code-textarea" class="card-code-textarea-wrapper">
					<span class="label-text">
						<?php esc_html_e( 'Koodi', 'topten' ); ?>
					</span>

					<div class="textarea-wrapper">
						<div class="loading-spinner" aria-hidden="true"></div>

						<textarea id="card-code-textarea" rows="1" disabled></textarea>

						<button class="copy-card-code"
							type="button"
							aria-label="<?php esc_html_e( 'Kopioi koodi', 'topten' ); ?>"
							title="<?php esc_html_e( 'Kopioi koodi', 'topten' ); ?>">
							<span class="material-symbols-outlined" aria-hidden="true">
								content_copy
							</span>
						</button>
					</div>
				</label>

				<div class="message-wrapper">
					<p class="message"></p>
				</div>
			</div>

			<?php if ( get_field( 'lomake_modal_text_after', 'options' ) ) : ?>
				<p>
					<?php the_field( 'lomake_modal_text_after', 'options' ); ?>
				</p>
			<?php endif; ?>

			<label class="card-code-label" for="copy-card-code">
				<span class="label-text">
					<?php esc_html_e( 'Sähköpostiosoite', 'topten' ); ?>
				</span>

				<div class="input-with-button">
					<input class="card-code-input"
						id="send-card-code-email"
						type="email"
						placeholder="<?php esc_attr_e( 'Sähköpostiosoite', 'topten' ); ?>" />

					<button id="send-code-to-email" type="button" class="button">
						<?php esc_html_e( 'Lähetä', 'topten' ); ?>
					</button>
				</div>
			</label>

			<button class="close-modal"
				type="button"
				aria-label="<?php esc_attr_e( 'Sulje', 'topten' ); ?>">
				<span class="material-symbols-outlined" aria-hidden="true">
					close
				</span>
			</button>
		</div>

	</div>
	<div class="lomake-card-success">
		<div class="message-wrapper">
			<p class="message"></p>
		</div>
	</div>
</section>
