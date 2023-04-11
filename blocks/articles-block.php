<?php
$articles_type = get_field( 'articles_type' );
$type = get_field( 'type' );
if($type === 'card') {
	$post_type = array('tulkintakortti', 'ohjekortti', 'lomakekortti');
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

	$postslist = get_posts( $args );
} else {
	$postslist = get_field( 'articles' );
}
?>

<section <?php topten_block_id(); ?> class="articles-block <?php the_field('background'); ?>">
	<div class="grid">
		<div class="text-block">
			<?php topten_block_title(); ?>

			<?php if ( get_field( 'text' ) ) : ?>
				<?php the_field( 'text' ); ?>
			<?php endif; ?>
		</div>

		<div class="post-wrapper">
			<?php if ( $postslist && $type === 'card') : 
				
				?>
				<?php foreach ( $postslist as $post ) : ?>
					<?php setup_postdata( $post ); ?>
						<div class="card-container">
							<?php  
							// Poimitaan oliosta tarvittavat tiedot
							$id = $post->ID;
							$identifier_start = esc_html(get_field('identifier_start', $id));
							$identifier_end = esc_html(get_field('identifier_end', $id));
							$title = esc_html($post->post_title);
							$type = get_post_type($id);
							$version = esc_html(get_field('version', $id));
							$modified = date('j.n.Y', strtotime(esc_html($post->post_modified)));
							$link = esc_url(get_permalink($id));
							$summary = get_field('edit_summary', $id);
							?>
							<span class="type"><?php echo $type; ?></span>
							<div class="top">
								<div class="identifier">
									<span class="start"><?php echo $identifier_start; ?></span>
									<span class="end"><?php echo $identifier_end; ?></span>
								</div>
								<span class="version"><?php echo $version; ?></span>
							</div>
							<h2 class="title h4"><?php echo $title; ?></h2>
							<span class="modified"><?php echo $modified; ?></span>
							<div class="buttons">
								<a class="button" href="<?php echo $link; ?>"><?php esc_html_e( 'Siirry korttiin', 'topten' ); ?></a>
							</div>
							<div class="bottom">
								<p><?php echo $summary; ?></p>
							</div>

						</div>
				<?php endforeach; ?>

				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( $postslist as $post ) : ?>
					<?php setup_postdata( $post ); ?>
						<div class="single-post" id="post-<?php echo esc_attr( $post->ID ); ?>">
						
						<div class="image">
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'medium' ) ); ?>" alt=""/>
						</div>

						<div class="content">
							<div class="date">
								<?php echo esc_html( date( 'd.m.Y', strtotime( $post->post_date ) ) ); ?>
							</div>

							<h4 class="title h4">
								<?php echo esc_html( get_the_title( $post->ID ) ); ?>
							</h4>

							<div class="text">
								<?php echo wp_kses_post( get_the_excerpt( $post->ID ) ); ?>
							</div>

							<div class="buttons">
								<a class="button" href="<?php the_permalink( $post->ID ); ?>">
									<span class="button-text">
										<?php esc_html_e( 'Lue koko juttu', 'topten' ); ?>
									</span>
								</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
		<?php topten_buttons(); ?>
	</div>
</section>
