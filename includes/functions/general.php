<?php
/**
 * Generic functions
 *
 * @package Topten
 */

/**
 * Converts parameter to JSON and writes it to error_log
 *
 * @package Topten\Tools
 *
 * @since 1.0.0
 *
 * @param any $data_to_log Data to log
 */
function json_log( $data_to_log ) { // phpcs:ignore
	error_log( wp_json_encode( $data_to_log, JSON_UNESCAPED_UNICODE ) ); // phpcs:ignore
}

/**
 * Print_r to error_log
 *
 * @package Topten\Tools
 *
 * @since 1.0.0
 *
 * @param any $data_to_log Data to log
 */
function print_log( $data_to_log ) { // phpcs:ignore
	error_log( print_r( $data_to_log, true ) ); // phpcs:ignore
}

/**
 * Breadcrumbs
 *
 * @package Topten\Posts
 *
 * @since 1.0.0
 */
function topten_breadcrumbs() {
	if ( function_exists( 'yoast_breadcrumb' ) ) { ?>
		<section class="page-breadcrumbs">
			<div class="grid">
				<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
			</div>
		</section>
		<?php
	}
}

/**
 * Prints a card as a list element
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param int    $post_id Post ID
 * @param string $return_format Return format
 */
function topten_get_card( $post_id, $return_format = 'echo' ) {
	$id               = $post_id;
	$card             = get_post( $id );
	$identifier_start = get_field( 'identifier_start', $id );
	$identifier_end   = get_field( 'identifier_end', $id );
	$title            = get_the_title( $card );
	$version          = get_field( 'version', $id );
	$post_date        = date( 'j.n.Y', strtotime( $card->post_date ) );
	$link             = get_permalink( $id );
	$languages        = topten_get_card_language_versions( $id );
	$status           = get_field( 'card_status_publish', $id );
	$date             = $post_date;
	$date_class       = '';
	if ( is_array( $status ) ) {
		if ( in_array( 'valid', $status, true ) ) {
			$date       = $post_date;
			$date_class = '';
		}
		if ( in_array( 'expired', $status, true ) ) {
			$date_start = get_field( 'card_valid_start', $id );
			$date_end   = get_field( 'card_valid_end', $id );
			$date_class = 'red';

			if ( $date_start && $date_end ) {
				$date = date( 'j.n.Y', strtotime( $date_start ) ) . ' - ' . date( 'j.n.Y', strtotime( $date_end ) );
			}
		}
		if ( in_array( 'future', $status, true ) ) {
			$date       = get_field( 'card_future_date', $id );
			$date_class = 'green';
		}
	}
	if ( ! empty( $languages['fi']['post'] ) ) {
		$link_fi = get_permalink( $languages['fi']['post']->ID );
	}
	if ( ! empty( $languages['sv']['post'] ) ) {
		$link_sv = get_permalink( $languages['sv']['post']->ID );
	}
	if ( ! empty( $languages['en']['post'] ) ) {
		$link_en = get_permalink( $languages['en']['post']->ID );
	}
	$html  = '<li class="card">';
	$html .= '<a class="cardlink" href="' . esc_url( $link ) . '">';
	$html .= '<span class="screen-reader-text">' . $identifier_start . ' ' . $identifier_end . ' ' . $version . ' ' . $title . '</span>';
	$html .= '<div class="first block">';
	$html .= '<div class="ident">';
	$html .= '<span class="start">' . esc_html( $identifier_start ) . '</span>';
	$html .= '<span class="end"> ' . esc_html( $identifier_end ) . '</span>';
	$html .= '</div>';
	$html .= '<span class="version">' . esc_html( $version ) . '</span>';
	$html .= '<span class="date ' . esc_attr( $date_class ) . '">' . esc_html( $date ) . '</span>';
	$html .= '</div>';
	$html .= '<div class="second block">';
	$html .= '<span class="card-title">' . esc_html( $title ) . '</span>';
	$html .= '</div>';


	/*
	$html .= '<div class="third block">';
	$html .= '<div class="languages">';
	if ( ! empty( $link_fi ) ) {
		$html .= '<a href="' . esc_url( $link_fi ) . '">
		<span>Fi</span>
		<span class="screen-reader-text">' . esc_html__( 'Suomeksi', 'topten' ) . '</span>
		</a>';
	}
	if ( ! empty( $link_sv ) ) {
		$html .= '<a href="' . esc_url( $link_sv ) . '">
		<span>Sv</span>
		<span class="screen-reader-text">' . esc_html__( 'På svenska', 'topten' ) . '</span>
		</a>';
	}
	if ( ! empty( $link_en ) ) {
		$html .= '<a href="' . esc_url( $link_en ) . '">
		<span>En</span>
		<span class="screen-reader-text">' . esc_html__( 'In English', 'topten' ) . '</span>
		</a>';
	}
	$html .= '</div>';
	$html .= '</div>';
	*/

	$html .= '</a>';
	$html .= '</li>';

	if ( 'echo' === $return_format ) {
		echo $html; // phpcs:ignore
	} elseif ( 'return' === $return_format ) {
		return $html;
	} else {
		return false;
	}
}

/**
 * Prints out the card lists
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param array $card_array Card list
 */
function topten_card_list( $card_array, $law_type = '', $active_card_tab = '' ) {
	error_log( 'card list called' );
	// Card arrays are created by the function that calls this one
	// Need to get all the laki and kortin_kategoria terms for this to work
	if ( $law_type === 'rakl' ) {
		$term_name = 'laki_rakl';
	} else {
		$term_name = 'laki';
	}
	$laws = get_terms(
		$term_name,
		array(
			'hide_empty' => false,
		)
	);
	
	$categories = get_terms(
		'kortin_kategoria',
		array(
			'hide_empty' => false,
		)
	);
	// check for empty card arrays to determine where to put active class. if active card tab is set, use that instead
	if ( empty( $active_card_tab ) ) {
		if ( ! empty( $card_array['tulkinta'] ) ) {
			$active_tulkinta = ' active';
		} else {
			$active_tulkinta = '';
		}
		if ( ! empty( $card_array['ohje'] ) && empty( $card_array['tulkinta'] ) ) {
			$active_ohje = ' active';
		} else {
			$active_ohje = '';
		}
		if ( ! empty( $card_array['lomake'] ) && empty( $card_array['tulkinta'] ) && empty( $card_array['ohje'] ) ) {
			$active_lomake = ' active';
		} else {
			$active_lomake = '';
		}
	} else {
		if ( $active_card_tab === 'tulkintakortit' ) {
			$active_tulkinta = ' active';
			$active_ohje     = '';
			$active_lomake   = '';
		} elseif ( $active_card_tab === 'ohjekortit' ) {
			$active_tulkinta = '';
			$active_ohje     = ' active';
			$active_lomake   = '';
		} elseif ( $active_card_tab === 'lomakekortit' ) {
			$active_tulkinta = '';
			$active_ohje     = '';
			$active_lomake   = ' active';
		} else {
			// Default to tulkinta if something unexpected is passed
			$active_tulkinta = 'active';
			$active_ohje     = '';
			$active_lomake   = '';
		}
	}


		// Tulkinta cards have a different structure (one more level) and different taxonomy than the rest.
		// Other cards are like Taxonomy term - cards, these are Taxonomy term - taxonomy child term - cards
	if ( ! empty( $card_array['tulkinta'] ) ) :
		?>
			<div class="cardlist <?php echo esc_attr( $active_tulkinta ); ?>" id="tulkintakortit">
				<h2 class="h2 title">
					<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
				</h2>

				<ul class="cards">
					<?php foreach ( $laws as $term ) : ?>
						<?php
						if ( 0 !== $term->parent ) :
							continue;
						endif;

						$children = get_term_children( $term->term_id, $term_name );
						?>

						<li class="parent" data-id="<?php echo esc_html( $term->term_id ); ?>">
							<h3 class="name h3">
								<?php echo esc_html( $term->name ); ?>
							</h3>

							<ul class="children" data-parent="<?php echo esc_html( $term->term_id ); ?>">
								<?php foreach ( $children as $child ) : ?>
									<?php $child = get_term( $child ); ?>

									<li class="child" data-id="<?php echo esc_html( $child->term_id ); ?>">
										<h4 class="name h4">
											<?php echo esc_html( $child->name ); ?>
										</h4>

										<ul class="grandchildren" data-parent="<?php echo esc_html( $child->term_id ); ?>" data-grandparent="<?php echo esc_html( $term->term_id ); ?>">
											<?php
											$cards = $card_array['tulkinta'];
											foreach ( $cards as $card ) {
												if ( 'tulkintakortti' !== get_post_type( $card ) || ! is_object_in_term( $card, $term_name, $child->name ) ) {
													continue;
												}
												// in the end call the function that creates the actual card and echo it out. could've should've would've been done in js, but this works too
												topten_get_card( $card, 'echo' );
											}
											?>
										</ul>
									</li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<div class="cardlist empty <?php echo esc_attr( $active_tulkinta ); ?>" id="tulkintakortit">
				<h2 class="h2 title">
					<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
				</h2>
				<p class="empty">
					<?php esc_html_e( 'Hakuehdoilla ei löytynyt tulkintakortteja.', 'topten' ); ?>
				</p>
			</div>
		<?php endif; ?>
			<?php // The rest of the cards are simpler, just get the categories and sort the cards into them. Logic is similar ?>
		<?php if ( ! empty( $card_array['ohje'] ) ) : ?>
			<div class="cardlist  <?php echo esc_attr( $active_ohje ); ?>" id="ohjekortit">
				<h2 class="h2 title">
					<?php esc_html_e( 'Ohjekortit', 'topten' ); ?>
				</h2>

				<ul class="cards">
					<?php foreach ( $categories as $category ) : ?>

						<li class="parent" data-id="<?php echo esc_html( $category->term_id ); ?>">
							<h3 class="name h3">
								<?php echo esc_html( $category->name ); ?>
							</h3>

							<ul class="children" data-parent="<?php echo esc_html( $category->term_id ); ?>">
								<?php
								$cards = $card_array['ohje'];
								foreach ( $cards as $card ) {
									if ( 'ohjekortti' !== get_post_type( $card ) || ! is_object_in_term( $card, 'kortin_kategoria', $category->name ) ) {
										continue;
									}
									topten_get_card( $card, 'echo' );
								}
								?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<div class="cardlist empty <?php echo esc_attr( $active_ohje ); ?>" id="ohjekortit">
				<h2 class="h2 title">
					<?php esc_html_e( 'Ohjekortit', 'topten' ); ?>
				</h2>
				<p class="empty">
					<?php esc_html_e( 'Hakuehdoilla ei löytynyt ohjekortteja.', 'topten' ); ?>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $card_array['lomake'] ) ) : ?>
			<div class="cardlist  <?php echo esc_attr( $active_lomake ); ?>" id="lomakekortit">
				<h2 class="h2 title">
					<?php esc_html_e( 'Lomakekortit', 'topten' ); ?>
				</h2>

				<ul class="cards">
					<?php foreach ( $categories as $category ) : ?>
						<li class="parent" data-id="<?php echo esc_html( $category->term_id ); ?>">
							<h3 class="name h3">
								<?php echo esc_html( $category->name ); ?>
							</h3>

							<ul class="children" data-parent="<?php echo esc_html( $category->term_id ); ?>">
								<?php
								$cards = $card_array['lomake'];
								foreach ( $cards as $card ) {
									if ( 'lomakekortti' !== get_post_type( $card ) || ! is_object_in_term( $card, 'kortin_kategoria', $category->name ) ) {
										continue;
									}
									topten_get_card( $card, 'echo' );
								}
								?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<div class="cardlist empty <?php echo esc_attr( $active_lomake ); ?>" id="lomakekortit">
				<h2 class="h2 title">
					<?php esc_html_e( 'Lomakekortit', 'topten' ); ?>
				</h2>
				<p class="empty">
					<?php esc_html_e( 'Hakuehdoilla ei löytynyt lomakekortteja.', 'topten' ); ?>
				</p>
			</div>
		<?php endif; ?>

	<?php
}

/**
 * Card notification
 * Sets up a notification banner that the user is browsing an archive of cards (or a single card) that are either expired or not yet valid
 * Messages are set in ACF options page
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param string $type Type
 */
function topten_card_notification( $type = '' ) {
	if ( ! $type ) :
		return;
	else :
		$id = get_the_ID();

		if ( 'archive' === $type ) :
			
			$status = get_field( 'card_status_type', $id );

			if ( 'future' === $status ) :

				$message        = get_field( 'future_card_archive_note', 'options' );
				$class          = 'future';
				$card_page_type = get_field( 'card_page_type', $id );
				if ( get_field( 'future_card_archive_link', 'options' ) ) {
					if ( 'rakl' === $card_page_type ) {
							$link = get_permalink( get_field( 'main_card_archive_rakl', 'options' ) );
					} else {
						$link = get_permalink( get_field( 'main_card_archive', 'options' ) );
					}               
				} else {
					$link = '';
				}


				elseif ( 'past' === $status ) :

					$message        = get_field( 'expired_card_archive_note', 'options' );
					$class          = 'expired';
					$card_page_type = get_field( 'card_page_type', $id );
					if ( get_field( 'expired_card_archive_link', 'options' ) ) {
						if ( 'rakl' === $card_page_type ) {
							$link = get_permalink( get_field( 'main_card_archive_rakl', 'options' ) );
						} else {
							$link = get_permalink( get_field( 'main_card_archive', 'options' ) );
						}
					} else {
						$link = '';
					}

				endif;

				elseif ( 'single' === $type ) :
					$status = get_field( 'card_status_publish', $id );

					if ( is_array( $status ) ) :

						if ( in_array( 'expired', $status, true ) || in_array( 'repealed', $status, true ) ) :
							$message = get_field( 'expired_card_archive_note', 'options' );
							$class   = 'expired';

							if ( get_field( 'expired_card_archive_link', 'options' ) ) {
								$card_taxonomy_type = get_the_terms( $id, 'card_type' );
								$term_slugs         = wp_list_pluck( $card_taxonomy_type, 'slug' );
								if ( in_array( 'rakl', $term_slugs, true ) ) {
									$link = get_permalink( get_field( 'main_card_archive_rakl', 'options' ) );
								} else {
									$link = get_permalink( get_field( 'main_card_archive', 'options' ) );
								}                       
							} else {
								$link = '';
							}

							elseif ( in_array( 'future', $status, true ) ) :

								$message = get_field( 'future_card_archive_note', 'options' );
								$class   = 'future';

								if ( get_field( 'future_card_archive_link', 'options' ) ) {
									$card_taxonomy_type = get_the_terms( $id, 'card_type' );
									$term_slugs         = wp_list_pluck( $card_taxonomy_type, 'slug' );
									if ( in_array( 'rakl', $term_slugs, true ) ) {
										$link = get_permalink( get_field( 'main_card_archive_rakl', 'options' ) );
									} else {
										$link = get_permalink( get_field( 'main_card_archive', 'options' ) );
									}                       
								} else {
									$link = '';
								}


							endif;

						endif;

				endif;

				if ( ! empty( $message ) ) :
					?>
			<section class="cards-notification <?php echo esc_attr( $class ); ?>">
				<div class="grid">
						<?php if ( ! empty( $link ) ) : ?>
						<a href="<?php echo esc_url( $link ); ?>">
							<p><?php echo esc_html( $message ); ?><span class="material-symbols" aria-hidden="true">double_arrow</span></p>
						</a>
					<?php else : ?>
						<p><?php echo esc_html( $message ); ?></p>
					<?php endif; ?>
				</div>
			</section>
					<?php
		endif;
	endif;
}

/**
 * Decrement letter (B -> A, C -> B, etc.)
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param string $letter Letter
 */
function topten_decrement_letter( string $letter ): string {
	return chr( ord( $letter ) - 1 );
}
