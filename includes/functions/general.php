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
 * @param WP_Post $card Kortti
 */
function topten_get_card( $card ) {
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
			<a href="" class="fi">Fi</a>
			<a href="" class="se">Se</a>
		</div>

		<div class="buttons">
			<a class="button" href="<?php echo esc_url( $link ); ?>">
				<?php esc_html_e( 'Siirry korttiin', 'topten' ); ?>
			</a>
		</div>
	</li>
	<?php
}
