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

$cardClasses = get_terms(
	'luokka',
	array(
		'hide_empty' => false,
	)
);

?>

<main id="primary" class="site-main" data-template="<?php echo esc_html( get_field( 'card_status_type' ) ); ?>">
	<?php topten_breadcrumbs(); ?>

	<?php topten_card_notification( 'archive' ); ?>

	<?php the_content(); ?>

	<section class="cards filters">
		<div class="grid top">
			<div class="title-wrapper">
				<h2 class="title h3"><?php esc_html_e( 'Suodata kortteja', 'topten' ); ?></h2><button id="toggleFilters" class="toggler" aria-expanded="false" aria-controls="searchAndFilters">keyboard_double_arrow_right</button>
			</div>

			<div class="content-area" id="searchAndFilters">
				<div class="search" id="searchCards" role="search">
					<div class="search-wrapper">
						<p id="error-message"></p>
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


								</div>

							</div>
						</div>

						<div class="full
						<?php
						if ( 'valid' !== get_field( 'card_status_type' ) ) :
							?>
							hidden
						<?php endif; ?> ">
							<div class="input-wrapper keywords">
								<label for="cardkeywords">
									<?php esc_html_e( 'Suodata kortteja asiasanan mukaan', 'topten' ); ?>
								</label>

								<div class="inner-wrapper">
									<input type="text"
										name="cardkeywords"
										id="cardkeywords"
										placeholder="<?php esc_html_e( 'Hae asiasanaa', 'topten' ); ?>" />
									<input type="hidden"
										name="cardkeywordsValue"
										id="cardkeywordsValue"
										/>
									<button type="submit"
										name="keywordssearch"
										id="keywordssearch" >
										<?php esc_html_e( 'Lisää asiasana', 'topten' ); ?>
									</button>
								</div>
							</div>
						</div>


						<div class="full">
							<div class="input-wrapper dateRange">
								<label for="cardDateStart">
									<?php esc_html_e( 'Näytä vain kortit jotka on laadittu', 'topten' ); ?>
								</label>

								<div class="inner-wrapper date">
									<input type="date" name="cardDateStart" id="cardDateStart"/> <span class="divider">-</span> <input type="date" name="cardDateEnd" id="cardDateEnd"/>
								</div>
								<small><?php esc_html_e( 'välisenä ajankohtana', 'topten' ); ?></small>
							</div>

						</div>

						<?php if ( $laws ) : ?>
							<div class="one-third">
								<label for="cardLaw">
									<?php esc_html_e( 'Suodata MRL:n lukujen ja pykälien mukaan aihepiireittäin', 'topten' ); ?>
									<?php esc_html_e( '(Huom: koskee toistaiseksi vain tulkintakortteja)', 'topten' ); ?>
								</label>

								<div class="inner-wrapper">
									<select name="cardLaw" id="cardLaw">
										<option value="" selected>
											<?php esc_html_e( 'Valitse', 'topten' ); ?>
										</option>

										<?php foreach ( $laws as $law ) : ?>
											<option value="<?php echo esc_attr( $law->term_id ); ?>" data-name="<?php echo esc_attr( $law->name ); ?>">
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
									<?php esc_html_e( 'Suodata kortteja suunnittelualan mukaan', 'topten' ); ?>
								</label>

								<div class="inner-wrapper">
									<select name="cardCategory" id="cardCategory">
										<option value="" selected>
											<?php esc_html_e( 'Valitse', 'topten' ); ?>
										</option>

										<?php foreach ( $categories as $category ) : ?>
											<option value="<?php echo esc_attr( $category->term_id ); ?>" data-name="<?php echo esc_attr( $category->name ); ?>">
											<?php echo esc_html( $category->name ); ?>
										</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $cardClasses ) : ?>
							<div class="full checkboxes" id="classCheckboxes">
								<p for="cardClass" class="label">
									<?php esc_html_e( 'Näytä kortit luokasta', 'topten' ); ?>
								</p>

									<?php foreach ( $cardClasses as $class ) : ?>
									<div class="input-wrapper horizontal">
										<div class="checkbox-wrapper">
											<label for="class-<?php echo esc_html( $class->slug ); ?>" class="inner-wrapper">
												<input class=""
													type="checkbox"
													name="cardclassfilter"
													data-name="<?php echo esc_attr( $class->name ); ?>"
													id="class-<?php echo esc_attr( $class->slug ); ?>"
													value="<?php echo esc_attr( $class->term_id ); ?>"
													checked />
												<span class="checkmark"></span>
											</label>
										</div>
										<span class="check" data-name="cardclassfilter" data-id="<?php echo esc_attr( $class->term_id ); ?>"><?php echo esc_html( $class->name ); ?></span>
									</div>
									<?php endforeach; ?>

							</div>
						<?php endif; ?>

						<div class="submit">
							<button type="submit"
								class="searchTrigger"
								name="textSearch"
								id="textSearch">
								<?php esc_html_e( 'Suodata hakutuloksia', 'topten' ); ?>
							</button>
						</div>
					</div> <!-- close wrapper -->
				</div>

					<div class="sidebar" id="cardSidebar">
						<h3 class="h4 title"><?php esc_html_e( 'Käytössä olevat suodattimet', 'topten' ); ?></h3>
						<figure>
							<figcaption class="small"><?php esc_html_e( 'Avoin tekstihaku', 'topten' ); ?></figcaption>
							<ul class="keywords" id="selectedFreeText"></ul>
						</figure>
						<figure>
							<figcaption class="small"><?php esc_html_e( 'Valitut asiasanat', 'topten' ); ?></figcaption>
							<ul class="keywords" id="selectedkeywords"></ul>
						</figure>
						<figure>
							<figcaption class="small"><?php esc_html_e( 'Näytä kortit joita koskee pykälä ', 'topten' ); ?></figcaption>
							<ul class="keywords" id="selectedLaw"></ul>
						</figure>
						<figure>
							<figcaption class="small"><?php esc_html_e( 'Kortit ajanjaksolta', 'topten' ); ?></figcaption>
							<ul class="keywords" id="selectedDateRange">
								<li id="selectedDateStart"></li>
								<li id="divider">-</li>
								<li id="selectedDateEnd"></li>
							</ul>
						</figure>
						<figure>
							<figcaption class="small"><?php esc_html_e( 'Valittu vastuuryhmä', 'topten' ); ?></figcaption>
							<ul class="keywords" id="selectedCategory"></ul>
						</figure>
						<figure class="classes">
							<figcaption class="small"><?php esc_html_e( 'Näytä kortit luokasta', 'topten' ); ?></figcaption>
							<ul class="keywords" id="selectedCardClasses"></ul>
						</figure>
						<button type="submit"
							class="resetFilters"
							name="resetFilters"
							id="resetFilters">

						<?php esc_html_e( 'Tyhjennä kaikki valinnat', 'topten' ); ?>
					</div>


			</div> <!-- end content area -->



		</div><!-- end top grid -->
	</section>
	<section class="cards list">
	<div id="ajaxOverlay"><div class="spinner"></div></div>
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
					<div class="input-wrapper">
						<p for="filterType" class="label">
							<?php esc_html_e( 'Näytä', 'topten' ); ?>
						</p>
						<div class="boxes">

							<div class="box-wrapper">
								<div class="checkbox-wrapper">
									<label for="cardTulkinta" class="inner-wrapper">
										<input class="filterTrigger"
											type="checkbox"
											name="cardTypeFilter"
											id="cardTulkinta"
											value="tulkintakortti"
											checked />
										<span class="checkmark"></span>
									</label>
								</div>
								<span class="check" data-name="cardTypeFilter" data-id="tulkintakortti"><?php esc_html_e( 'Tulkintakortit', 'topten' ); ?></span>
							</div>

							<div class="box-wrapper">
								<div class="checkbox-wrapper">
									<label for="cardOhje" class="inner-wrapper">
										<input class="filterTrigger"
											type="checkbox"
											name="cardTypeFilter"
											id="cardOhje"
											value="ohjekortti"
											checked />
										<span class="checkmark"></span>
									</label>
								</div>
								<span class="check" data-name="cardTypeFilter" data-id="ohjekortti"><?php esc_html_e( 'Ohjekortit', 'topten' ); ?></span>
							</div>

							<div class="box-wrapper">
								<div class="checkbox-wrapper">
									<label for="cardLomake" class="">
										<input class="filterTrigger"
											type="checkbox"
											name="cardTypeFilter"
											id="cardLomake"
											value="lomakekortti"
											checked />
										<span class="checkmark"></span>
									</label>
								</div>
								<span class="check" data-name="cardTypeFilter" data-id="lomakekortti"><?php esc_html_e( 'Lomakekortit', 'topten' ); ?></span>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="list" id="listCards">
				<div class="cardlist" id="tulkintakortit">

				</div>

				<div class="cardlist" id="ohjekortit">

				</div>

				<div class="cardlist" id="lomakekortit">

				</div>
			</div>

		</div>
	</section>

</main>

<?php
get_footer();
