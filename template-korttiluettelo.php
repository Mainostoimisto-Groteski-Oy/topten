<?php
/**
 * Template name: Korttiluettelo
 *
 * Template for Korttiluettelo pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

get_header();

// Querytaan kaikki julkaistut kortit joiden tila on Julkaistu - Voimassa
// Haetaan kaikki yhdessä queryssa ja tulostellaan niistä tarpeelliset myöhemmin ettei tarvita useampia meta_queryja
$cards = get_posts(
	array(
		'post_type'      => array( 'ohjekortti', 'tulkintakortti', 'lomakekortti' ),
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'card_status',
				'value'   => 'publish',
				'compare' => '=',
			),
			array(
				'key'     => 'card_status_publish',
				'value'   => 'valid',
				'compare' => '=',
			),
		),
	),
);

// Haetaan kaikki lait
$laws = get_terms(
	'laki',
	array(
		'hide_empty' => false,
	)
);

// Ohjekortin kategoriat
$ohje_terms = get_terms(
	'ohje',
	array(
		'hide_empty' => false,
	)
);

// Lomalomakkeen kategoriat
$lomake_terms = get_terms(
	'lomake',
	array(
		'hide_empty' => false,
	)
);

if ( ! empty( $ohje_terms ) && ! empty( $lomake_terms ) ) {
	$terms = array_merge( $ohje_terms, $lomake_terms );
} elseif ( ! empty( $ohje_terms ) ) {
	$terms = $ohje_terms;
} elseif ( ! empty( $lomake_terms ) ) {
	$terms = $lomake_terms;
} else {
	$terms = array();
}
?>

<main id="primary" class="site-main">
	<section class="page-title">
		<div class="grid">
			<h1 class="entry-title h3">
				<?php the_title(); ?>
			</h1>
		</div>
	</section>

	<?php topten_breadcrumbs(); ?>

	<section class="cards">
		<div class="grid">
			<div class="search" id="searchCards">
				<div class="full">
					<div class="input-wrapper keywords">
						<label for="cardKeywords">
							<?php esc_html_e( 'Suodata kortteja asiasanan mukaan', 'topten' ); ?>
						</label>

						<div class="inner-wrapper">
							<input type="text"
								name="cardKeywords"
								id="cardKeywords"
								placeholder="<?php esc_html_e( 'Alkaa kirjaimilla', 'topten' ); ?>..."/>
							<button type="submit" name="keywordSearch" role="search">
								<?php esc_html_e( 'Hae asiasanalla', 'topten' ); ?>
							</button>
						</div>
					</div>

					<div class="keywords" id="selectedKeywords"></div>
				</div>

				<div class="one-third">
					<label for="cardMunicipality">
						<?php esc_html_e( 'Suodata kortteja kunnan mukaan', 'topten' ); ?>
					</label>

					<div class="inner-wrapper">
						<input type="text"
							name="cardMunicipality"
							id="cardMunicipality"
							placeholder="Kirjoita kuntasi nimi tähän"/>

						<button type="submit" name="municipalitySearch" role="search">
							<?php esc_html_e( 'Lisää', 'topten' ); ?>
						</button>
					</div>

					<p class="help">
						<?php esc_html_e( 'Erota kunnat pilkulla', 'topten' ); ?>
					</p>

					<div class="keywords" id="selectedMunicipalities"></div>
				</div>

				<?php if ( $laws ) : ?>
					<div class="one-third">
						<label for="cardLaw">
							<?php esc_html_e( 'Suodata kortteja pykälän mukaan', 'topten' ); ?>
						</label>

						<div class="inner-wrapper">
							<select name="cardLaw" id="cardLaw">
								<?php foreach ( $laws as $law ) : ?>
									<option value="<?php echo esc_attr( $law->term_id ); ?>">
										<?php echo esc_html( $law->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $terms ) : ?>
					<div class="one-third">
						<label for="cardCategory">
							<?php esc_html_e( 'Suodata kortteja luokan mukaan', 'topten' ); ?>
						</label>

						<div class="inner-wrapper">
							<select name="cardCategory" id="cardCategory">
								<?php foreach ( $terms as $term ) : ?>
									<option value="<?php echo esc_attr( $term->term_id ); ?>">
										<?php echo esc_html( $term->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div class="filters" id="filterCards">
				<div class="half">
					<div class="input-wrapper">
						<label for="filterOrder">
							<?php esc_html_e( 'Järjestä korttiluettelo', 'topten' ); ?>
						</label>

						<select name="filterOrder" id="filterOrder">
							<option value="identifier">
								<?php esc_html_e( 'Tunnisteen mukaan', 'topten' ); ?>
							</option>

							<option value="pubDate">
								<?php esc_html_e( 'Julkaisuajankohdan mukaan', 'topten' ); ?>
							</option>

							<option value="alphabetical">
								<?php esc_html_e( 'Aakkosjärjestys', 'topten' ); ?>
							</option>
						</select>
					</div>
				</div>

				<div class="half">
					<div class="input-wrapper horizontal">
						<label for="filterType">
							<?php esc_html_e( 'Näytä vain', 'topten' ); ?>
						</label>

						<div class="inner-wrapper">
							<label for="cardTulkinta">
								<input type="checkbox"
									name="cardTulkinta"
									id="cardTulkinta"
									value="tulkintakortti"
									checked />

								<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
							</label>
						</div>

						<div class="inner-wrapper">
							<label for="cardOhje">
								<input type="checkbox"
									name="cardOhje"
									id="cardOhje"
									value="ohjekortti"
									checked />

								<?php esc_html_e( 'Ohjekortit', 'topten' ); ?>
							</label>
						</div>

						<div class="inner-wrapper">
							<label for="cardLomake">
								<input type="checkbox"
									name="cardLomake"
									id="cardLomake"
									value="lomakekortti"
									checked />

								<?php esc_html_e( 'Lomakekortit', 'topten' ); ?>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="list" id="listCards">
				<div class="cardlist" id="tulkintakortit">
					<h2 class="h4 title">
						<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
					</h2>

					<ul class="cards">
						<?php foreach ( $laws as $index => $law ) : ?>
							<?php
							if ( 0 !== $law->parent ) :
								continue;
							endif;
							?>

							<?php $children = get_term_children( $law->term_id, 'laki' ); ?>

							<li class="parent">
								<p class="name">
									<?php echo esc_html( $law->name ); ?>
								</p>

								<ul class="children">
									<?php foreach ( $children as $child ) : ?>
										<?php $child = get_term_by( 'id', $child, 'laki' ); ?>

										<li class="child">
											<p class="name">
												<?php echo esc_html( $child->name ); ?>
											</p>

											<ul class="grandchildren">
												<?php
												foreach ( $cards as $index => $card ) :
													if ( 'tulkintakortti' === get_post_type( $card->ID ) && is_object_in_term( $card->ID, 'laki', $child->name ) ) :
														topten_get_card( $card );
													endif;
												endforeach;
												?>
											</ul>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="cardlist" id="ohjekortit">
					<h2 class="h4 title">
						<?php esc_html_e( 'Ohjekortit', 'topten' ); ?>
					</h2>

					<ul class="cards">
						<?php foreach ( $ohje_terms as $term ) : ?>
							<li class="parent">
								<p class="name">
									<?php echo esc_html( $term->name ); ?>
								</p>

								<ul class="children">
									<?php
									foreach ( $cards as $card ) :
										if ( 'ohjekortti' === get_post_type( $card->ID ) && is_object_in_term( $card->ID, 'ohje', $term->name ) ) :
											topten_get_card( $card );
										endif;
									endforeach;
									?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="cardlist" id="lomakekortit">
					<h2 class="h4 title">
						<?php esc_html_e( 'Lomakekortit', 'topten' ); ?>
					</h2>

					<ul class="cards">
						<?php foreach ( $lomake_terms as $term ) : ?>
							<li class="parent">
								<p class="name">
									<?php echo esc_html( $term->name ); ?>
								</p>

								<ul class="children">
									<?php
									foreach ( $cards as $card ) :
										if ( 'ohjekortti' === get_post_type( $card->ID ) && is_object_in_term( $card->ID, 'ohje', $term->name ) ) :
											topten_get_card( $card );
										endif;
									endforeach;
									?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<?php the_content(); ?>
</main>

<?php
get_footer();
