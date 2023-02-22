<?php
/**
 * Template name: Unsubscribe
 *
 * Template for unsubscribing from automatic updates
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Topten
 */

// phpcs:disable WordPress.Security.NonceVerification.Recommended

get_header();
// Server secret key for hashing email addresses
$secret = 'dGA6jTPZk5D7e2Fg73kEW1V9Kyj867oJ';

?>

<main id="primary" class="site-main" data-template="<?php echo esc_html( get_field( 'card_status_type' ) ); ?>">

	<?php topten_breadcrumbs(); ?>

	<?php the_content(); ?>

	<section class="cards filters">
		<div class="grid">
			<h1 class="h2"><?php esc_html_e( 'Topten-infoviestit', 'topten' ); ?></h1>
			<?php
			// if token and email are set, and token matches hash of email, unsubscribe user
			if ( ( isset( $_GET['token'] ) && isset( $_GET['email'] ) ) && $_GET['token'] === hash_hmac( 'sha256', $_GET['email'], $secret ) ) { // phpcs:ignore

				// should probably be sanitized with sanitize_email() but I won't touch this
				$email = esc_html( $_GET['email'] ); // phpcs:ignore

				if ( have_rows( 'subscribers', 'options' ) ) {
					while ( have_rows( 'subscribers', 'options' ) ) {
						the_row();
						if ( get_sub_field( 'email', 'options' ) === $email ) {
							delete_row( 'subscribers', get_row_index(), 'option' );
							?>
							<p><?php esc_html_e( 'Olet poistanut itsesi Topten-infoviestien tilaajalistalta.', 'topten' ); ?></p>
							<?php
						}
					}
				}
			} elseif ( ! isset( $_GET['email'] ) ) {
				?>
				<p class="error"><?php esc_html_e( 'Sähköpostiosoitetta ei ole määritetty.', 'topten' ); ?></p>
				<?php
			} elseif ( ! isset( $_GET['token'] ) ) {
				?>
				<p class="error"><?php esc_html_e( 'Varmistustunnusta ei ole määritetty.', 'topten' ); ?></p>
				<?php
			} elseif ( ( isset( $_GET['token'] ) && isset( $_GET['email'] ) ) && $_GET['token'] !== hash_hmac( 'sha256', $_GET['email'], $secret ) ) { // phpcs:ignore
				?>
				<p class="error"><?php esc_html_e( 'Virheellinen varmistustunnus.', 'topten' ); ?></p>
				<?php
			} else {
				?>
				<p class="error"><?php esc_html_e( 'Sähköpostiosoitetta ei ole rekisteröity tilaajalistalle.', 'topten' ); ?></p>
				<?php
			}
			?>
			<p><a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Siirry etusivulle', 'topten' ); ?></a></p>
		</div>
	</section>

</main>

<?php
get_footer();
