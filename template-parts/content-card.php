<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

$id               = $post->ID;
$identifier_start = get_field( 'identifier_start', $id );
$identifier_end   = get_field( 'identifier_end', $id );
$type             = get_post_type( $id );
$version          = get_field( 'version', $id );
$modified         = date( 'j.n.Y', strtotime( $post->post_modified ) );
$keywords         = get_the_terms( $id, 'asiasanat' );
?>

<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
	<section class="page-breadcrumbs">
		<div class="grid">
			<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
		</div>
	</section>
<?php endif; ?>

<h1 class="screen-reader-text">
	<?php the_title(); ?>
</h1>

<div class="grid sidebar-grid">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="card-content">
			<section class="row-block top">
				<div class="grid">
					<div class="column date">
						<p class="small-title">
							<?php esc_html_e( 'Vahvistuspvm', 'topten' ); ?>

							<strong>
								<?php echo esc_html( $modified ); ?>
							</strong>
						</p>
					</div>

					<div class="column"></div>

					<div class="column identifier">
						<p class="small-title">
							<?php esc_html_e( 'Tunniste', 'topten' ); ?>

							<?php if ( 'tulkintakortti' === $type ) : ?>
								<strong>
									<?php echo esc_html( $identifier_start ); ?>

									<sup>
										<?php echo esc_html( $identifier_end ); ?>
									</sup>
								</strong>
							<?php else : ?>
								<strong>
									<?php echo esc_html( $identifier_start ); ?>

									<?php echo esc_html( $identifier_end ); ?>
								</strong>
							<?php endif; ?>
						</p>
					</div>

					<div class="column version">
						<p class="small-title">
							<?php echo esc_html_e( 'Muutos', 'topten' ); ?>

							<strong>
								<?php echo esc_html( $version ); ?>
							</strong>
						</p>
					</div>
				</div>
			</section>

			<?php the_content(); // Kortin sisältölohkot ?>

			<?php if ( ! empty( $keywords ) ) : // Kortin asiasanat ?>
				<section class="row-block">
					<div class="grid">
						<div class="column">
							<?php topten_get_desc( __( 'Asiasanat', 'topten' ) ); ?>

							<p class="keywords">
								<?php $keywords_count = count( $keywords ) - 1; ?>

								<?php foreach ( $keywords as $index => $keyword ) : ?>
									<?php if ( $index !== $keywords_count ) : ?>
										<?php echo esc_html( $keyword->name ) . ', '; ?>
									<?php else : ?>
										<?php echo esc_html( $keyword->name ); ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</p>
						</div>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<button type="button" class="save-as-pdf" data-type="<?php echo esc_attr( $type ); ?>">
			<?php esc_html_e( 'Tulosta sivu', 'topten' ); ?>
		</button>
	</article>

	<aside class="feedback">
		<?php topten_get_table_of_contents(); ?>

		<h2 class="h4 red">
			<strong>
				<?php esc_html_e( 'Palaute', 'topten' ); ?>
			</strong>
		</h2>

		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas placerat porttitor erat pharetra facilisis. Duis rutrum suscipit ex at sodales. Nullam mollis auctor justo sed accumsan. Nam ac metus feugiat, viverra est eget, tempus lectus.
		</p>
	</aside>
</div>
