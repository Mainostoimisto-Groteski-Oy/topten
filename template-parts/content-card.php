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

<!-- todo -->
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
						</p>

						<p class="strong">
							<?php echo esc_html( $modified ); ?>
						</p>
					</div>

					<div class="column"></div>

					<div class="column identifier">
						<p class="small-title">
							<?php esc_html_e( 'Tunniste', 'topten' ); ?>
						</p>

						<?php if ( 'tulkintakortti' === $type ) : ?>
							<p class="identifier-text">
								<?php echo esc_html( $identifier_start ); ?>

								<sup>
									<?php echo esc_html( $identifier_end ); ?>
								</sup>
							</p>
						<?php else : ?>
							<p class="identifier-text">
								<?php echo esc_html( $identifier_start ); ?>

								<?php echo esc_html( $identifier_end ); ?>
							</p>
						<?php endif; ?>
					</div>

					<div class="column version">
						<p class="small-title">
							<?php echo esc_html_e( 'Muutos', 'topten' ); ?>
						</p>

						<p class="version-text">
							<?php echo esc_html( $version ); ?>
						</p>
					</div>
				</div>
			</section>

			<?php the_content(); // Kortin sisältölohkot ?>

			<?php if ( ! empty( $keywords ) ) : // Kortin asiasanat ?>
				<section class="row-block">
					<div class="grid">
						<div class="column">
							<em class="desc">
								<?php esc_html_e( 'Asiasanat', 'topten' ); ?>
							</em>

							<ul class="keywords">
								<?php $keywords_count = count( $keywords ) - 1; ?>

								<?php foreach ( $keywords as $index => $keyword ) : ?>
									<li class="keyword">
										<?php if ( $index !== $keywords_count ) : ?>
											<?php echo esc_html( $term->name ) . ', '; ?>
										<?php else : ?>
											<?php echo esc_html( $term->name ); ?>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
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
		<span class="h4 red">
			<strong>
				<?php esc_html_e( 'Palaute', 'topten' ); ?>
			</strong>
		</span>

		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas placerat porttitor erat pharetra facilisis. Duis rutrum suscipit ex at sodales. Nullam mollis auctor justo sed accumsan. Nam ac metus feugiat, viverra est eget, tempus lectus.
		</p>
	</aside>
</div>
