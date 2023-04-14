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
?>

	<main id="primary" class="site-main">
		<?php
		if ( ! is_front_page() ) :
			?>
			<section class="page-title">
				<div class="grid">
					<?php the_title( '<h1 class="entry-title h3">', '</h1>' ); ?>
				</div>
			</section>

			<?php
			// Yoast SEO pluginin tarjoama murupolku, tarkistetaan ensin että plugin on päällä function_exists -funktiolla
			if ( function_exists( 'yoast_breadcrumb' ) ) :
				?>
				<section class="page-breadcrumbs">
					<div class="grid">
						<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
					</div>
				</section>
			<?php endif; ?>
		<?php endif; ?>
		<section class="cards">
			<div class="grid">
				<div class="search" id="searchCards">
					<div class="full">
						<div class="input-wrapper keywords">
							<label for="cardKeywords">Suodata kortteja asiasanan mukaan</label>
							<div class="inner-wrapper">
								<input type="text" name="cardKeywords" id="cardKeywords" placeholder="Alkaa kirjaimilla.."/><button type="submit" name="keywordSearch" role="search">Hae asiasanalla</button>
							</div>
						</div>
						<div class="keywords" id="selectedKeywords">

						</div>
					</div>
					<div class="one-third">
						<label for="cardMunicipality">Suodata kortteja kunnan mukaan</label>
						<div class="inner-wrapper">
							<input type="text" name="cardMunicipality" id="cardMunicipality" placeholder="Kirjoita kuntasi nimi tähän"/><button type="submit" name="municipalitySearch" role="search">Lisää</button>
						</div>
						<label>Erota kunnat pilkulla</label>
						<div class="keywords" id="selectedMunicipalities">


						</div>
					</div>
					<div class="one-third">
						<label for="cardLaw">Suodata kortteja pykälän mukaan</label>
						<div class="inner-wrapper">
							<?php $terms = get_terms( 'laki', array( 'hide_empty' => false ) ); ?>
							<select name="cardLaw" id="cardLaw">
								<?php foreach ( $terms as $term ) : ?>
									<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="one-third">
						<label for="cardCategory">Suodata kortteja luokan mukaan</label>
						<div class="inner-wrapper">
							<?php
							$terms1 = get_terms( 'ohje', array( 'hide_empty' => false ) );
							$terms2 = get_terms( 'lomake', array( 'hide_empty' => false ) );
							if ( ! empty( $terms1 ) && ! empty( $terms2 ) ) {
								$terms = array_merge( $terms1, $terms2 );
							}
							?>
							<select name="cardCategory" id="cardCategory">
								<?php foreach ( $terms as $term ) : ?>
									<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="filters" id="filterCards">
					<div class="half">
						<div class="input-wrapper">
							<label for="filterOrder">Järjestä korttiluettelo</label>
							<select name="filterOrder" id="filterOrder">
								<option value="identifier">Tunnisteen mukaan</option>
								<option value="pubDate">Julkaisuajankohdan mukaan</option>
								<option value="alphabetical">Aakkosjärjestys</option>
							</select>
						</div>
					</div>
					<div class="half">
						<div class="input-wrapper horizontal">
							<label for="filterType">Näytä vain</label>
							<div class="inner-wrapper">
								<input type="checkbox" name="cardTulkinta" id="cardTulkinta" checked />
								<span class="post-type">Tulkintakortit</span>
							</div>
							<div class="inner-wrapper">
								<input type="checkbox" name="cardOhje" id="cardOhje" checked />
								<span class="post-type">Ohjekortit</span>
							</div>
							<div class="inner-wrapper">
								<input type="checkbox" name="cardLomake" id="cardLomake" checked />
								<span class="post-type">Lomakekortit</span>
							</div>
						</div>
					</div>
				</div>
				<div class="list" id="listCards">
					<div class="cardlist" id="tulkintakortit">
						<h2 class="h4 title">Tulkintakortit</h2>
						<?php $terms = get_terms( 'laki', array( 'hide_empty' => false ) ); ?>
						<ul class="cards">
							<?php
							foreach ( $terms as $term ) :
								if ( $term->parent === 0 ) :
									$children = get_term_children( $term->term_id, 'laki' );
									?>
									<li class="parent"> <span class="name"><?php echo esc_html( $term->name ); ?> </span>
									<ul class="children">
									<?php
									foreach ( $children as $child ) :
										$child = get_term( $child );
										?>

										<li class="child"> <span class="name"><?php echo esc_html( $child->name ); ?> </span>
											<ul class="grandchildren">
											<?php
											foreach ( $cards as $card ) :
												if ( 'tulkintakortti' === get_post_type( $card->ID ) && is_object_in_term( $card->ID, 'laki', $child->name ) ) :
													$id               = $card->ID;
													$identifier_start = esc_html( get_field( 'identifier_start', $id ) );
													$identifier_end   = esc_html( get_field( 'identifier_end', $id ) );
													$title            = esc_html( $card->post_title );
													$type             = get_post_type( $id );
													$version          = esc_html( get_field( 'version', $id ) );
													$modified         = date( 'j.n.Y', strtotime( esc_html( $card->post_modified ) ) );
													$link             = esc_url( get_permalink( $id ) );
													?>
													<li class="card">
														<div class="ident">
															<span class="start"><?php echo $identifier_start; ?></span>
															<span class="end"><?php echo $identifier_end; ?></span>
														</div>
														<span class="version">
															<?php echo $version; ?>
														</span>
														<span class="card-title">
															<?php echo $title; ?>
														</span>
														<div class="languages">
															<a href="" class="fi">Fi</a>
															<a href="" class="se">Se</a>
														</div>
														<div class="buttons">
															<a class="button" href="<?php echo $link; ?>"><?php esc_html_e( 'Siirry korttiin', 'topten' ); ?></a>
														</div>
													</li>
												<?php endif; ?>
											<?php endforeach; ?>
											</ul></li>
										<?php

									endforeach;
									?>
									</ul></li>
									<?php
								endif;
							endforeach;
							?>
						</ul>
					</div>
					<div class="cardlist" id="ohjekortit">
						<h2 class="h4 title">Ohjekortit</h2>
						<?php $terms = get_terms( 'ohje', array( 'hide_empty' => false ) ); ?>
						<ul class="cards">
							<?php
							foreach ( $terms as $term ) :
								?>
									<li class="parent"> <span class="name"><?php echo esc_html( $term->name ); ?> </span>
									<ul class="children">
										<?php
										foreach ( $cards as $card ) :
											if ( 'ohjekortti' === get_post_type( $card->ID ) && is_object_in_term( $card->ID, 'ohje', $term->name ) ) :
												$id               = $card->ID;
												$identifier_start = esc_html( get_field( 'identifier_start', $id ) );
												$identifier_end   = esc_html( get_field( 'identifier_end', $id ) );
												$title            = esc_html( $card->post_title );
												$type             = get_post_type( $id );
												$version          = esc_html( get_field( 'version', $id ) );
												$modified         = date( 'j.n.Y', strtotime( esc_html( $card->post_modified ) ) );
												$link             = esc_url( get_permalink( $id ) );
												?>
												<li class="card">
													<div class="ident">
														<span class="start"><?php echo $identifier_start; ?></span>
														<span class="end"><?php echo $identifier_end; ?></span>
													</div>
													<span class="version">
														<?php echo $version; ?>
													</span>
													<span class="card-title">
														<?php echo $title; ?>
													</span>
													<div class="languages">
														<a href="" class="fi">Fi</a>
														<a href="" class="se">Se</a>
													</div>
													<div class="buttons">
														<a class="button" href="<?php echo $link; ?>"><?php esc_html_e( 'Siirry korttiin', 'topten' ); ?></a>
													</div>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul></li>
								<?php
							endforeach;
							?>
						</ul>
					</div>
					<div class="cardlist" id="lomakekortit">
						<h2 class="h4 title">Lomakekortit</h2>
						<?php $terms = get_terms( 'lomake', array( 'hide_empty' => false ) ); ?>
						<ul class="cards">
							<?php
							foreach ( $terms as $term ) :
								?>
									<li class="parent"> <span class="name"><?php echo esc_html( $term->name ); ?> </span>
									<ul class="children">
										<?php
										foreach ( $cards as $card ) :
											if ( 'lomakekortti' === get_post_type( $card->ID ) && is_object_in_term( $card->ID, 'lomake', $term->name ) ) :
												$id               = $card->ID;
												$identifier_start = esc_html( get_field( 'identifier_start', $id ) );
												$identifier_end   = esc_html( get_field( 'identifier_end', $id ) );
												$title            = esc_html( $card->post_title );
												$type             = get_post_type( $id );
												$version          = esc_html( get_field( 'version', $id ) );
												$modified         = date( 'j.n.Y', strtotime( esc_html( $card->post_modified ) ) );
												$link             = esc_url( get_permalink( $id ) );
												?>
												<li class="card">
													<div class="ident">
														<span class="start"><?php echo $identifier_start; ?></span>
														<span class="end"><?php echo $identifier_end; ?></span>
													</div>
													<span class="version">
														<?php echo $version; ?>
													</span>
													<span class="card-title">
														<?php echo $title; ?>
													</span>
													<div class="languages">
														<a href="" class="fi">Fi</a>
														<a href="" class="se">Se</a>
													</div>
													<div class="buttons">
														<a class="button" href="<?php echo $link; ?>"><?php esc_html_e( 'Siirry korttiin', 'topten' ); ?></a>
													</div>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul></li>
								<?php
							endforeach;
							?>
						</ul>
					</div>
				</div>
			</div>
		</section>
		<?php the_content(); ?>
	</main>
<?php get_footer(); ?>


