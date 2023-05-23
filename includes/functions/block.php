<?php
/**
 * Luo blockin titlen blockin globaaleista ACF-kentistä
 *
 * @param boolean $echo Echotaanko title suoraan, default true
 * @param string  $id Titlelle annettava ID, default ''
 */
function topten_block_title( $echo = true, $id = '' ) {
	$title_text = get_field( 'block_title' );
	$title_tag  = get_field( 'block_title_tag' );
	$title_size = get_field( 'block_title_size' );

	if ( ! $title_text ) {
		return;
	}

	if ( ! $title_tag || 'string' !== gettype( $title_tag ) ) {
		$title_tag = 'h2';
	}

	if ( $id ) {
		$title_id = esc_attr( $id );

		$title = sprintf( '<%1$s id="%4$s" class="%2$s title">%3$s</%1$s>', $title_tag, $title_size, $title_text, $title_id );
	} else {
		$title = sprintf( '<%1$s class="%2$s title">%3$s</%1$s>', $title_tag, $title_size, $title_text );
	}

	if ( $echo ) {
		echo wp_kses_post( $title );
	} else {
		return $title;
	}
}

/**
 * Palauttaa $postin ensimmäisen 'text'-nimisen kentän ensimmäiset $words sanaa
 *
 * @param WP_Post|int $post Postiobjekti tai post ID
 * @param int         $words Sanojen määrä, default 20
 */
function topten_excerpt( $post, $words = 20 ) {
	$blocks = parse_blocks( get_the_content( null, false, $post ) );

	foreach ( $blocks as $block ) {
		if ( isset( $block['attrs']['data']['text'] ) && ! empty( $block['attrs']['data']['text'] ) ) {
			return wp_trim_words( $block['attrs']['data']['text'], $words );
		}
	}
}

/**
 * Palauttaa blockin IDn
 *
 * @param string $id Blockin id
 */
function topten_block_id( $id = '' ) {
	$block_id    = get_field( 'block_id' );
	$block_title = get_field( 'block_title' );

	if ( '' !== $block_id ) {
		$id .= '' . $block_id;
	}

	if ( ! $id ) {
		$id = esc_attr( sanitize_title( $block_title ) );
	}

	if ( $id ) {
		echo 'id="' . esc_attr( $id ) . '"';
	}
}

/**
 * Palauttaa blockin kuvan focal pointin
 *
 * @param boolean       $sub_field Onko kenttä repeater-kentän sisällä? Default false
 * @param array|boolean $block ACF ryhmä (image_block tms), default tyhjä array
 * @param string        $selector Kentän nimi, default 'focal_point'
 */
function topten_focal_point( $sub_field = false, $block = array(), $selector = 'focal_point' ) {
	if ( $block ) {
		if ( ! empty( $block[ $selector ] ) ) {
			$focal_point = $block[ $selector ];
		}
	} elseif ( $sub_field ) {
		$focal_point = get_sub_field( $selector );
	} else {
		$focal_point = get_field( $selector );
	}

	$focal_point = $focal_point ? str_replace( '_', '-', $focal_point ) : 'center';

	if ( $focal_point ) {
		echo esc_attr( 'focal-' . $focal_point );
	}
}

/**
 * Tulostaa (echo) napit (rakenne: div.buttons -> a.button)
 * Funktio olettaa että repeater-kentän nimi on 'buttons' ja yksittäisen napin nimi on 'button'
 * Jos napit on lohkon sisällä (esim. $left_block, $right_block), niin silloin parametriksi tulee antaa kyseinen lohko
 *
 * Esim.
 * $left_block = get_field( 'left_block' );
 * topten_buttons( $left_block );
 *
 * Muuten parametrin voi jättää tyhjäksi
 *
 * @param array|boolean $block ACF ryhmä (left_block tms), default tyhjä array
 */
function topten_buttons( $block = array(), $background = '') {
	if ( $block ) {
		if ( ! empty( $block['buttons'] ) ) {
			echo '<div class="buttons">';

			foreach ( $block['buttons'] as $button ) {
				if ( ! empty( $button['button'] ) ) {
					$button = $button['button'];
					$href   = $button['url'];
					$title  = $button['title'];
					$target = $button['target'];
					
					echo sprintf( '<a class="button background-%s" href="%s" title="%s" target="%s">%s</a>', esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
				}
			}

			echo '</div>';
		}
	} elseif ( have_rows( 'buttons' ) ) {
		echo '<div class="buttons">';

		while ( have_rows( 'buttons' ) ) {
			the_row();

			$button = get_sub_field( 'button' );

			if ( $button ) {
				$href   = esc_url( $button['url'] );
				$title  = esc_attr( $button['title'] );
				$target = esc_attr( $button['target'] );

				echo sprintf( '<a class="button background-%s" href="%s" title="%s" target="%s">%s</a>', esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
			}
		}

		echo '</div>';
	}
}

/**
 * Luo sisällysluettelon
 */
function topten_get_table_of_contents() {
	$blocks = parse_blocks( get_the_content() );

	$table_of_contents = '<ol class="table-of-contents">';

	// Rivit
	foreach ( $blocks as $row ) {
		if ( ! empty( $row['innerBlocks'] ) ) {

			// Sarakkeet
			foreach ( $row['innerBlocks'] as $column ) {
				if ( ! empty( $column['innerBlocks'] ) ) {

					// Sarakkeen lohkot
					foreach ( $column['innerBlocks'] as $block ) {
						if ( ! empty( $block['attrs']['data']['description'] ) ) {
							$title = $block['attrs']['data']['description'];
							$id    = sanitize_title( $block['attrs']['data']['description'] );
							$href  = sprintf( '#%s', $id );

							$link = sprintf( '<a href="%s" aria-label="%s">%s</a>', $href, $title, $title );

							$table_of_contents .= sprintf( '<li>%s</li>', $link );
						}
					}
				}
			}
		}
	}

	$table_of_contents .= '</ol>';

	echo wp_kses_post( $table_of_contents );
}

/**
 * Hakee pikkuotsikon
 *
 * @param string $description Pikkuotsikko, default get_field('description')
 */
function topten_get_desc( $description = false ) {
	if ( ! $description ) {
		$description = get_field( 'description' );
	}

	if ( $description ) {
		$id = sanitize_title( $description );

		echo sprintf( '<h2 id="%s" class="desc">%s</h2>', esc_attr( $id ), esc_html( $description ) );
	}
}
