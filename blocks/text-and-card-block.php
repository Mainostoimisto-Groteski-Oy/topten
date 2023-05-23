<section <?php topten_block_id(); ?> class="text-and-card-block">
	<div class="grid">
		<div class="text-container">
			<?php topten_block_title(); ?>

			<?php if ( get_field( 'ingress' ) ) : ?>
				<div class="ingress">
					<?php the_field( 'ingress' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( get_field( 'text' ) ) : ?>
				<div class="text">
					<?php the_field( 'text' ); ?>
				</div>
			<?php endif; ?>

			<?php topten_buttons(); ?>
		</div>

		<div class="card">
			<?php
			// Jos kortti on asetettu automaattisesti haettavaksi, tehdään query, muussa tapauksessa haetaan artikkeliolio
			if ( get_field( 'automatic_card' ) ) :
				$args = array(
					'post_type'      => array( 'ohjekortti', 'tulkintakortti', 'lomakekortti' ),
					'posts_per_page' => 1,
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
				);
				$card = get_posts( $args );
			else :
				$card = get_field( 'choose_card' );
			endif;
			?>
			<div class="card-container">
				<?php
				// Poimitaan oliosta tarvittavat tiedot
				// Tässä tapauksessa haetaan vain yksi kortti joten se on aina 0
				$card             = $card[0];
				$id               = $card->ID;
				$identifier_start = get_field( 'identifier_start', $id );
				$identifier_end   = get_field( 'identifier_end', $id );
				$title            = $card->post_title;
				$type             = get_post_type( $id );
				$version          = get_field( 'version', $id );
				$post_date         = date( 'j.n.Y', strtotime( $card->post_date ) );
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
					<?php echo esc_html( $modified ); ?>
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
		</div>
	</div>
</section>
