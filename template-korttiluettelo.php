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
		<div class="grid">
			<div class="search" id="searchCards">
				<div class="full">
					<div class="input-wrapper freeText">
						<label for="freeText">
							<?php esc_html_e( 'Vapaa haku', 'topten' ); ?>
						</label>

						<div class="inner-wrapper">
							<input type="text"
								name="freeText"
								id="freeText"
								placeholder="<?php esc_html_e( 'Alkaa kirjaimilla...', 'topten' ); ?>" />

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
								class="searchTrigger"
								name="keywordSearch"
								id="keywordSearch" >
								<?php esc_html_e( 'Hae asiasanalla', 'topten' ); ?>
							</button>
						</div>

						<small class="small">
							<?php esc_html_e( 'Erota asiasanat pilkulla', 'topten' ); ?>
						</small>
					</div>

					<ul class="keywords" id="selectedKeywords"></ul>
				</div>

				<div class="full">
					<div class="input-wrapper dateRange">
						<label for="cardDateStart">
							<?php esc_html_e( 'Suodata kortteja laatimisajan mukaan', 'topten' ); ?>
						</label>

						<div class="inner-wrapper date">
							<input type="date" name="cardDateStart" id="cardDateStart"/> - <input type="date" name="cardDateEnd" id="cardDateEnd"/>

							<button type="submit"
								class="searchTrigger"
								name="cardDateRange"
								id="cardDateRange">
								<?php esc_html_e( 'Rajaa', 'topten' ); ?>
							</button>
						</div>
					</div>

					<ul class="keywords" id="selectedKeywords"></ul>
				</div>

				<div class="one-third">
					<label for="cardMunicipality">
						<?php esc_html_e( 'Suodata kortteja kunnan mukaan', 'topten' ); ?>
					</label>

					<div class="inner-wrapper">
						<input type="text"
							name="cardMunicipality"
							id="cardMunicipality"
							placeholder="<?php esc_html_e( 'Kirjoita kuntasi nimi tähän', 'topten' ); ?>" />

						<button type="submit"
							class="searchTrigger"
							name="municipalitySearch"
							id="municipalitySearch" role="search">
							<?php esc_html_e( 'Lisää', 'topten' ); ?>
						</button>
					</div>

					<small class="small">
						<?php esc_html_e( 'Erota kunnat pilkulla', 'topten' ); ?>
					</small>

					<ul class="keywords" id="selectedMunicipalities"></ul>

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

					<ul class="keywords" id="selectedMunicipalities"></ul>
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
								<option value="" disabled selected>
									<?php esc_html_e( 'Valitse', 'topten' ); ?>
								</option>

								<?php foreach ( $categories as $category ) : ?>
									<option value="<?php echo esc_attr( $term->term_id ); ?>">
									<?php echo esc_attr( $term->name ); ?>
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

						<select class="filterTrigger" name="filterOrder" id="filterOrder">
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

							<li class="parent">
								<p class="name">
									<?php echo esc_html( $term->name ); ?>
								</p>

								<ul class="children">
									<?php foreach ( $children as $child ) : ?>
										<?php $child = get_term( $child ); ?>

										<li class="child">
											<p class="name">
												<?php echo esc_html( $child->name ); ?>
											</p>

											<ul class="grandchildren">
												<?php foreach ( $cards as $card ) : ?>
													<?php
													if ( 'tulkintakortti' !== get_post_type( $card->ID ) || ! is_object_in_term( $card->ID, 'laki', $child->name ) ) :
														continue;
													endif;

													$id               = $card->ID;
													$identifier_start = get_field( 'identifier_start', $id );
													$identifier_end   = get_field( 'identifier_end', $id );
													$title            = $card->post_title;
													$type             = get_post_type( $id );
													$version          = get_field( 'version', $id );
													$modified         = date( 'j.n.Y', strtotime( $card->post_modified ) );
													$link             = get_permalink( $id );
													?>
													<li class="card">
														<div class="ident">
															<span class="start">
																<?php echo esc_html( $identifier_start ); ?>
															</span>

															<span class="end">
																<?php echo esc_html( $identifier_end ); ?>
															</span>
														</div>

														<span class="version">
															<?php echo esc_html( $version ); ?>
														</span>

														<span class="card-title">
															<?php echo esc_html( $title ); ?>
														</span>

														<div class="languages">
															<a href="" class="fi">
																Fi
															</a>

															<a href="" class="se">
																Se
															</a>
														</div>

														<div class="buttons">
															<a class="button"
																href="<?php echo esc_url( $link ); ?>">
																<?php esc_html_e( 'Siirry korttiin', 'topten' ); ?>
															</a>
														</div>
													</li>
												<?php endforeach; ?>
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
							<li class="parent">
								<p class="name">
									<?php echo esc_html( $term->name ); ?>
								</p>

								<ul class="children">
									<?php
									foreach ( $cards as $card ) :
										if ( 'ohjekortti' !== get_post_type( $card->ID ) || ! is_object_in_term( $card->ID, 'ohje', $term->name ) ) :
											continue;
										endif;

										$id               = $card->ID;
										$identifier_start = get_field( 'identifier_start', $id );
										$identifier_end   = get_field( 'identifier_end', $id );
										$title            = $card->post_title;
										$type             = get_post_type( $id );
										$version          = get_field( 'version', $id );
										$modified         = date( 'j.n.Y', strtotime( $card->post_modified ) );
										$link             = esc_url( get_permalink( $id ) );
										?>

										<li class="card">
											<div class="ident">
												<span class="start">
													<?php echo esc_html( $identifier_start ); ?>
												</span>

												<span class="end">
													<?php echo esc_html( $identifier_end ); ?>
												</span>
											</div>

											<span class="version">
												<?php echo esc_html( $version ); ?>
											</span>

											<span class="card-title">
												<?php echo esc_html( $title ); ?>
											</span>

											<div class="languages">
												<a href="" class="fi">
													Fi
												</a>

												<a href="" class="se">
													Se
												</a>
											</div>

											<div class="buttons">
												<a class="button"
													href="<?php echo esc_url( $link ); ?>">
													<?php esc_html_e( 'Siirry korttiin', 'topten' ); ?>
												</a>
											</div>
										</li>
									<?php endforeach; ?>
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
							<li class="parent">
								<p class="name">
									<?php echo esc_html( $term->name ); ?>
								</p>

								<ul class="children">
									<?php
									foreach ( $cards as $card ) :
										if ( 'lomakekortti' !== get_post_type( $card->ID ) || ! is_object_in_term( $card->ID, 'lomake', $term->name ) ) :
											continue;
										endif;

										$id               = $card->ID;
										$identifier_start = get_field( 'identifier_start', $id );
										$identifier_end   = get_field( 'identifier_end', $id );
										$title            = $card->post_title;
										$type             = get_post_type( $id );
										$version          = get_field( 'version', $id );
										$modified         = date( 'j.n.Y', strtotime( $card->post_modified ) );
										$link             = get_permalink( $id );
										?>

										<li class="card">
											<div class="ident">
												<span class="start">
													<?php echo esc_html( $identifier_start ); ?>
												</span>

												<span class="end">
													<?php echo esc_html( $identifier_end ); ?>
												</span>
											</div>

											<span class="version">
												<?php echo esc_html( $version ); ?>
											</span>

											<span class="card-title">
												<?php echo esc_html( $title ); ?>
											</span>

											<div class="languages">
												<a href="" class="fi">
													Fi
												</a>

												<a href="" class="se">
													Se
												</a>
											</div>

											<div class="buttons">
												<a class="button"
													href="<?php echo esc_url( $link ); ?>">
													<?php esc_html_e( 'Siirry korttiin', 'topten' ); ?>
												</a>
											</div>
										</li>
									<?php endforeach; ?>
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
