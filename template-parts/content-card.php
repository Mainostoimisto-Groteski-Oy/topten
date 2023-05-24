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
$post_date        = date( 'j.n.Y', strtotime( $post->post_date ) );
$keywords         = get_the_terms( $id, 'asiasanat' );
$full_name = $identifier_start.' '.$identifier_end.' '.$version.' '.get_the_title($id);
$status = get_field('card_status_publish', $id);

if(is_array($status)) {
	if (in_array('valid', $status) || in_array('approved_for_repeal' === $status)) {
		$status = 'valid';
		$target_url = home_url().'/rakentamisen-yhteiset-kaytannot/';
		$insert = 'Rakentamisen yhteiset käytännöt';
	} else if (in_array('expired', $status) || in_array('repealed' === $status)) {
		$status = 'past';
		$target_url = home_url().'/arkisto/';
		$insert = 'Arkisto';
	} else if (in_array('future' === $status)) {
		$status = 'future';
		$target_url = home_url().'/rakentamislaki-2025/';
		$insert = 'Rakentamislaki 2025';
	} else {
		$status = '';
		$target_url = '';
		$insert = '';
	}
} else {
	$status = '';
	$target_url = '';
	$insert = '';
}
	

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
<?php

?>
<div class="grid sidebar-grid">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-status="<?php echo esc_attr($status); ?>">
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

			
		</div>

		<button type="button" class="button inverted save-as-pdf" data-type="<?php echo esc_attr( $type ); ?>">
			<?php esc_html_e( 'Tulosta kortti', 'topten' ); ?>
		</button>
	</article>

	<aside class="sidebar">
		<div class="box open">
			<div class="box-title">
				<h3 class="h2">
					<?php esc_html_e('Sisällysluettelo', 'topten'); ?>
				</h3>
				<button class="material-icons" aria-expanded="true">double_arrow</button>
			</div>
			<div class="box-content" aria-expanded="true">
				<?php topten_get_table_of_contents(); ?>
			</div>
		</div>
		<?php if ( ! empty( $keywords ) ) : // Kortin asiasanat ?>
		<div class="box">
			<div class="box-title">
				<h3 class="h2">
					<?php esc_html_e('Asiasanat', 'topten'); ?>
				</h3>
				<button class="material-icons" aria-expanded="false">double_arrow</button>
			</div>
			<div class="box-content">
				<ul class="keywords" aria-expanded="false">
					<?php $keywords_count = count( $keywords ) - 1; ?>

					<?php foreach ( $keywords as $index => $keyword ) : 
						$redirect_url = $target_url.'?keyword='.$keyword->term_id;
						?>
						<li class="keyword">
							<a class="name" href="<?php echo esc_url($redirect_url); ?>"><span><?php echo esc_html( $keyword->name ); ?></span></a>
						
							<?php if( get_field('link', $keyword->taxonomy. '_' .$keyword->term_id ) ) : ?>
								<a href="<?php echo esc_url( get_field('link', $keyword->taxonomy. '_' .$keyword->term_id ) ); ?>" class="keyword-link material-icons" target="_blank" rel="noopener noreferrer">
									info
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		<div class="box">
			<div class="box-title">
				<h3 class="h2">
					<?php esc_html_e( 'Palaute', 'topten' ); ?>
				</h3>
				<button class="material-icons" aria-expanded="false">double_arrow</button>
			</div>
			<div class="box-content">
				<?php echo do_shortcode('[gravityform id="2" field_values="card_title='.esc_html($full_name).'" title="false" description="false" ajax="true"]'); ?>
			</div>
		</div>
		
	</aside>
</div>
