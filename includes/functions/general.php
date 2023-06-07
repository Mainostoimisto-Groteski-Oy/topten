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
	$post_date        = date( 'j.n.Y', strtotime( $card->post_date ) );
	$link             = get_permalink( $id );
	$languages = topten_get_card_language_versions( $id );
	if(!empty($languages['fi']['post'])) {
		$link_fi = get_permalink($languages['fi']['post']->ID);
	}
	if(!empty($languages['sv']['post'])) {
		$link_sv = get_permalink($languages['sv']['post']->ID);
	}
	if(!empty($languages['en']['post'])) {
		$link_en = get_permalink($languages['en']['post']->ID);
	}
	$html  = '<li class="card">';
	$html .= '<div class="first block">';
	$html .= '<div class="ident">';
	$html .= '<span class="start">' . esc_html( $identifier_start ) . '</span>';
	$html .= '<span class="end"> ' . esc_html( $identifier_end ) . '</span>';
	$html .= '</div>';
	$html .= '<span class="version">' . esc_html( $version ) . '</span>';
	$html .= '<span class="date">' . esc_html( $post_date ) . '</span>';
	$html .= '</div>';
	$html .= '<div class="second block">';
	$html .= '<span class="card-title">' . esc_html( $title ) . '</span>';
	$html .= '</div>';
	$html .= '<div class="third block">';
	$html .= '<div class="languages">';
	if(!empty($link_fi)) {
		$html .= '<a target="_blank" href="' . esc_url( $link_fi ) . '">
		<span>Fi</span>
		<span class="screen-reader-text">'. esc_html__( 'Avautuu uuteen ikkunaan, suomeksi', 'topten' ) .'</span>
		</a>';
	}
	if(!empty($link_sv)) {
		$html .= '<a target="_blank" href="' . esc_url( $link_sv ) . '">
		<span>Sv</span>
		<span class="screen-reader-text">'. esc_html__( 'Avautuu uuteen ikkunaan, ruotsiksi', 'topten' ) .'</span>
		</a>';
	}
	if(!empty($link_en)) {
		$html .= '<a target="_blank" href="' . esc_url( $link_en ) . '">
		<span>En</span>		
		<span class="screen-reader-text">'. esc_html__( 'Avautuu uuteen ikkunaan, englanniksi', 'topten' ) .'</span>
		</a>';
	}
	$html .= '</div>';
	$html .= '<div class="buttons">';
	$html .= '<a class="button" href="' . esc_url( $link ) . '" target="_blank">';
	$html .= esc_html( 'Siirry kortille', 'topten' );
	$html .= '<span class="screen-reader-text">'. esc_html__( 'Linkki aukeaa uuteen ikkunaan', 'topten' ) .'</span></a>';
	$html .= '</div>';
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


function topten_card_notification($type = '') {
	
	if(!$type) :
		return;
	else :
		$id = get_the_ID(); 

			if( 'archive' === $type ) :

				$status = get_field( 'card_status_type', $id );

				if ( 'future' ===  $status ) :

					$message = get_field('future_card_archive_note', 'options');
					$class = 'future';

					if(get_field('future_card_archive_link', 'options')) {
						$link = get_permalink(get_field('main_card_archive', 'options'));
					} else {
						$link = '';
					}
					

				elseif ( 'past' === $status ) :

					$message = get_field('expired_card_archive_note', 'options');
					$class = 'expired';

					if(get_field('expired_card_archive_link', 'options')) {
						$link = get_permalink(get_field('main_card_archive', 'options'));
					} else {
						$link = '';
					}

				endif;
				
			elseif ('single' === $type) :

				$status = get_field( 'card_status_publish', $id );

					if ( is_array( $status ) ) :
		
						if ( in_array( 'expired', $status ) || in_array( 'repealed', $status ) ) :

							$message = get_field('expired_card_archive_note', 'options');
							$class = 'expired';
							
							if(get_field('expired_card_archive_link', 'options')) {
								$link = get_permalink(get_field('main_card_archive', 'options'));
							} else {
								$link = '';
							}

						elseif ( in_array( 'future', $status ) ) :

							$message = get_field('future_card_archive_note', 'options');
							$class = 'future';

							if(get_field('future_card_archive_link', 'options')) {
								$link = get_permalink(get_field('main_card_archive', 'options'));
							} else {
								$link = '';
							}

						endif;
					
					endif;

				endif;

		if(!empty($message)) : ?>
			<section class="cards-notification <?php echo esc_attr($class); ?>">
				<div class="grid">
					<?php if(!empty($link)) : ?>
						<a href="<?php echo esc_url($link); ?>">
							<p><?php echo esc_html($message); ?></p>	
						</a>
					<?php else : ?>
						<p><?php echo esc_html($message); ?></p>
					<?php endif; ?>
				</div>
			</section>
			<?php
		endif;
	endif; 	
}