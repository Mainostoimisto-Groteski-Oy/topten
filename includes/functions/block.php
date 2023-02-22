<?php
/**
 * Creates a block title
 *
 * @package Topten\Blocks
 *
 * @since 1.0.0
 *
 * @param bool   $echo Echo?
 * @param string $id Title ID
 *
 * @return string Title
 */
function topten_block_title( $echo = true, $id = '' ) {
	$title_text = get_field( 'block_title' );
	$title_tag  = get_field( 'block_title_tag' );
	$title_size = get_field( 'block_title_size' );
	$hide_title = get_field( 'hide_title' );
	if ( $hide_title ) {
		$hide_title = 'hide';
	} else {
		$hide_title = '';
	}

	if ( ! $title_text ) {
		return;
	}

	if ( ! $title_tag || 'string' !== gettype( $title_tag ) ) {
		$title_tag = 'h2';
	}

	if ( $id ) {
		$title_id = esc_attr( $id );

		$title = sprintf( '<%1$s id="%4$s" class="%2$s %5$s title">%3$s</%1$s>', $title_tag, $title_size, $title_text, $title_id, $hide_title );
	} else {
		$title = sprintf( '<%1$s class="%2$s %4$s title">%3$s</%1$s>', $title_tag, $title_size, $title_text, $hide_title );
	}

	if ( $echo ) {
		echo wp_kses_post( $title );
	} else {
		return $title;
	}
}

/**
 * Returns block ID
 *
 * @package Topten\Blocks
 *
 * @since 1.0.0
 *
 * @param string $id Block ID
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
 * Returns focal point of the image
 *
 * @package Topten\Blocks
 *
 * @since 1.0.0
 *
 * @param bool       $sub_field Is field a sub field, default false
 * @param array|bool $block ACF group (left_block etc), default empty array
 * @param string     $selector ACF field selector, default 'focal_point'
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
 * Prints (echo) buttons (structure: div.buttons -> a.button)
 * Function assumes that repeater field name is 'buttons' and single button name is 'button'
 * If buttons are inside a block (e.g. $left_block, $right_block), then the block should be given as a parameter
 *
 * E.g.
 * $left_block = get_field( 'left_block' );
 * topten_buttons( $left_block );
 *
 * Otherwise the parameter can be left empty
 *
 * @package Topten\Blocks
 *
 * @since 1.0.0
 *
 * @param array|bool $block ACF group (left_block etc), default empty array
 * @param string     $background Button background color
 */
function topten_buttons( $block = array(), $background = '' ) {
	if ( $block ) {
		if ( ! empty( $block['buttons'] ) ) {
			echo '<div class="buttons">';
			// if icon sub field exists
			if ( ! empty( $block['button_icon'] ) && 'none' !== $block['button_icon'] ) {
				$class = $block['button_icon'];
			} else {
				$class = '';
			}

			// if background isn't empty
			if ( $background ) {
				$background = 'background-' . $background;
			}

			foreach ( $block['buttons'] as $button ) {
				if ( ! empty( $button['button'] ) ) {
					$button = $button['button'];
					$href   = $button['url'];
					$title  = $button['title'];
					$target = $button['target'];
					if ( 'back' === $block['button_icon'] ) {
						echo sprintf( '<a class="button %s %s" href="%s" title="%s" target="%s"><span class="material-symbols">arrow_right_alt</span>%s</a>', esc_attr( $class ), esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
					} elseif ( 'rss' === $block['button_icon'] ) {
						echo sprintf( '<a class="button %s %s" href="%s" title="%s" target="%s"><span class="material-symbols">rss_feed</span>%s</a>', esc_attr( $class ), esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
					} else {
						echo sprintf( '<a class="button %s %s" href="%s" title="%s" target="%s">%s</a>', esc_attr( $class ), esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
					}
				}
			}

			echo '</div>';
		}
	} elseif ( have_rows( 'buttons' ) ) {
		echo '<div class="buttons">';

		while ( have_rows( 'buttons' ) ) {
			the_row();

			$button = get_sub_field( 'button' );

			if ( get_sub_field( 'button_icon' ) && 'none' !== get_sub_field( 'button_icon' ) ) {
				$class = get_sub_field( 'button_icon' );
			} else {
				$class = '';
			}

			// if background isn't empty
			if ( $background ) {
				$background = 'background-' . $background;
			}

			if ( $button ) {
				$href   = esc_url( $button['url'] );
				$title  = esc_attr( $button['title'] );
				$target = esc_attr( $button['target'] );
				if ( 'back' === get_sub_field( 'button_icon' ) ) {
					echo sprintf( '<a class="button %s %s" href="%s" title="%s" target="%s"><span class="material-symbols">arrow_right_alt</span>%s</a>', esc_attr( $class ), esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
				} elseif ( 'rss' === get_sub_field( 'button_icon' ) ) {
					echo sprintf( '<a class="button %s %s" href="%s" title="%s" target="%s"><span class="material-symbols">rss_feed</span>%s</a>', esc_attr( $class ), esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
				} else {
					echo sprintf( '<a class="button %s %s" href="%s" title="%s" target="%s">%s</a>', esc_attr( $class ), esc_attr( $background ), esc_url( $href ), esc_attr( $title ), esc_attr( $target ), wp_kses_post( $title ) );
				}
			}
		}

		echo '</div>';
	}
}

/**
 * Creates a table of contents from the title blocks of the card
 * This is horrible, but it works? Not my finest work
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param bool $echo Echo?
 */
function topten_get_table_of_contents( $echo = true ) {
	$blocks = parse_blocks( get_the_content() );

	$table_of_contents = '<ol class="table-of-contents" role="list">';

	$h2_open = false;
	$h3_open = false;
	$h4_open = false;
	$h5_open = false;

	$block_title_ids = array();

	// Rivit
	foreach ( $blocks as $row ) {
		if ( ! empty( $row['innerBlocks'] ) ) {

			// Sarakkeet
			foreach ( $row['innerBlocks'] as $index => $column ) {
				if ( ! empty( $column['innerBlocks'] ) ) {

					// Sarakkeen lohkot
					foreach ( $column['innerBlocks'] as $index => $block ) {
						if ( 'acf/otsikko' === $block['blockName'] ) {
							$title = $block['attrs']['data']['title'];
							$id    = sanitize_title( $block['attrs']['data']['title'] );
							$tag   = $block['attrs']['data']['title_tag'];

							if ( ! isset( $block_title_ids[ $id ] ) ) {
								$block_title_ids[ $id ] = array(
									'id'    => $id,
									'count' => 0,
								);
							} else {
								$block_title_ids[ $id ]['count']++;

								$id = $id . '-' . $block_title_ids[ $id ]['count'];
							}

							$href = sprintf( '#%s', $id );

							if ( 'h2' === $tag ) {
								if ( $h5_open ) {
									$table_of_contents .= '</li></ol>';

									$h5_open = false;
								}

								if ( $h4_open ) {
									$table_of_contents .= '</li></ol>';

									$h4_open = false;
								}

								if ( $h3_open ) {
									$table_of_contents .= '</li></ol>';

									$h3_open = false;
								}

								if ( $h2_open ) {
									$table_of_contents .= sprintf( '</li><li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );
								} else {
									$table_of_contents .= sprintf( '<li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );

									$h2_open = true;
								}
							}

							if ( 'h3' === $tag ) {
								if ( $h5_open ) {
									$table_of_contents .= '</li></ol>';

									$h5_open = false;
								}

								if ( $h4_open ) {
									$table_of_contents .= '</li></ol>';

									$h4_open = false;
								}

								if ( $h3_open ) {
									$table_of_contents .= sprintf( '</li><li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );
								} else {
									if ( ! $h2_open ) {
										$table_of_contents .= '<ol class="h2-list sub-list" role="list"><li>';

										$h2_open = true;
									}

									$table_of_contents .= '<ol class="h3-list sub-list" role="list">';
									$table_of_contents .= sprintf( '<li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );

									$h3_open = true;
								}
							}

							if ( 'h4' === $tag ) {
								if ( $h5_open ) {
									$table_of_contents .= '</li></ol>';

									$h5_open = false;
								}

								if ( $h4_open ) {
									$table_of_contents .= sprintf( '</li><li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );
								} else {
									if ( ! $h2_open ) {
										$table_of_contents .= '<ol class="h2-list sub-list" role="list"><li>';

										$h2_open = true;
									}

									if ( ! $h3_open ) {
										$table_of_contents .= '<ol class="h3-list sub-list" role="list"><li>';

										$h3_open = true;
									}

									$table_of_contents .= '<ol class="h4-list sub-list" role="list">';
									$table_of_contents .= sprintf( '<li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );

									$h4_open = true;
								}
							}

							if ( 'h5' === $tag ) {
								if ( $h5_open ) {
									$table_of_contents .= sprintf( '</li><li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );
								} else {
									if ( ! $h2_open ) {
										$table_of_contents .= '<ol class="h2-list sub-list" role="list"><li>';

										$h2_open = true;
									}

									if ( ! $h3_open ) {
										$table_of_contents .= '<ol class="h3-list sub-list" role="list"><li>';

										$h3_open = true;
									}

									if ( ! $h4_open ) {
										$table_of_contents .= '<ol class="h4-list sub-list" role="list"><li>';

										$h4_open = true;
									}

									$table_of_contents .= '<ol class="h5-list sub-list" role="list">';
									$table_of_contents .= sprintf( '<li><a href="%s" aria-label="%s">%s</a>', esc_url( $href ), esc_attr( $title ), esc_html( $title ) );

									$h5_open = true;
								}
							}
						}
					}
				}
			}
		}
	}

	$table_of_contents .= '</li></ol>';

	if ( $echo ) {
		echo wp_kses_post( $table_of_contents );
	} else {
		// this could probably be better but it works anyway for my purposes
		if ( '<ol class="table-of-contents" role="list"></li></ol>' === $table_of_contents ) {
			return false;
		} else {
			return true;
		}
	}
}

/**
 * Create title numbers
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @return array Title numbers
 */
function topten_get_title_numbers(): array {
	$blocks = parse_blocks( get_the_content() );

	$title_numbers = array();

	$h2s = 0;
	$h3s = 0;
	$h4s = 0;
	$h5s = 0;

	// Rows
	foreach ( $blocks as $row ) {
		if ( ! empty( $row['innerBlocks'] ) ) {

			// Columns
			foreach ( $row['innerBlocks'] as $index => $column ) {
				if ( ! empty( $column['innerBlocks'] ) ) {

					// Column blocks
					foreach ( $column['innerBlocks'] as $index => $block ) {
						if ( 'acf/otsikko' === $block['blockName'] ) {
							$title = $block['attrs']['data']['title'];
							$tag   = $block['attrs']['data']['title_tag'];

							$id = sanitize_title( $title );

							if ( ! isset( $block_title_ids[ $id ] ) ) {
								$block_title_ids[ $id ] = array(
									'id'    => $id,
									'count' => 0,
								);
							} else {
								$block_title_ids[ $id ]['count']++;

								$id = $id . '-' . $block_title_ids[ $id ]['count'];
							}

							if ( 'h1' === $tag ) {
								continue;
							}

							if ( 'h2' === $tag ) {
								$h2s++;

								$number = $h2s . '.';

								$h3s = 0;
								$h4s = 0;
								$h5s = 0;
							} elseif ( 'h3' === $tag ) {
								$h3s++;

								$number = $h2s . '.' . $h3s . '.';

								$h4s = 0;
								$h5s = 0;
							} elseif ( 'h4' === $tag ) {
								$h4s++;

								$number = $h2s . '.' . $h3s . '.' . $h4s . '.';

								$h5s = 0;
							} elseif ( 'h5' === $tag ) {
								$h5s++;

								$number = $h2s . '.' . $h3s . '.' . $h4s . '.' . $h5s . '.';
							}

							$title_numbers[ $id ] = $number;
						}
					}
				}
			}
		}
	}

	return $title_numbers;
}

/**
 * Get title number by ID
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param string $id Title ID
 *
 * @return string Title number
 */
function topten_get_title_number( $id = '' ): string {
	$title_numbers = topten_get_title_numbers();

	if ( isset( $title_numbers[ $id ] ) ) {
		return $title_numbers[ $id ];
	}

	return '';
}

/**
 * Gets the card "desc" (small title)
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param string $description Description
 */
function topten_get_desc( $description = false ) {
	if ( ! $description ) {
		$description = get_field( 'description' );
	}

	if ( $description ) {
		echo sprintf( '<h2 class="desc">%s</h2>', esc_html( $description ) );
	}
}

/**
 * Retrieves color from options page based on value
 *
 * @package Topten\Cards
 *
 * @since 1.0.0
 *
 * @param string $value Value on options page
 *
 * @return string|bool
 */
function topten_get_guide_color( $value ) {
	$guide = get_field( 'guide', 'options' );

	$index = array_search( $value, array_column( $guide, 'icon' ), true );

	if ( false !== $index ) {
		return $guide[ $index ]['color'];
	}

	return false;
}

/**
 * Get block width in percent
 *
 * @package Topten\Blocks
 *
 * @since 1.0.0
 */
function topten_get_block_width() {
	$width = get_field( 'width' ) ?: 100;
	$width = intval( $width );

	$width = $width > 100 ? 100 : $width;
	$width = $width < 0 ? 0 : $width;

	echo '--width: ' . esc_attr( $width ) . '%;';
}
