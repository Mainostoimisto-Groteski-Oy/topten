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
            if(get_field('automatic_card')) : ?>
                <?php 
                $args = array(
                    'post_type'	=> array('ohjekortti', 'tulkintakortti', 'lomakekortti'),
                    'posts_per_page'	=> 1,
                    'post_status'    => 'publish',
                );
                $card = get_posts($args);
                ?>
                
            <?php else : ?>
                <?php
                $card = get_field( 'choose_card' );
                ?>
            <?php endif; ?>
            <div class="card-container">
                <?php  
                // Poimitaan oliosta tarvittavat tiedot
                // Tässä tapauksessa haetaan vain yksi kortti joten se on aina 0
                $card = $card[0];
                $id = $card->ID;
                $identifier_start = esc_html(get_field('identifier_start', $id));
                $identifier_end = esc_html(get_field('identifier_end', $id));
                $title = esc_html($card->post_title);
                $type = get_post_type($id);
                $version = esc_html(get_field('version', $id));
                $modified = date('j.n.Y', strtotime(esc_html($card->post_modified)));
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
        </div>
	</div>
</section>
