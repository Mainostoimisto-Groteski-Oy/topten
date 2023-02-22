<?php
$articles_type = get_field( 'articles_type' );
$type          = get_field( 'type' );
if ( $type === 'card' ) {
	$post_type = array( 'tulkintakortti', 'ohjekortti', 'lomakekortti' );
} else {
	$post_type = 'post';
}

if ( 'newest' === $articles_type ) {
	if ( get_field( 'show_all' ) ) {
		$posts_per_page = -1;
	} else {
		$posts_per_page = intval( get_field( 'posts_per_page' ) );
	}

	$taxonomy = get_field( 'taxonomy' );

	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => $posts_per_page,
	);

	if ( $taxonomy ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $taxonomy,
			),
		);
	}

	if ( $post_type !== 'post' ) {
		$args['meta_query'] = array(
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
		);
	}

	$postslist = get_posts( $args );
} else {
	$postslist = get_field( 'articles' );
}
?>

<section <?php topten_block_id(); ?> class="articles-block <?php the_field( 'background' ); ?>">
	<div class="grid">
		<div class="text-block">
			<?php topten_block_title(); ?>

			<?php if ( get_field( 'text' ) ) : ?>
				<?php the_field( 'text' ); ?>
			<?php endif; ?>
		</div>

		<div class="post-wrapper">
			<?php
			if ( $postslist && $type === 'card' ) :

				?>
				<?php foreach ( $postslist as $post ) : ?>
					<?php setup_postdata( $post ); ?>

					<div class="card-container">
						<?php
						// Poimitaan oliosta tarvittavat tiedot
						$id               = $post->ID;
						$identifier_start = get_field( 'identifier_start', $id );
						$identifier_end   = get_field( 'identifier_end', $id );
						$title            = $post->post_title;
						$type             = get_post_type( $id );
						$version          = get_field( 'version', $id );
						$post_date        = date( 'j.n.Y', strtotime( $post->post_date ) );
						$link             = get_permalink( $id );
						$summary          = get_field( 'edit_summary', $id );
						?>

						<span class="type">
							<?php echo esc_html( $type ); ?>
						</span>

						<div class="top">
							<div class="identifier">
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
						</div>

						<h2 class="title h4">
							<?php echo esc_html( $title ); ?>
						</h2>

						<span class="modified">
							<?php echo esc_html( $post_date ); ?>
						</span>

						<div class="buttons">
							<a class="button" href="<?php echo esc_url( $link ); ?>">
								<?php esc_html_e( 'Siirry kortille', 'topten' ); ?>
							</a>
						</div>

						<div class="bottom">
							<p>
								<?php echo esc_html( $summary ); ?>
							</p>
						</div>
					</div>
				<?php endforeach; ?>

				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( $postslist as $post ) : ?>
					<?php
					setup_postdata( $post );
					if ( get_the_post_thumbnail_url( $post->ID, 'medium' ) ) {
						$src      = get_the_post_thumbnail_url( $post->ID, 'medium' );
						$image_id = get_post_thumbnail_id( $post->ID );
						$class    = '';
						$alt      = get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?? '';
					} else {
						$src   = get_template_directory_uri() . '/assets/dist/images/topten-placeholder-small.png';
						$class = 'placeholder';
						$alt   = '';
					}
					?>
						<div class="single-post" id="post-<?php echo esc_attr( $post->ID ); ?>">
						<?php if ( get_field( 'article_image_icon', $post->ID ) ) : ?>

							<div class="image icon">
								<div class="icon-wrapper">
								<img src="<?php echo esc_url( $src ); ?>" alt=""/>
								</div>
							</div>

						<?php else : ?>

							<div class="image <?php echo esc_attr( $class ); ?>">
								<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>"/>
							</div>

						<?php endif; ?>

						<div class="content">
							<p class="date">
								<?php echo esc_html( date( 'd.m.Y', strtotime( $post->post_date ) ) ); ?>
							</p>

							<h3 class="title h4">
								<?php echo esc_html( get_the_title( $post->ID ) ); ?>
							</h3>

							<p class="text">
								<?php echo wp_kses_post( get_the_excerpt( $post->ID ) ); ?>
							</p>

							<a class="link" href="<?php the_permalink( $post->ID ); ?>">
								<span class="link-text"><?php esc_html_e( 'Lue koko juttu', 'topten' ); ?></span>
								<span class="material-symbols" aria-hidden="true">keyboard_double_arrow_right</span>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
		<?php topten_buttons(); ?>
	</div>
</section>
