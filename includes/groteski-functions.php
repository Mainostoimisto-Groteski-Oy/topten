<?php
/**
 * Luo blockin titlen blockin globaaleista ACF-kentistä
 *
 * @param boolean $echo Echotaanko title suoraan, default true
 */
function groteski_block_title( $echo = true, $id = '' ) {
	$title_text = get_field( 'block_title' );
	$title_tag = get_field( 'block_title_tag' );
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
		echo $title;
	} else {
		return $title;
	}
}

/**
 * Palauttaa $postin ensimmäisen 'text'-nimisen kentän ensimmäiset $words sanaa
 *
 * @param WP_Post|int $post Postiobjekti tai post ID
 * @param int $words Sanojen määrä, default 20
 */
function groteski_excerpt( $post, $words = 20 ) {
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
function groteski_block_id( $id = '' ) {
	$block_id = get_field( 'block_id' );
	$block_title = get_field( 'block_title' );

	if ( '' !== $block_id ) {
		$id .= '' . $block_id;
	}
	if ( $id ) {
		echo 'id="' . esc_attr( $id ) . '"';
	} else {
		echo 'id="' . sanitize_title( $block_title ) . '"';
	}
}

/**
 * Palauttaa blockin kuvan focal pointin
 *
 * @param boolean
 * @param array|boolean $block ACF ryhmä (image_block tms), default tyhjä array
 * @param boolean $echo Echotaanko focal point? Default true, falsella palauttaa stringinä ilman echoa
 */
function groteski_focal_point( $sub_field = false, $block = array(), $selector = 'focal_point' ) {
	if ( $block ) {
		if ( ! empty( $block[ $selector ] ) ) {
			$focal_point = $block[ $selector ];
		}
	} else if ( $sub_field ) {
		$focal_point = get_sub_field( $selector );
	} else {
		$focal_point = get_field( $selector );
	}

	$focal_point = $focal_point ? str_replace( '_', '-', $focal_point ) : 'center';

	if ( $focal_point ) {
		echo 'focal-' . $focal_point;
	}
}

/**
 * Tulostaa (echo) napit (rakenne: div.buttons -> a.button)
 * Funktio olettaa että repeater-kentän nimi on 'buttons' ja yksittäisen napin nimi on 'button'
 * Jos napit on lohkon sisällä (esim. $left_block, $right_block), niin silloin parametriksi tulee antaa kyseinen lohko
 *
 * Esim.
 * $left_block = get_field( 'left_block' );
 * groteski_buttons( $left_block );
 *
 * Muuten parametrin voi jättää tyhjäksi
 *
 * @param array|boolean $block ACF ryhmä (left_block tms), default tyhjä array
 */
function groteski_buttons( $block = array() ) {
	if ( $block ) {
		if ( ! empty( $block['buttons'] ) ) {
			echo '<div class="buttons">';

			foreach ( $block['buttons'] as $button ) {
				if ( ! empty( $button['button'] ) ) {
					$button = $button['button'];
					$href = esc_url( $button['url'] );
					$title = esc_attr( $button['title'] );
					$target = esc_attr( $button['target'] );

					echo sprintf( '<a class="button" href="%s" title="%s" target="%s">%s</a>', $href, $title, $target, $title );
				}
			}

			echo '</div>';
		}
	} else if ( have_rows( 'buttons' ) ) {
		echo '<div class="buttons">';

		while ( have_rows( 'buttons' ) ) {
			the_row();

			$button = get_sub_field( 'button' );

			if ( $button ) {
				$href = esc_url( $button['url'] );
				$title = esc_attr( $button['title'] );
				$target = esc_attr( $button['target'] );

				echo sprintf( '<a class="button" href="%s" title="%s" target="%s">%s</a>', $href, $title, $target, $title );
			}
		}

		echo '</div>';
	}

}
