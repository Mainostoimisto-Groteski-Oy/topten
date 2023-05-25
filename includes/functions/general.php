<?php
/**
 * Geneerisiä funktioita
 *
 * @package Topten
 */

/**
 * Kääntää parametrin JSONiksi ja kirjoittaa sen error_logiin
 *
 * @param any $data_to_log Logitettava data
 * @return void
 */
function json_log( $data_to_log ) { // phpcs:ignore
	error_log( wp_json_encode( $data_to_log ) ); // phpcs:ignore
}

/**
 * Murupolku
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
 * Tulostaa kortin listaelementtina
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
	$type             = get_post_type( $id );
	$version          = get_field( 'version', $id );
	$post_date         = date( 'j.n.Y', strtotime( $card->post_date ) );
	$link             = get_permalink( $id );

	$html  = '<li class="card">';
	$html .= '<div class="ident">';
	$html .= '<span class="start">' . esc_html( $identifier_start ) . '</span>';
	$html .= '<span class="end"> ' . esc_html( $identifier_end ) . '</span>';
	$html .= '</div>';
	$html .= '<span class="version">' . esc_html( $version ) . '</span>';
	$html .= '<span class="date">' . esc_html( $post_date ) . '</span>';
	$html .= '<span class="card-title">' . esc_html( $title ) . '</span>';
	$html .= '<div class="languages">';
	$html .= '<a href="" class="fi">Fi</a>';
	$html .= '<a href="" class="se">Se</a>';
	$html .= '</div>';
	$html .= '<div class="buttons">';
	$html .= '<a class="button" href="' . esc_url( $link ) . '">';
	$html .= esc_html( 'Siirry kortille', 'topten' );
	$html .= '</a>';
	$html .= '</div>';
	$html .= '</li>';

	if ( 'echo' === $return_format ) {
		echo $html;
	} elseif ( 'return' === $return_format ) {
		return $html;
	} else {
		return false;
	}
}

/**
 * Tulostaa korttilistat
 */
function topten_card_list( $card_array ) {

	// Need to get all the laki and kortin_kategoria terms for this to work
	$laws = get_terms(
		'laki',
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
	// TODO: Comment this shit
	?>
		
		<?php if ( ! empty( $card_array['tulkinta'] ) ) : ?>
			<div class="cardlist" id="tulkintakortit">
				<h2 class="h3 title">
					<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
				</h2>

				<ul class="cards">
					<?php foreach ( $laws as $term ) : ?>
						<?php
						if ( 0 !== $term->parent ) :
							continue;
						endif;

						$children = get_term_children( $term->term_id, 'laki' );
						?>

						<li class="parent" data-id="<?php echo esc_html( $term->term_id ); ?>">
							<p class="name">
								<?php echo esc_html( $term->name ); ?>
							</p>

							<ul class="children" data-parent="<?php echo esc_html( $term->term_id ); ?>">
								<?php foreach ( $children as $child ) : ?>
									<?php $child = get_term( $child ); ?>

									<li class="child" data-id="<?php echo esc_html( $child->term_id ); ?>">
										<p class="name">
											<?php echo esc_html( $child->name ); ?>
										</p>

										<ul class="grandchildren" data-parent="<?php echo esc_html( $child->term_id ); ?>" data-grandparent="<?php echo esc_html( $term->term_id ); ?>">
											<?php
											$cards = $card_array['tulkinta'];
											foreach ( $cards as $card ) {
												if ( 'tulkintakortti' !== get_post_type( $card ) || ! is_object_in_term( $card, 'laki', $child->name ) ) {
													continue;
												}
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
			<?php endif; ?>
			<?php if ( ! empty( $card_array['ohje'] ) ) : ?>
			<div class="cardlist" id="ohjekortit">
				<h2 class="h3 title">
					<?php esc_html_e( 'Ohjekortit', 'topten' ); ?>
				</h2>

				<ul class="cards">
					<?php foreach ( $categories as $category ) : ?>

						<li class="parent" data-id="<?php echo esc_html( $category->term_id ); ?>">
							<p class="name">
								<?php echo esc_html( $category->name ); ?>
							</p>

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
			<?php endif; ?>
			<?php if ( ! empty( $card_array['lomake'] ) ) : ?>
			<div class="cardlist" id="lomakekortit">
				<h2 class="h3 title">
					<?php esc_html_e( 'Lomakekortit', 'topten' ); ?>
				</h2>

				<ul class="cards">
					<?php foreach ( $categories as $category ) : ?>
						<li class="parent" data-id="<?php echo esc_html( $category->term_id ); ?>">
							<p class="name">
								<?php echo esc_html( $category->name ); ?>
							</p>

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
			<?php endif; ?>
		
	<?php
}
