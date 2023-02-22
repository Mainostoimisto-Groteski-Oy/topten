<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

require_once get_template_directory() . '/includes/tfpdf/fpdfa.php';

/**
 * Path to font directory, for FPDF
 *
 * @package Topten\PDF
 *
 * @since 1.0.0
 */
define( 'FPDF_FONTPATH', get_template_directory() . '/fonts/tfpdf' ); // phpcs:ignore

/**
 * Class for PDF generation
 * Extends FPDFA (PDF-A compliant version of FPDF)
 * Parses HTML to PDF
 *
 * Not currently in use
 *
 * @see http://www.fpdf.org/en/script/script53.php
 *
 * @since 1.0.0
 *
 * @package Topten\PDF
 */
class Topten_PDF extends FPDFA {
	/**
	 * Blacklisted tags
	 *
	 * @since 1.0.0
	 *
	 * @var string[] Blacklisted tags
	 */
	protected array $tag_blacklist = array(
		'\n',
		'\t',
	);

	/**
	 * Line height (css)
	 *
	 * @since 1.0.0
	 *
	 * @var float Line height (css)
	 */
	protected float $line_height_css = 1.5;

	/**
	 * Line height (pt) calculated in set_size-method
	 *
	 * @since 1.0.0
	 *
	 * @var float Line height
	 */
	protected float $line_height = 0;

	/**
	 * Line height (mm), calculated in set_size-method
	 *
	 * @since 1.0.0
	 *
	 * @var float Line height
	 */
	protected float $line_height_mm = 0;

	/**
	 * Column classes
	 *
	 * @since 1.0.0
	 *
	 * @var string Column classes
	 */
	protected string $column_class = '';

	/**
	 * Is current tag a material symbol? (material-icons)
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected bool $is_material_symbol = false;

	/**
	 * Is current text after a material symbol?
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected bool $is_material_text = false;

	/**
	 * Size multiplier
	 * The card content area is not actually A4, so we need to scale everything
	 * This is calculated in generate_pdf-method
	 *
	 * @since 1.0.0
	 *
	 * @var float
	 */
	protected float $multiplier = 0;

	/**
	 * Font size (px)
	 * This is calculated in set_size-method and based on tag
	 *
	 * @since 1.0.0
	 *
	 * @var float Font size (px)
	 */
	protected float $font_size = 16;

	/**
	 * Font size (pt)
	 * $font_size converted to points
	 *
	 * @since 1.0.0
	 *
	 * @var float Font size (pt)
	 */
	protected float $font_size_pt = 12;

	/**
	 * Font style (bold, italic, etc)
	 *
	 * @since 1.0.0
	 *
	 * @var string Font style (B = bold, I = italic, U = underline, empty = normal)
	 */
	protected string $font_style = '';

	/**
	 * Current column height
	 *
	 * @since 1.0.0
	 *
	 * @var float Height
	 */
	protected float $height = 0;

	/**
	 * Page offsets
	 * If current element would end up between pages, we are moving to next page
	 * But obviously other elements need to move too, so we need to know how much we need to move them
	 *
	 * @since 1.0.0
	 *
	 * @var array Page offsets
	 */
	protected array $page_offsets = array();

	/**
	 * What was the previous page?
	 *
	 * @since 1.0.0
	 *
	 * @var int Previous page
	 */
	protected int $previous_page = 0;

	/**
	 * What was the previous y?
	 *
	 * @since 1.0.0
	 *
	 * @var float Previous y
	 */
	protected float $previous_y = 0;

	/**
	 * Is current element td?
	 *
	 * @since 1.0.0
	 *
	 * @var bool Is td
	 */
	protected bool $is_td = false;

	/**
	 * Rects array
	 * Rects are drawn before texts (otherwise they will draw over texts)
	 * But if text will have offset, we need to lengthen the rects
	 *
	 * @var array Rects
	 */
	protected array $rects = array();

	/**
	 * Convert pixels to millimeters
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $px Pixels
	 *
	 * @return float Pixels converted to millimeters
	 */
	protected function px_to_mm( int|float $px ): float {
		return (float) $px * 0.264583333;
	}

	/**
	 * Convert millimeters to pixels
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $mm Millimeters
	 *
	 * @return float Millimeters converted to pixels
	 */
	protected function mm_to_px( int|float $mm ): float {
		return (float) $mm * 3.779527559;
	}

	/**
	 * Convert pixels to points
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $px Pixels
	 *
	 * @return float Pixels converted to points
	 */
	protected function px_to_pt( int|float $px ): float {
		return (float) $px * 0.75;
	}

	/**
	 * Convert points to millimeters
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $pt Points
	 *
	 * @return float Points converted to millimeters
	 */
	protected function pt_to_mm( int|float $pt ): float {
		return (float) $pt * 0.352777778;
	}

	/**
	 * Set font size by tag
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Element tag (for example h1, h2, h3, p)
	 * @param string $class Element class
	 *
	 * @return void
	 */
	protected function set_size( string $tag, string $class = '' ): void {
		switch ( $tag ) {
			case 'h1':
				$this->font_size = 36;
				break;
			case 'h2':
				if ( str_contains( $class, 'desc' ) ) {
					$this->font_size = 14;
				} else {
					$this->font_size = 32;
				}

				break;
			case 'h3':
				$this->font_size = 28;
				break;
			case 'h4':
				$this->font_size = 24;
				break;
			case 'h5':
				$this->font_size = 20;
				break;
			case 'strong':
				if ( str_contains( $class, 'top_row' ) ) {
					if ( str_contains( $class, 'smaller' ) ) {
						$this->font_size = 14;
					} else {
						$this->font_size = 18;
					}
				} else {
					$this->font_size = 18;
				}
				break;
			case 'label':
				$this->font_size = 18;
				break;
			case 'bullet':
				$this->font_size = 24;
				break;
			case 'number':
				$this->font_size = 18;
				break;
			case 'checkbox':
				$this->font_size = 18;
				break;
			case 'td':
				$this->font_size = 16;
				break;
			default:
				$this->font_size = 18;
		}

		if ( $this->is_td ) {
			$this->font_size = 16;
		}

		if ( 'label-text' === $class ) {
			$this->font_size = 16;
		}

		if ( 'prefix' === $class || 'suffix' === $class ) {
			$this->font_size = 16;
		}

		if ( 'material-text' === $class ) {
			$this->font_size = 18;
		}

		$this->font_size = $this->font_size * $this->multiplier;

		$this->font_size_pt = $this->px_to_pt( $this->font_size );

		$this->line_height_css = $this->line_height_css;

		$this->line_height    = $this->px_to_pt( $this->font_size * $this->line_height_css );
		$this->line_height_mm = $this->pt_to_mm( $this->line_height );

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );

		$this->current_class = $class;
		$this->current_tag   = $tag;
	}

	/**
	 * Set font style by tag
	 *
	 * @since 1.0.0
	 *
	 * @todo Italic font is not working, Blinker-Italic does not exist
	 *
	 * @param string $tag Tag
	 * @param string $class Class
	 *
	 * @return void
	 */
	protected function set_style( string $tag, string $class = '' ): void {
		switch ( $tag ) {
			case 'b':
			case 'strong':
				$this->SetTextColor( 0, 0, 0 );
				$this->font_style = 'B';
				break;
			case 'i':
			case 'em':
				// $this->font_style = 'I';
				$this->SetTextColor( 0, 0, 0 );
				$this->font_style = '';
				break;
			case 'a':
				$this->SetTextColor( 78, 93, 51 );
				$this->font_style = 'U';
				break;
			default:
				$this->SetTextColor( 0, 0, 0 );
				$this->font_style = '';
				break;
		}

		if ( 'desc' === $class ) {
			// $this->font_style = 'I';
			$this->font_style = '';
		}

		$this->current_class = $class;
		$this->current_tag   = $tag;

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );
	}

	/**
	 * Set coordinates
	 *
	 * @todo Figure out why page break causes elements to be drawn on top of each other, this is not working properly
	 *
	 * @since 1.0.0
	 *
	 * @param float $x X coordinate
	 * @param float $y Y coordinate
	 * @param bool  $return Return instead of setting?
	 * @param bool  $set_offset Set offset?
	 *
	 * @return array|void
	 */
	protected function set_coords( float $x, float $y, bool $return = false, bool $set_offset = true ) {
		$x = $this->px_to_mm( $x ) * $this->multiplier;
		$y = $this->px_to_mm( $y ) * $this->multiplier;

		$original_y = $y;

		if ( ! $y ) {
			return;
		}

		$height = $this->GetPageHeight() - $this->tMargin - $this->tMargin;

		// Calculate page for element
		$page = ceil( $y / $height );

		// Add top margin to y
		$y = $y + ( ( $page - 1 ) * $this->tMargin );

		// Recalculate page after adding top margin
		$page = ceil( $y / $height );

		// if ( abs( $original_y - $this->previous_y ) < PHP_FLOAT_EPSILON ) {
		// $offset = $this->page_offsets[ $page ] ?? 0;
		// $y     += $offset;
		// } else {
		// $offset = $this->page_offsets[ $page ] ?? 0;
		// $y     += $offset;
		// }

		// Recalculate page after adding offset
		$page = ceil( $y / $height );

		// Calculate position on page
		$y = $y - ( ( $page - 1 ) * $height );

		if ( $y >= $this->PageBreakTrigger ) {
			$page++;

			$y = ( $y - $this->PageBreakTrigger );
		}

		if ( $y + $this->height >= $this->PageBreakTrigger ) {
			$page++;

			// if ( $set_offset ) {
			// if ( ! isset( $this->page_offsets[ $page ] ) ) {
			// $this->page_offsets[ $page ] = $this->height;

			// error_log( 'Setting offset for page ' . $page . ' to ' . $this->page_offsets[ $page ] );

			// if ( isset( $this->rects[ $page ] ) ) {
			// foreach ( $this->rects[ $page ] as $rect ) {
			// if ( $rect['is_last'] ) {
			// continue;
			// }

			// $this->Rect(
			// $rect['x'],
			// $rect['y'] + $rect['h'],
			// $rect['w'],
			// $this->height,
			// 'F'
			// );
			// }
			// }
			// }
			// }

			// if ( isset( $this->page_offsets[ $page ] ) ) {
			// $y = $this->tMargin + $this->page_offsets[ $page ];
			// } else {
				$y = $this->tMargin;
			// }
		} elseif ( $y < $this->tMargin ) {
			// if ( isset( $this->page_offsets[ $page ] ) ) {
			// if ( abs( $original_y - $this->previous_y ) < PHP_FLOAT_EPSILON ) {
			// $y = $this->tMargin;
			// } else {
			// $y = $this->tMargin + $this->page_offsets[ $page ];
			// }
			// } else {
			// $this->page_offsets[ $page ] = $this->height;

				$y = $this->tMargin;
			// }
		}

		// If page does not exist, add it
		if ( empty( $this->pages[ $page ] ) ) {
			$this->AddPage();
		} else {
			$this->page = $page;
		}

		$this->previous_page = $page;
		$this->previous_y    = $original_y;

		if ( $return ) {
			return array(
				'x' => $x,
				'y' => $y,
			);
		} else {
			$this->SetXY( $x, $y );
		}
	}

	/**
	 * Starts the HTML to PDF conversion
	 *
	 * @since 1.0.0
	 *
	 * @param array $data HTML data
	 *
	 * @return void
	 */
	public function generate_pdf( array $data ): void {
		$content_area = $this->px_to_mm( 873.33333 );
		$page_width   = $this->GetPageWidth();

		// We need to rescale everything based on the content area
		$this->multiplier = $page_width / $content_area;

		$margin = $this->px_to_mm( 15 );
		$margin = $this->multiplier * $margin;

		$this->SetMargins(
			$margin,
			$margin,
			$margin
		);

		$this->column_start_x = $this->lMargin;
		$this->column_end_x   = $this->GetPageWidth() - $this->rMargin;
		$this->column_width   = $this->GetPageWidth() - $this->lMargin - $this->rMargin;

		$this->SetAutoPageBreak( true, $margin );
		$this->SetAutoPageBreak( false );
		$this->PageBreakTrigger = $this->GetPageHeight() - $this->tMargin;

		$this->AddFont( 'Blinker', '', 'Blinker-Regular.ttf', true );
		$this->AddFont( 'Blinker', 'B', 'Blinker-Bold.ttf', true );

		// $this->AddFont( 'Blinker', 'I', 'Blinker-Regular.ttf', true );

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );

		$this->AddPage();

		foreach ( $data['rows'] as $index => $row ) {
			// I give up, this is very inefficient, but whatever
			$this->write_rects( $row );
			$this->write_columns( $row, $index );
		}
	}

	/**
	 * Write rects
	 *
	 * @since 1.0.0
	 *
	 * @param array $row Row data
	 *
	 * @return void
	 */
	protected function write_rects( array $row ): void {
		$columns = $row['columns'] ?? array();

		foreach ( $columns as $column ) {
			$width = $column['attributes']['width'] ?? 0;
			$class = $column['attributes']['class'] ?? '';

			$width = $width * $this->multiplier;

			foreach ( $column['data'] as $column_children ) {
				$class  = $column_children['attributes']['class'] ?? '';
				$x      = $column_children['attributes']['x'] ?? 0;
				$y      = $column_children['attributes']['y'] ?? 0;
				$height = $column_children['attributes']['height'] ?? 0;

				if ( str_contains( $class, 'bg-blue' ) || str_contains( $class, 'bg-red' ) || str_contains( $class, 'bg-green' ) || str_contains( $class, 'bg-lightblue' ) ) {
					$rect_x      = $this->px_to_mm( $x ) * $this->multiplier;
					$rect_y      = $this->px_to_mm( $y ) * $this->multiplier;
					$rect_width  = $this->px_to_mm( $width );
					$rect_height = $this->px_to_mm( $height ) * $this->multiplier;

					// if ( 1 !== (int) $this->PageNo() ) {
					// $rect_y += $this->tMargin * 2;
					// }

					$height = $this->GetPageHeight() - $this->tMargin - $this->tMargin;

					// Calculate page for element
					$page = ceil( $rect_y / $height );

					if ( empty( $this->pages[ $page ] ) ) {
						$last_index = array_key_last( $this->pages );
						$offset     = $page - $last_index;

						for ( $i = 0; $i < $offset; $i++ ) {
							$this->AddPage();
						}
					} else {
						$this->page = $page;
					}

					$rect_y = $rect_y - ( $page - 1 ) * $height;
					$rect_y = $rect_y + ( ( $page - 1 ) * $this->tMargin );

					if ( $rect_y < $this->tMargin ) {
						$rect_y = $this->tMargin;
					}

					if ( str_contains( $class, 'bg-blue' ) ) {
						$this->SetFillColor( 230, 231, 241 );
					} elseif ( str_contains( $class, 'bg-red' ) ) {
						$this->SetFillColor( 241, 234, 234 );
					} elseif ( str_contains( $class, 'bg-green' ) ) {
						$this->SetFillColor( 234, 237, 232 );
					} elseif ( str_contains( $class, 'bg-lightblue' ) ) {
						$this->SetFillColor( 232, 241, 244 );
					}

					$content_end = $this->GetPageHeight() - $this->tMargin;

					if ( $rect_y + $rect_height > $content_end ) {
						$on_this_page = $this->GetPageHeight() - $this->tMargin - $rect_y;
						$on_next_page = $rect_height - $on_this_page + $this->tMargin;

						$this->Rect(
							$rect_x,
							$rect_y,
							$rect_width,
							$on_this_page,
							'F'
						);

						$this->rects[ $page ][] = array(
							'page'    => $page,
							'x'       => $rect_x,
							'y'       => $rect_y,
							'w'       => $rect_width,
							'h'       => $on_this_page,
							'is_last' => true,
						);

						$this->maybe_add_page();

						$this->Rect(
							$rect_x,
							$this->tMargin,
							$rect_width,
							$on_next_page,
							'F'
						);

						$this->rects[ $page ][] = array(
							'x'       => $rect_x,
							'y'       => $rect_y,
							'w'       => $rect_width,
							'h'       => $on_next_page,
							'is_last' => false,
						);
					} else {
						$this->rects[ $page ][] = array(
							'x'       => $rect_x,
							'y'       => $rect_y,
							'w'       => $rect_width,
							'h'       => $rect_height,
							'is_last' => true,
						);

						$this->Rect(
							$rect_x,
							$rect_y,
							$rect_width,
							$rect_height,
							'F'
						);
					}
				}
			}
		}
	}

	/**
	 * Write columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $row Row data
	 * @param int   $row_index Row index
	 *
	 * @return void
	 */
	protected function write_columns( array $row, int $row_index ): void {
		// Columns count
		$columns = $row['columns'] ?? array();

		if ( 0 !== $row_index ) {
			$x = $row['attributes']['x'] ?? 0;
			$y = $row['attributes']['y'] ?? 0;

			$coords = $this->set_coords( $x, $y, true, false );

			$this->Line(
				$this->lMargin,
				$coords['y'],
				$this->GetPageWidth() - $this->rMargin,
				$coords['y'],
			);
		}

		foreach ( $columns as $col_index => $column ) {
			$start_page = $this->PageNo();

			foreach ( $column['data'] as $column_children ) {
				$this->column_class = $column_children['attributes']['class'] ?? '';

				$this->handle_output( $column_children, $row_index );
			}

			$end_page = $this->PageNo();

			$pages = $end_page - $start_page;

			for ( $i = 0; $i <= $pages; $i ++ ) {
				$this->page = $start_page + $i;

				$this->Rect(
					$this->tMargin,
					$this->lMargin,
					$this->GetPageWidth() - $this->rMargin - $this->lMargin,
					$this->GetPageHeight() - $this->tMargin - $this->tMargin,
					'D'
				);
			}

			$this->page = $end_page;
		}
	}

	/**
	 * Write image
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 *
	 * @return void
	 */
	protected function write_image( array $data ): void {
		$src        = $data['attributes']['src'] ?? '';
		$x          = $data['attributes']['x'] ?? 0;
		$y          = $data['attributes']['y'] ?? 0;
		$img_width  = $data['attributes']['width'] ?? '';
		$img_height = $data['attributes']['height'] ?? '';

		$img_width  = $this->px_to_mm( $img_width ) * $this->multiplier;
		$img_height = $this->px_to_mm( $img_height ) * $this->multiplier;
		$x          = $this->px_to_mm( $x ) * $this->multiplier;
		$y          = $this->px_to_mm( $y ) * $this->multiplier;

		$height = $this->GetPageHeight() - $this->tMargin - $this->tMargin;

		$page = ceil( $y / $height );

		if ( empty( $this->pages[ $page ] ) ) {
			$this->AddPage();
		} else {
			$this->page = $page;
		}

		$y = $y - ( $page - 1 ) * $height;
		$y = $y + ( ( $page - 1 ) * $this->tMargin );

		if ( $y < $this->tMargin ) {
			$y = $this->tMargin;
		}

		$content_end = $this->GetPageHeight() - $this->tMargin;

		if ( $y + $img_height > $content_end ) {
			$on_this_page = $this->GetPageHeight() - $this->tMargin - $y;
			$on_next_page = $img_height - $on_this_page + $this->tMargin;

			$this->ClippingRect( $x, $y, $img_width, $on_this_page );
			$this->Image( $src, $x, $y, $img_width, $img_height );
			$this->UnsetClipping();

			$this->maybe_add_page();

			$this->ClippingRect( $x, $this->tMargin, $img_width, $on_next_page );
			$this->Image( $src, $x, $this->tMargin - $on_this_page, $img_width, $on_next_page );
			$this->UnsetClipping();
		} else {
			$this->Image( $src, $x, $y, $img_width, $img_height );
		}
	}

	/**
	 * Write list
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 *
	 * @return void
	 */
	protected function write_list( array $data ): void {
		if ( ! isset( $data['children'] ) ) {
			return;
		}

		$list_type = $data['tag'];

		$li_number = 0;

		foreach ( $data['children'] as $index => $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'li' !== $tag ) {
				continue;
			}

			$children = $datum['children'];

			$x      = $datum['attributes']['x'] ?? 0;
			$y      = $datum['attributes']['y'] ?? 0;
			$height = $datum['attributes']['height'] ?? 0;
			$height = $this->px_to_mm( $height ) * $this->multiplier;

			if ( 'ol' === $list_type ) {
				$this->set_size( 'number' );
				$this->set_style( 'number' );

				// If list type is ordered, add a number
				$li_number++;

				$char = $li_number . '.';

				// Write char
				$this->set_coords( $x - 20, $y + 2.5 );

				$this->set_size( 'number' );
				$this->set_style( 'number' );

				$this->Write( $this->line_height_mm, $char . ' ' );
			} else {
				// Otherwise add a bullet
				$this->set_size( 'bullet' );
				$this->set_style( 'bullet' );

				$char = '•';

				// Write char
				$this->set_coords( $x - 15, $y - 2 );

				$this->set_size( 'bullet' );
				$this->set_style( 'bullet' );

				$this->Write( $this->line_height_mm, $char . ' ' );
			}

			// Write list item
			$this->write_list_item( $children, $char );
		}
	}

	/**
	 * Write list item
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param string $char Character to add before list item
	 * @param string $parent_tag Parent tag
	 * @param string $href Link href
	 *
	 * @return void
	 */
	protected function write_list_item( array $data, string $char, string $parent_tag = '', string $href = '' ): void {
		foreach ( $data as $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'a' === $tag ) {
				$this->write_link( $datum );

				continue;
			}

			if ( 'span' !== $tag && '// text' !== $tag ) {
				$parent_tag = $tag;
			}

			if ( ! empty( $datum['children'] ) ) {
				$child_tag = $datum['tag'];

				if ( 'ul' === $child_tag || 'ol' === $child_tag ) {
					$this->write_list( $datum, true );

					continue;
				} else {
					$this->write_list_item( $datum['children'], $char, $parent_tag, $href );
				}
			} else {
				$value = str_replace( $this->tag_blacklist, '', $datum['value'] );
				$value = wp_strip_all_tags( $datum['value'] );

				if ( ! $value ) {
					continue;
				}

				$x            = $datum['attributes']['x'] ?? 0;
				$y            = $datum['attributes']['y'] ?? 0;
				$height       = $datum['attributes']['height'] ?? 0;
				$this->height = $this->px_to_mm( $height ) * $this->multiplier;

				$this->set_style( $parent_tag );
				$this->set_size( $parent_tag );

				$value .= ' ';

				$this->set_coords( $x, $y );

				$this->set_style( $parent_tag );
				$this->set_size( $parent_tag );

				$this->Write( $this->height, $value );
			}
		}
	}

	/**
	 * Get link text
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data Data
	 * @param string $string String
	 * @param int    $count Count
	 * @param array  $coords Coordinates
	 */
	protected function get_link_text( array $data, string &$string = '', int &$count = 0, array &$coords = array() ) {
		if ( ! empty( $data['children'] ) ) {
			foreach ( $data['children'] as $child ) {
				$this->get_link_text( $child, $string, $count, $coords );
			}
		} else {
			$count++;

			$value = str_replace( $this->tag_blacklist, '', $data['value'] );
			$value = wp_strip_all_tags( $data['value'] );

			if ( $value ) {
				$string .= $value . ' ';
			}

			if ( 1 === $count ) {
				$x = $data['attributes']['x'] ?? 0;
				$y = $data['attributes']['y'] ?? 0;

				$coords = array(
					'x' => $x,
					'y' => $y,
				);
			}
		}

		return $coords;
	}

	/**
	 * Write link
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Link data
	 *
	 * @return void
	 */
	protected function write_link( array $data ): void {
		if ( ! isset( $data['children'] ) ) {
			return;
		}

		$href = $data['value'] ?? '';

		$value = '';

		$coords = $this->get_link_text( $data, $value );

		$this->set_style( 'a' );
		$this->set_size( 'a' );

		$x = $coords['x'] ?? 0;
		$y = $coords['y'] ?? 0;

		$this->set_coords( $x, $y );

		$this->Write( $this->line_height_mm, $value, esc_url_raw( $href ) );
	}

	/**
	 * Write text
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data Data, containing tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param string $parent_tag Parent tag
	 * @param string $parent_class Parent class
	 * @param string $href Link href
	 */
	protected function write_text( array $data, string $parent_tag = '', string $parent_class = '', string $href = '' ): void {
		if ( ! isset( $data['children'] ) ) {
			return;
		}

		$class = sanitize_text_field( $data['attributes']['class'] ) ?? '';
		$tag   = sanitize_text_field( $data['tag'] );

		if ( str_contains( $class, 'responsive-only' ) || str_contains( $parent_class, 'responsive-only' ) ) {
			return;
		}

		if ( 'td' === $tag ) {
			$this->is_td = true;
		}

		if ( 'span' !== $tag || str_contains( $class, 'tulkinta-symbol' ) || str_contains( $class, 'label-text' ) ) {
			$parent_tag   = sanitize_text_field( $data['tag'] ) ?? '';
			$parent_class = sanitize_text_field( $data['attributes']['class'] ) ?? '';
		}

		// Actual writable text has a tag "#text", and it's parents tag is the actual tag (for example "p", "strong", etc)
		$x = $data['attributes']['x'] ?? 0;
		$y = $data['attributes']['y'] ?? 0;

		if ( str_contains( $parent_class, 'material-symbols' ) ) {
			$this->is_material_symbol = true;
		}

		foreach ( $data['children'] as $index => $datum ) {
			if ( ! empty( $datum['children'] ) ) {
				$this->write_text( $datum, $parent_tag, $parent_class );
			} else {
				$x      = $datum['attributes']['x'] ?? 0;
				$y      = $datum['attributes']['y'] ?? 0;
				$height = $datum['attributes']['height'] ?? 0;

				if ( str_contains( $parent_class, 'label-text' ) ) {
					$height = 24;
				}

				$this->height = $this->px_to_mm( $height ) * $this->multiplier;

				if ( $this->is_material_symbol ) {
					$this->is_material_symbol = false;

					$value = str_replace( $this->tag_blacklist, '', $datum['value'] );
					$value = wp_strip_all_tags( $value );

					$src = '';

					if ( 'engineering' === $value ) {
						$src = get_template_directory_uri() . '/assets/dist/icons/lomake-rakennusvalvonta.png';
					} elseif ( '§' === $value ) {
						$src = get_template_directory_uri() . '/assets/dist/icons/lomake-saannokset.png';
					} elseif ( 'i' === $value ) {
						$src = get_template_directory_uri() . '/assets/dist/icons/lomake-ymparistoministerio.png';
					} elseif ( 'done' === $value ) {
						$src = get_template_directory_uri() . '/assets/dist/icons/lomake-ymparistoministerio.png';
					}

					if ( $src ) {
						$width = $this->px_to_mm( 30 ) * $this->multiplier;

						$x = 45;

						$this->set_size( '' );
						$this->set_style( '' );

						$coords = $this->set_coords( $x, $y, true );

						$x = $coords['x'];
						$y = $coords['y'];

						$this->Image( $src, $x, $y, $width, $width );
					}

					continue;
				}

				// If value is not allowed (like \n), remove it
				$value = str_replace( $this->tag_blacklist, '', $datum['value'] );
				$value = wp_strip_all_tags( $value );

				if ( ! $value ) {
					continue;
				}

				if ( 'a' === $parent_tag ) {
					$href = $datum['value'] ?? '';
				}

				if ( str_contains( $parent_class, 'tulkinta-text' ) ) {
					$value = mb_strtoupper( $value );
				}

				$this->set_size( $parent_tag, $parent_class );
				$this->set_style( $parent_tag, $parent_class );

				$this->set_coords( $x, $y );

				$this->set_size( $parent_tag, $parent_class );
				$this->set_style( $parent_tag, $parent_class );

				$this->is_td = false;

				// error_log( 'X: ' . $this->GetX() . ', Y: ' . $this->GetY() . ', page: ' . $this->PageNo() );

				if ( 'a' === $parent_tag ) {
					$this->Write( $this->height, $value, esc_url_raw( $href ) );
				} else {
					$this->Write( $this->height, $value );
				}
			}
		}
	}

	/**
	 * Write table
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data
	 *
	 * @return void
	 */
	protected function write_table( array $data ): void {
		if ( empty( $data['children'] ) ) {
			return;
		}

		foreach ( $data['children'] as $datum ) {
			$tag = $datum['tag'];

			if ( 'tbody' === $tag ) {
				$this->write_table_row( $datum );

				foreach ( $datum['children'] as $child ) {
					$this->write_table_row( $child );
				}
			}
		}
	}

	/**
	 * Write table row
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data
	 */
	protected function write_table_row( array $data ): void {
		foreach ( $data['children'] as $child ) {
			$tag = $child['tag'];
			$tag = sanitize_text_field( $child['tag'] );

			if ( 'a' === $tag ) {
				$this->write_link( $child );

				continue;
			}

			if ( 'td' === $tag ) {
				$x      = $child['attributes']['x'] ?? 0;
				$y      = $child['attributes']['y'] ?? 0;
				$width  = $child['attributes']['width'] ?? 0;
				$height = $child['attributes']['height'] ?? 0;

				$width  = $this->px_to_mm( $width ) * $this->multiplier;
				$height = $this->px_to_mm( $height ) * $this->multiplier;

				$coords = $this->set_coords( $x, $y, true );

				$this->Rect(
					$coords['x'],
					$coords['y'],
					$width,
					$height,
					'D'
				);

				$this->write_text( $child );
			}
		}
	}

	/**
	 * Write input
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data
	 *
	 * @return void
	 */
	protected function write_input( array $data ): void {
		$type = $data['attributes']['type'] ?? '';

		$x = $data['attributes']['x'] ?? 0;
		$y = $data['attributes']['y'] ?? 0;

		if ( 'text' === $type || 'number' === $type || 'textarea' === $type ) {
			$input_x      = $this->px_to_mm( $x ) * $this->multiplier;
			$input_y      = $this->px_to_mm( $y ) * $this->multiplier;
			$input_height = $data['attributes']['height'] ?? 0;
			$input_width  = $data['attributes']['width'] ?? 0;

			if ( 'textarea' === $type ) {
				$input_height += 10;
			}

			$input_height = $this->px_to_mm( $input_height ) * $this->multiplier;
			$input_width  = $this->px_to_mm( $input_width ) * $this->multiplier;

			$this->height = $input_height;

			$height      = $this->GetPageHeight() - $this->tMargin - $this->tMargin;
			$content_end = $this->GetPageHeight() - $this->tMargin;

			// Calculate page for element
			$page = ceil( $input_y / $height );

			// Add top margin to y
			$input_y = $input_y + ( ( $page - 1 ) * $this->tMargin );

			// Recalculate page after adding top margin
			$page = ceil( $input_y / $height );

			// Add offset
			if ( $this->previous_page && (int) $this->previous_page === (int) $page ) {
				if ( $input_y !== $this->previous_y ) {
					$offset   = $this->page_offsets[ $page ] ?? 0;
					$input_y += $offset;
				}
			}

			// Recalculate page after adding offset
			$page = ceil( $input_y / $height );

			// Calculate position on page
			$input_y = $input_y - ( ( $page - 1 ) * $height );

			if ( $input_y < $this->tMargin ) {
				$input_y = $this->tMargin;
			}

			if ( $input_y >= $content_end ) {
				$input_y = $input_y - $height + $this->tMargin;

				$this->maybe_add_page();

				$this->Rect(
					$input_x,
					$input_y,
					$input_width,
					$input_height,
					'D'
				);
			} elseif ( $input_y + $input_height > $content_end ) {
				$on_this_page = $this->GetPageHeight() - $this->tMargin - $input_y;
				$on_next_page = $input_height - $on_this_page + $this->tMargin;

				$this->Rect(
					$input_x,
					$input_y,
					$input_width,
					$on_this_page,
					'D'
				);

				$this->maybe_add_page();

				$this->Rect(
					$input_x,
					$this->tMargin,
					$input_width,
					$on_next_page,
					'D'
				);
			} else {
				$this->Rect(
					$input_x,
					$input_y,
					$input_width,
					$input_height,
					'D'
				);
			}

			if ( empty( $this->pages[ $page ] ) ) {
				$this->AddPage();
			} else {
				$this->page = $page;
			}

			$value = $data['attributes']['value'] ?? '';

			// Remove all tags from value
			$value = wp_strip_all_tags( $value );

			if ( $value ) {
				if ( 'textarea' === $type ) {
					$margin = 0; // Input margin in px
				} else {
					$margin = 10; // Input margin in px
				}

				$this->set_coords( $x, $y + $margin );

				$this->set_style( 'input' );
				$this->set_size( 'input' );

				// Set column start and end point, otherwise text will be written to whole page
				$this->column_start_x = $this->GetX();
				$this->column_end_x   = $this->GetX() + $input_width;

				$this->Write( $this->line_height_mm, $value );

				// Reset column start and end point
				$this->column_start_x = $this->lMargin;
				$this->column_end_x   = $this->GetPageWidth() - $this->rMargin;
			}
		} elseif ( 'checkbox' === $type || 'radio' === $type ) {
			$x = $this->px_to_mm( $x ) * $this->multiplier;
			$y = $this->px_to_mm( $y ) * $this->multiplier;

			$height = $this->GetPageHeight() - $this->tMargin - $this->tMargin;

			// Calculate page for element
			$page = ceil( $y / $height );

			// Add top margin to y
			$y = $y + ( ( $page - 1 ) * $this->tMargin );

			// Recalculate page after adding top margin
			$page = ceil( $y / $height );

			// Add offset
			if ( $this->previous_page && (int) $this->previous_page === (int) $page ) {
				if ( $y !== $this->previous_y ) {
					$offset = $this->page_offsets[ $page ] ?? 0;
					$y     += $offset;
				}
			}

			// Recalculate page after adding offset
			$page = ceil( $y / $height );

			// Calculate position on page
			$y = $y - ( ( $page - 1 ) * $height );

			// Sizes in px
			$checkbox_size = $this->px_to_mm( 14 ) * $this->multiplier;
			$radio_size    = $this->px_to_mm( 7 ) * $this->multiplier;
			$hheight       = $this->px_to_mm( 24 ) * $this->multiplier;

			if ( $y + $hheight >= $this->PageBreakTrigger ) {
				$page++;

				$y = $this->tMargin + 1;
			} elseif ( $y < $this->tMargin ) {
				$y = $this->tMargin + 1;
			}

			// If page does not exist, add it
			if ( empty( $this->pages[ $page ] ) ) {
				$this->AddPage();
			} else {
				$this->page = $page;
			}

			$checked = $data['attributes']['checked'] ?? false;

			if ( is_string( $checked ) ) {
				$checked = $checked === 'true' ? true : false;
			}

			// Set fill if checked
			$fill = $checked ? 'F' : 'D';

			$this->set_size( 'checkbox' );

			if ( 'checkbox' === $type ) {
				$this->Rect(
					$x,
					$y,
					$checkbox_size,
					$checkbox_size,
					$fill,
				);
			} elseif ( 'radio' === $type ) {
				$this->Circle(
					$x + 2,
					$y + 2,
					$radio_size,
					$radio_size,
					$fill,
				);
			}
		}
	}

	/**
	 * Write label
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data
	 *
	 * @return void
	 */
	protected function write_label( array $data ): void {
		if ( ! isset( $data['children'] ) ) {
			return;
		}

		foreach ( $data['children'] as $child ) {
			$tag = $child['tag'];

			if ( 'input' === $tag ) {
				$this->write_input( $child );
			} elseif ( 'div' === $tag ) {
				foreach ( $child['children'] as $grandchild ) {
					$grandchild_tag   = sanitize_text_field( $grandchild['tag'] ) ?? '';
					$grandchild_class = isset( $grandchild['attributes']['class'] ) ? sanitize_text_field( $grandchild['attributes']['class'] ) : '';

					if ( 'input' === $grandchild_tag ) {
						$this->write_input( $grandchild );
					} elseif ( 'span' === $grandchild_tag ) {
						$this->write_text( $grandchild, $grandchild_tag, $grandchild_class );
					}
				}
			} elseif ( 'span' === $tag ) {
				$this->write_text( $child );
			}
		}
	}

	/**
	 * Handle output
	 * Directs data to correct function
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data
	 * @param int   $row_index Row index
	 *
	 * @return void
	 */
	protected function handle_output( array $data, int $row_index ): void {
		$tag      = ! empty( $data['tag'] ) ? sanitize_text_field( $data['tag'] ) : '';
		$value    = ! empty( $data['value'] ) ? sanitize_text_field( $data['value'] ) : '';
		$children = ! empty( $data['children'] ) ? $data['children'] : array();

		if ( 'img' !== $tag && ! $value && ! $children ) {
			return;
		}

		if ( 'div' === $tag || 'figure' === $tag ) {
			if ( $children ) {
				foreach ( $children as $child ) {
					$this->handle_output( $child, $row_index );
				}
			}
		} else {
			if ( 'ul' === $tag || 'ol' === $tag ) {
				$this->write_list( $data );
			} elseif ( 'img' === $tag ) {
				$this->write_image( $data );
			} elseif ( 'label' === $tag ) {
				$this->write_label( $data );
			} elseif ( 'table' === $tag ) {
				$this->write_table( $data );
			} else {
				$this->write_text( $data );
			}
		}
	}
}
