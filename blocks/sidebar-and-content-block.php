<?php
global $post;

// Lohkot, joita halutaan käyttää content alueella (75% leveys gridistä)
$allowed_blocks = array(
	'acf/teksti',
	'acf/kaksi-saraketta',
	'acf/kolme-saraketta',
	'acf/teksti-ja-kuva',
	'acf/artikkelit',
	'acf/upotus',
	'acf/nosto',
	'acf/logot',
	'acf/yhteystiedot',
	'acf/painikkeet',
);
?>
<section <?php topten_block_id(); ?> class="sidebar-and-content-block">
	<div class="grid sidebar-and-content">
		<div class="sidebar">
			<span class="sidebar-title">
				<?php the_title(); ?>
			</span>

			<ul class="sidebar-navigation">
				<?php
				if ( $post->post_parent ) :
					$ancestors = get_post_ancestors( $post->ID );
					$root      = count( $ancestors ) - 1;
					$parent    = $ancestors[ $root ];
				else :
					$parent = $post->ID;
				endif;

				wp_list_pages(
					array(
						'title_li' => '',
						'child_of' => $parent,
					)
				);
				?>
			</ul>

		</div>

		<div class="content">
			<?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />'; ?>
		</div>
	</div>
</section>
