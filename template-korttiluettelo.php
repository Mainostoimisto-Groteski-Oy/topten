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

// Kortin kategoriat
$categories = get_terms(
	'kortin_kategoria',
	array(
		'hide_empty' => false,
	)
);

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
		<div class="grid top">
			<div class="content-area">
				<div class="search" id="searchCards" role="search">
					<div class="full">
						<div class="input-wrapper freeText">
							<label for="freeText">
								<?php esc_html_e( 'Vapaa haku', 'topten' ); ?>
							</label>

							<div class="inner-wrapper">
								<input type="text"
									name="freeText"
									id="freeText"
									placeholder="<?php esc_html_e( 'Avoin tekstihaku', 'topten' ); ?>" />

								<button type="submit"
									class="searchTrigger"
									name="textSearch"
									id="textSearch">
									<?php esc_html_e( 'Hae', 'topten' ); ?>
								</button>
							</div>

						</div>
					</div>

					<div class="full">
						<div class="input-wrapper keywords">
							<label for="cardKeywords">
								<?php esc_html_e( 'Suodata kortteja asiasanan mukaan', 'topten' ); ?>
							</label>

							<div class="inner-wrapper">
								<input type="text"
									name="cardKeywords"
									id="cardKeywords"
									placeholder="<?php esc_html_e( 'Alkaa kirjaimilla...', 'topten' ); ?>" />

								<button type="submit"
									name="keywordSearch"
									id="keywordSearch" >
									<?php esc_html_e( 'Lisää', 'topten' ); ?>
								</button>
							</div>

							<small class="small">
								<?php esc_html_e( 'Erota asiasanat pilkulla', 'topten' ); ?>
							</small>
						</div>

						

					</div>
					

					<div class="full">
						<div class="input-wrapper keywords">
							<label for="cardMunicipality">
								<?php esc_html_e( 'Suodata kortteja kunnan mukaan', 'topten' ); ?>
							</label>

							<div class="inner-wrapper">
								<input type="text"
									name="cardMunicipality"
									id="cardMunicipality"
									placeholder="<?php esc_html_e( 'Kirjoita kuntasi nimi tähän', 'topten' ); ?>" />

								<button type="submit"
									name="municipalitySearch"
									id="municipalitySearch">
									<?php esc_html_e( 'Lisää', 'topten' ); ?>
								</button>
							</div>

							<small class="small">
								<?php esc_html_e( 'Erota kunnat pilkulla', 'topten' ); ?>
							</small>
						</div>
							
				
					
					</div>

					<div class="one-third">
						<div class="input-wrapper dateRange">
							<label for="cardDateStart">
								<?php esc_html_e( 'Suodata kortteja laatimisajan mukaan', 'topten' ); ?>
							</label>

							<div class="inner-wrapper date">
								<input type="date" name="cardDateStart" id="cardDateStart"/> - <input type="date" name="cardDateEnd" id="cardDateEnd"/>
							</div>
						</div>

					</div>

					<?php if ( $laws ) : ?>
						<div class="one-third">
							<label for="cardLaw">
								<?php esc_html_e( 'Suodata kortteja pykälän mukaan', 'topten' ); ?>
							</label>

							<div class="inner-wrapper">
								<select class="searchTrigger" name="cardLaw" id="cardLaw">
									<option value="" selected>
										<?php esc_html_e( 'Valitse', 'topten' ); ?>
									</option>

									<?php foreach ( $laws as $law ) : ?>
										<option value="<?php echo esc_attr( $law->term_id ); ?>">
											<?php echo esc_html( $law->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $categories ) : ?>
						<div class="one-third">
							<label for="cardCategory">
								<?php esc_html_e( 'Suodata kortteja luokan mukaan', 'topten' ); ?>
							</label>

							<div class="inner-wrapper">
								<select class="searchTrigger" name="cardCategory" id="cardCategory">
									<option value="" selected>
										<?php esc_html_e( 'Valitse', 'topten' ); ?>
									</option>

									<?php foreach ( $categories as $category ) : ?>
										<option value="<?php echo esc_attr( $category->term_id ); ?>">
										<?php echo esc_html( $category->name ); ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div> <!-- end content area -->
			
			<div class="sidebar" id="cardSidebar">
				<ul class="keywords" id="selectedKeywords"></ul>
				<ul class="keywords" id="selectedMunicipalities"></ul>
			</div>
			<div id="test" style="grid-column: 1 / -1"></div>
		</div><!-- end top grid -->

		<div class="grid">
			<div class="filters" id="filterCards" role="search">
				<div class="half">
					<div class="input-wrapper">
						<label for="filterOrder">
							<?php esc_html_e( 'Järjestä korttiluettelo', 'topten' ); ?>
						</label>

						<select class="filterTrigger" name="filterOrder" id="filterOrder">
							<option value="identifier">
								<?php esc_html_e( 'Tunnisteen mukaan', 'topten' ); ?>
							</option>

							<option value="publishDate">
								<?php esc_html_e( 'Julkaisuajankohdan mukaan', 'topten' ); ?>
							</option>

							<option value="title">
								<?php esc_html_e( 'Aakkosjärjestys', 'topten' ); ?>
							</option>
						</select>
					</div>
				</div>

				<div class="half">
					<div class="input-wrapper horizontal">
						<p for="filterType">
							<?php esc_html_e( 'Näytä vain', 'topten' ); ?>
						</p>

						<label for="cardTulkinta" class="inner-wrapper">
							<input class="filterTrigger"
								type="checkbox"
								name="cardTulkinta"
								id="cardTulkinta"
								value="tulkintakortti"
								checked />

							<?php esc_html_e( 'Tulkintakortit', 'topten' ); ?>
						</label>

						<label for="cardOhje" class="inner-wrapper">
							<input class="filterTrigger"
								type="checkbox"
								name="cardOhje"
								id="cardOhje"
								value="ohjekortti"
								checked />

							<?php esc_html_e( 'Ohjekortit', 'topten' ); ?>
						</label>

						<label for="cardLomake" class="inner-wrapper">
							<input class="filterTrigger"
								type="checkbox"
								name="cardLomake"
								id="cardLomake"
								value="lomakekortti"
								checked />
							<?php esc_html_e( 'Lomakekortit', 'topten' ); ?>
						</label>
					</div>
				</div>
			</div>

			<div class="list" id="listCards">
				<div class="cardlist" id="tulkintakortit">
					<h2 class="h4 title">
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

							<li class="parent" data-id="<?php echo esc_html($term->term_id); ?>">
								<p class="name">
									<?php echo esc_html( $term->name ); ?>
								</p>

								<ul class="children" data-parent="<?php echo esc_html($term->term_id); ?>">
									<?php foreach ( $children as $child ) : ?>
										<?php $child = get_term( $child ); ?>

										<li class="child" data-id="<?php echo esc_html($child->term_id); ?>">
											<p class="name">
												<?php echo esc_html( $child->name ); ?>
											</p>

											<ul class="grandchildren" data-parent="<?php echo esc_html($child->term_id); ?>" data-grandparent="<?php echo esc_html($term->term_id); ?>">
												<?php foreach ( $cards as $card ) {
													if ( 'tulkintakortti' !== get_post_type( $card->ID ) || ! is_object_in_term( $card->ID, 'laki', $child->name ) ) {
														continue;
													}
													topten_get_card( $card, 'echo');
												} ?>
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
						<?php foreach ( $categories as $category ) : ?>

							<li class="parent" data-id="<?php echo esc_html( $category->term_id ); ?>">
								<p class="name">
									<?php echo esc_html( $category->name ); ?>
								</p>

								<ul class="children" data-parent="<?php echo esc_html( $category->term_id ); ?>">
									<?php
									foreach ( $cards as $card ) {
										if ( 'ohjekortti' !== get_post_type( $card->ID ) || ! is_object_in_term( $card->ID, 'kortin_kategoria', $category->name ) ) {
											continue;
										}
										topten_get_card( $card, 'echo');
									} ?>
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
						<?php foreach ( $categories as $category ) : ?>
							<li class="parent" data-id="<?php echo esc_html( $category->term_id ); ?>">
								<p class="name">
									<?php echo esc_html( $category->name ); ?>
								</p>

								<ul class="children" data-parent="<?php echo esc_html( $category->term_id ); ?>">
									<?php
									foreach ( $cards as $card ) {
										if ( 'lomakekortti' !== get_post_type( $card->ID ) || ! is_object_in_term( $card->ID, 'kortin_kategoria', $category->name ) ) {
											continue;
										}
										topten_get_card( $card, 'echo');
									} ?>
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
