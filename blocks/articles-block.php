<?php
$type      = get_field( 'articles_type' );
$post_type = get_field( 'post_type' );

if ( 'newest' === $type ) {
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

<section <?php groteski_block_id(); ?> class="articles-block">
	<div class="grid">
		<div class="text-block">
			<?php groteski_block_title(); ?>

			<?php if ( get_field( 'text' ) ) : ?>
				<?php the_field( 'text' ); ?>
			<?php endif; ?>
		</div>

		<div class="post-wrapper">
			<?php if ( $postslist ) : ?>
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
										<?php esc_html_e( 'Lue lisää', 'groteski' ); ?>
									</span>
								</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
		<?php groteski_buttons(); ?>
	</div>
</section>
