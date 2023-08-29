<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

require_once get_template_directory() . '/includes/tfpdf/fpdfa.php';

define( 'FPDF_FONTPATH', get_template_directory() . '/fonts/tfpdf' ); // phpcs:ignore

/**
 * Class Topten_PDF
 *
 * Based on http://www.fpdf.org/en/script/script53.php
 */
class Topten_PDF extends FPDFA {
	/**
	 * Whitelisted tags
	 *
	 * @var array Whitelisted tags
	 */
	protected array $tag_whitelist = array(
		'section',
		'a',
		'b',
		'br',
		'div',
		'em',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'i',
		'img',
		'li',
		'ol',
		'p',
		'span',
		'strong',
		'ul',
		'sup',
		'sub',
	);

	/**
	 * Blacklisted tags
	 *
	 * @var string[] Blacklisted tags
	 */
	protected array $tag_blacklist = array(
		'\n',
		'\t',
	);

	/**
	 * Font size (px)
	 *
	 * @var float Font size (px)
	 */
	protected float $font_size = 16;

	/**
	 * Font size (pt)
	 *
	 * @var float Font size (pt)
	 */
	protected float $font_size_pt = 12;

	/**
	 * Font style
	 *
	 * @var string Font style (B = bold, I = italic, U = underline, empty = normal)
	 */
	protected string $font_style = '';

	/**
	 * Line height (css)
	 *
	 * @var float Line height (css)
	 */
	protected float $line_height_css = 1.15;

	/**
	 * Line height (pt) calculated in set_size-method
	 *
	 * @var float Line height
	 */
	protected float $line_height = 0;

	/**
	 * Line height (mm), calculated in set_size-method
	 *
	 * @var float Line height
	 */
	protected float $line_height_mm = 0;

	/**
	 * Line margin (px)
	 *
	 * @var float Line margin (px)
	 */
	protected float $line_margin = 15;

	/**
	 * Row padding (mm)
	 *
	 * @var float Row padding (mm)
	 */
	protected float $row_padding = 3.96875;

	/**
	 * Row padding (px)
	 *
	 * @var float Row padding (px)
	 */
	protected float $row_padding_px = 15;

	/**
	 * Page
	 *
	 * @var int Page
	 */
	protected int $c_page = 1;

	/**
	 * Last column index
	 *
	 * @var int|null Last column index
	 */
	protected $last_col_index = null;

	/**
	 * Last row index
	 *
	 * @var int|null Last row index
	 */
	protected $last_row_index = null;

	/**
	 * Room left in current row
	 *
	 * @var float Room left in current row
	 */
	protected float $room_left = 0;

	/**
	 * Column height (mm)
	 *
	 * @var float Column height (mm)
	 */
	protected float $column_height = 0;

	/**
	 * Previous known page
	 *
	 * @var int Previous known page
	 */
	protected int $previous_page = 1;

	/**
	 * Column classes
	 *
	 * @var string Column classes
	 */
	protected string $column_class = '';

	/**
	 * Convert pixels to millimeters
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
	 * @param string $tag Element tag (for example h1, h2, h3, p)
	 * @param string $class Element class
	 *
	 * @return void
	 */
	protected function set_size( string $tag, string $class = '' ): void {
		switch ( $tag ) {
			case 'h1':
				$this->font_size = 32;
				break;
			case 'h2':
				if ( str_contains( $class, 'desc' ) ) {
					$this->font_size = 14;
				} else {
					$this->font_size = 32;
				}

				break;
			case 'h3':
				$this->font_size = 26;
				break;
			case 'strong':
				if ( str_contains( $class, 'top_row' ) ) {
					if ( str_contains( $class, 'smaller' ) ) {
						$this->font_size = 14;
					} else {
						$this->font_size = 20;
					}
				} else {
					$this->font_size = 16;
				}
				break;
			case 'label':
				$this->font_size = 14;
				break;
			default:
				$this->font_size = 16;
		}

		if ( 'label-text' === $class ) {
			$this->font_size = 12;
		}

		$this->font_size_pt = $this->px_to_pt( $this->font_size );

		$this->line_height    = $this->px_to_pt( $this->font_size * $this->line_height_css );
		$this->line_height_mm = $this->pt_to_mm( $this->line_height );

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );
	}

	/**
	 * Set font style by tag
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
				// TODO: ITALICS NOT WORKING
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
			// TODO: ITALICS NOT WORKING
			// $this->font_style = 'I';
			$this->font_style = '';
		}

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );
	}

	/**
	 * Get line widths
	 *
	 * @param array $data Data array
	 * @param bool  $is_top_row Is top row?
	 * @param float $width Width for recursive calls
	 * @param array $widths Widths for recursive calls
	 *
	 * @return array Line widths (mm)
	 */
	protected function get_line_widths( array $data, bool $is_top_row = false, float $width = 0, array $widths = array() ): array {
		if ( ! isset( $data['children'] ) ) {
			return array( 0 );
		}

		$child_count = count( $data['children'] );

		$parent_tag   = sanitize_text_field( $data['tag'] );
		$parent_class = $data['attributes']['class'] ?? '';

		foreach ( $data['children'] as $index => $datum ) {
			if ( ! empty( $datum['children'] ) ) {
				$widths = $this->get_line_widths( $datum, $is_top_row, $width, $widths );
			} else {
				$tag = sanitize_text_field( $datum['tag'] );

				if ( 'br' === $tag ) {
					$widths[] = $width;

					$width = 0;

					continue;
				}

				// If child is not last, add space, so words don't stick together
				$space = $index < $child_count - 1 ? true : false;

				// If value is not allowed (like \n), remove it
				$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

				$this->set_style( $parent_tag );
				$this->set_size( $parent_tag, $parent_class );

				if ( $is_top_row ) {
					$this->set_size( $parent_tag, $parent_class . ' top_row' );
				}

				$string = sanitize_text_field( $value );

				if ( $space ) {
					$string .= ' ';
				}

				$w = $this->GetStringWidth( $string );

				$width += $w;
			}
		}

		$widths[] = $width;

		return $widths;
	}

	/**
	 * Get column height
	 *
	 * @param array $data Data array
	 */
	protected function get_column_height( array $data ): float {
		$column_height = 0;

		// Get line widths
		$line_widths = $this->get_line_widths( $data );

		foreach ( $line_widths as $line_width ) {
			// How many lines string takes
			$lines = ceil( $line_width / $this->column_width );

			// Calculate column child height
			$column_height += ( $this->line_height_mm + $this->row_padding ) * $lines;
		}

		return $column_height;
	}

	/**
	 * Converts HTML to PDF
	 *
	 * @param array $data HTML data
	 *
	 * @return void
	 */
	public function generate_pdf( array $data ): void {
		$this->SetMargins( $this->row_padding * 2, $this->row_padding * 2, $this->row_padding * 2 );

		$this->AddFont( 'Blinker', '', 'Blinker-Regular.ttf', true );
		$this->AddFont( 'Blinker', 'B', 'Blinker-Bold.ttf', true );

		// Todo: italic font is not working, Blinker-Italic does not exist
		// $this->AddFont( 'Blinker', 'I', 'Blinker-Regular.ttf', true );

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );

		$this->AddPage();

		foreach ( $data['rows'] as $index => $row ) {
			$this->write_columns( $row, $index );
		}
	}

	/**
	 * Create columns
	 *
	 * @param array $row Row data
	 * @param int   $row_index Row index
	 *
	 * @return void
	 */
	protected function write_columns( array $row, int $row_index ): void {
		// Columns count
		$columns   = $row['columns'] ?? array();
		$row_class = $row['attributes']['class'] ?? '';

		if ( str_contains( $row_class, 'top' ) ) { // If class includes "top", it is topmost row and is a special case
			$content_width = $this->GetPageWidth() - $this->rMargin - $this->lMargin;

			// There are two columns in top row
			$this->column_width = ( $content_width / 2 );

			$this->SetY( $this->GetY() + $this->row_padding );

			$this->write_top_row( $row );

			unset( $content_width );
		} else {
			foreach ( $columns as $col_index => $column ) {
				$width = $column['attributes']['width'] ?? 100;
				$class = $column['attributes']['class'] ?? '';

				$this->column_class = $class;

				$width_pct     = ( $width / 100 );
				$content_width = $this->GetPageWidth() - $this->rMargin - $this->lMargin;

				$column_start_y = $this->GetY();

				$this->column_width = $content_width * $width_pct;

				$total_column_height = 0;

				$start_page = $this->PageNo();

				foreach ( $column['data'] as $column_children ) {
					$class         = $column_children['attributes']['class'] ?? '';
					$column_height = $this->get_column_height( $column_children );

					$total_column_height += $column_height;

					// Write background color
					if ( str_contains( $class, 'bg-blue' ) ) {
						$this->SetFillColor( 0, 0, 255 );
						$this->Rect( $this->GetX(), $this->GetY(), $this->column_width, $column_height, 'F' );
					}

					$this->handle_output( $column_children, $col_index, $row_index );
				}

				$end_page = $this->PageNo();

				// If column is split to multiple pages, we need to draw multiple lines
				$pages = $end_page - $start_page;

				for ( $i = 0; $i <= $pages; $i ++ ) {
					$this->page = $start_page + $i;

					$this->Rect(
						$this->tMargin - $this->row_padding,
						$this->lMargin - $this->row_padding,
						$this->GetPageWidth() - $this->rMargin,
						$this->GetPageHeight() - $this->bMargin,
						'D'
					);
				}

				$this->page = $end_page;
			}
		}
	}

	/**
	 * Handle top row
	 *
	 * @param array $row Row data
	 *
	 * @return void
	 */
	protected function write_top_row( array $row ): void {
		$columns = $row['columns'] ?? array();

		$start_y = $this->GetY();

		$top_row_height = $this->px_to_mm( 60 ) + ( $this->row_padding * 2 );

		foreach ( $columns as $column_index => $column ) {
			$children = $column['data'];

			$line_widths = array();

			foreach ( $children as $child ) {
				$tag = $child['tag'];

				if ( 'div' === $tag ) {
					$line_width = $this->get_line_widths( $child, true );

					$line_widths = array_merge( $line_widths, $line_width );
				}
			}

			if ( ! empty( $line_widths ) ) {
				$max_line_width = max( $line_widths );
			} else {
				$max_line_width = 0;
			}

			$max_line_width += $this->px_to_mm( 10 ) + $this->rMargin;

			foreach ( $children as $child_index => $child ) {
				$tag   = $child['tag'] ?? '';
				$class = $child['attributes']['class'] ?? '';

				$current_x = $this->GetX();

				// Set parent tag
				$this->set_size( $tag, $class );
				$this->set_style( $tag );

				// Move Y-axis by line height, save current position
				$child_y_position = $this->GetY();

				if ( 'img' === $tag ) {
					$img_width = $child['attributes']['width'] ?? 0;
					$img_width = $this->px_to_mm( $img_width );

					$img_height = $child['attributes']['height'] ?? 0;
					$img_height = $this->px_to_mm( $img_height );

					$max_image_height = $top_row_height - ( $this->row_padding * 2 );

					// Resize image if it is too big
					if ( $img_height > $max_image_height ) {
						$percentage = $max_image_height / $img_height;
						$img_height = $max_image_height;
						$img_width  = $img_width * $percentage;
					}

					// Center image vertically
					$img_y = $start_y - $this->row_padding + ( $top_row_height - $img_height ) / 2;

					$this->SetXY( $this->GetX(), $img_y );

					$this->write_image( $child, $img_width, $img_height );

					$this->SetXY( $current_x + $img_width, $start_y );
				} else {
					// Todo: Figure out why $max_line_width is too small. 10 is a quick fix for now
					$x = $this->GetPageWidth() - $this->lMargin - $this->row_padding - $max_line_width - 10;

					$this->SetX( $x );

					foreach ( $child['children'] as $grandchild_index => $grandchild ) {
						$this->write_text( $grandchild, true, true );

						if ( 1 === $column_index && 1 === $child_index && 1 === $grandchild_index ) {
							$this->SetX( $this->GetX() + $this->px_to_mm( 10 ) );
						}
					}

					// Set XY-coordinates to next child start. X-coordinate doesn't change, Y-coordinate is increased by line height
					$next_child_position = $child_y_position + $this->line_height_mm;

					if ( 0 === $child_index ) {
						$next_child_position += $this->px_to_mm( 5 );
					}

					$this->SetXY( $x, $next_child_position );
				}
			}
		}

		$this->SetY( $start_y + $top_row_height );
	}

	/**
	 * Write image. By default height and width are taken from image attributes, but they can be overridden
	 *
	 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param float $img_width Image width override (mm), 0 = use image attributes
	 * @param float $img_height Image height override (mm), 0 = use image attributes
	 *
	 * @return void
	 */
	protected function write_image( array $data, float $img_width = 0, float $img_height = 0 ): void {
		$src    = $data['attributes']['src'] ?? '';
		$alt    = $data['attributes']['alt'] ?? '';
		$width  = $data['attributes']['width'] ?? '';
		$height = $data['attributes']['height'] ?? '';

		if ( $img_width && $img_height ) {
			$width  = $img_width;
			$height = $img_height;
		} else {
			$width  = $this->px_to_mm( $width );
			$height = $this->px_to_mm( $height );
		}

		$aspect_ratio = $width / $height;

		$width  = min( $width, $this->column_width );
		$height = $width / $aspect_ratio;

		$this->Image( $src, $this->GetX(), $this->GetY(), $width, $height );
	}

	/**
	 * Count list items
	 *
	 * @param array $data Data
	 *
	 * @return int
	 */
	protected function count_lis( $data ): int {
		$lis = 0;

		foreach ( $data as $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'li' === $tag ) {
				$lis++;
			}
		}

		return $lis;
	}

	/**
	 * Write list
	 *
	 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param bool  $child_list Is this a child list?
	 *
	 * @return void
	 */
	protected function write_list( array $data, bool $child_list = false ): void {
		$list_type = $data['tag'];

		$li_number = 0;

		$total_lis = $this->count_lis( $data['children'] );
		$lis       = 0;

		foreach ( $data['children'] as $index => $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'li' !== $tag ) {
				continue;
			}

			$lis++;

			$children = $datum['children'];

			if ( 'ol' === $list_type ) {
				// If list type is ordered, add a number
				$li_number++;

				$char = $li_number . '.';
			} else {
				// Otherwise add a bullet
				$char = '•';
			}

			if ( $child_list ) {
				$this->SetX( $this->GetX() + $this->px_to_mm( 24 ) );
			}

			// Set font size for the bullet and reset it back to original afterwards
			$current_font_size = $this->FontSizePt;
			$this->SetFontSize( 24 );
			$this->Write( $this->line_height_mm, $char . ' ' ); // Add space after the bullet
			$this->SetFontSize( $current_font_size );

			$this->write_list_item( $children );

			if ( $lis < $total_lis ) {
				$this->Ln();
			}

			// $column_height += $this->line_height_mm;
		}
	}

	/**
	 * Write list item
	 *
	 * @param array  $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param string $parent_tag Parent tag
	 * @param string $href Link href
	 *
	 * @return void
	 */
	protected function write_list_item( array $data, string $parent_tag = '', string $href = '' ): void {
		foreach ( $data as $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'a' === $tag ) {
				$href = $datum['value'] ?? '';
			}

			if ( ! empty( $datum['children'] ) ) {
				$child_tag = $datum['tag'];

				if ( 'ul' === $child_tag || 'ol' === $child_tag ) {
					$this->write_list( $datum, true );

					continue;
				} else {
					$this->write_list_item( $datum['children'], $tag, $href );
				}
			} else {
				$this->set_style( $parent_tag );

				$value  = sanitize_text_field( $datum['value'] );
				$value  = str_replace( $this->tag_blacklist, '', $datum['value'] );
				$value .= ' ';

				$line_width = $this->GetStringWidth( $value );

				// Todo
				// if ( $this->GetX() + $line_width > $this->column_end_x && $parent_tag ) {
				// $this->Ln();
				// $value = "\n" . $value;
				// }

				if ( 'a' === $parent_tag ) {
					$this->Write( $this->line_height_mm, $value, esc_url_raw( $href ) );
				} else {
					$this->Write( $this->line_height_mm, $value );
				}
			}
		}
	}

	/**
	 * Write data to PDF-file
	 *
	 * @param array $data Data, containing tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param bool  $uppercase Should text output be in UPPERCASE?
	 * @param bool  $is_top_row Is this top row?
	 */
	protected function write_text( array $data, bool $uppercase = false, bool $is_top_row = false ): void {
		if ( ! isset( $data['children'] ) ) {
			return;
		}

		// Children count
		$child_count = count( $data['children'] );

		// Actual writable text has a tag "#text", and it's parents tag is the actual tag (for example "p", "strong", etc)
		$parent_tag   = sanitize_text_field( $data['tag'] );
		$parent_class = $data['attributes']['class'] ?? '';

		foreach ( $data['children'] as $index => $datum ) {
			if ( ! empty( $datum['children'] ) ) {
				$this->write_text( $datum, $uppercase, $is_top_row );
			} else {
				$tag = sanitize_text_field( $datum['tag'] );

				if ( 'br' === $tag ) {
					$this->Ln();

					continue;
				}

				// If child is not last, add space so words don't stick together
				$space = $index < $child_count - 1 ? true : false;

				// If value is not allowed (like \n), remove it
				$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

				$this->set_style( $parent_tag );

				$value = sanitize_text_field( $value );
				$value = $uppercase ? strtoupper( $value ) : $value;

				if ( $is_top_row && ! $value ) {
					continue;
				}

				if ( $space ) {
					$value .= ' ';
				}

				if ( $is_top_row ) {
					$this->set_size( $parent_tag, $parent_class . ' top_row' );

					$old_y = $this->GetY();

					if ( 'p' === $parent_tag && ! str_contains( $parent_class, 'date-title' ) ) {
						$strong_line_height    = 20 * $this->line_height_css;
						$strong_line_height_mm = $this->px_to_mm( $strong_line_height );

						$offset = ( $strong_line_height_mm - $this->line_height_mm ) / 2;

						// Top row 'strong' tag is 20px and 'p' tag is 16px, and we want to align them, so we need to move the 'p' down based on the line height difference
						$this->SetXY( $this->GetX(), $this->GetY() + $offset );
					}

					if ( 'strong' === $parent_tag && str_contains( $parent_class, 'smaller' ) ) {
						$normal_line_height    = 16 * $this->line_height_css;
						$normal_line_height_mm = $this->px_to_mm( $normal_line_height );
						$offset                = ( $this->line_height_mm - $normal_line_height_mm ) / 2;

						// Top row 'strong' tag is 20px and 'p' tag is 16px, and we want to align them, so we need to move the 'p' down based on the line height difference
						$this->SetXY( $this->GetX(), $this->GetY() - $offset );
					}
				} else {
					$this->set_size( $parent_tag, $parent_class );
					$this->set_style( $parent_tag, $parent_class );
				}

				$this->Write( $this->line_height_mm, $value );

				if ( $is_top_row ) {
					// Reset Y-coordinate
					$this->SetXY( $this->GetX(), $old_y );
				}
			}
		}
	}

	/**
	 * Write input data to PDF-file
	 *
	 * @param array $data Data
	 *
	 * @return void
	 */
	protected function write_input( array $data ): void {
		$type  = $data['attributes']['type'] ?? '';
		$label = $data['attributes']['label'] ?? '';
		$rows  = $data['attributes']['rows'] ?? 0;

		if ( 'text' === $type || 'number' === $type || 'textarea' === $type ) {
			$original_column_width   = $this->column_width;
			$original_column_start_x = $this->column_start_x;
			$original_column_end_x   = $this->column_end_x;
			$original_column_start_y = $this->column_start_y;
			$original_column_end_y   = $this->column_end_y;

			$value = $data['attributes']['value'] ?? '';

			if ( $label ) {
				// Line break so label and input are on different lines
				$this->Ln();
			}

			// Calculate how many lines the value takes
			$string_width = $this->GetStringWidth( $value );
			$lines        = ceil( $string_width / $this->column_width );
			$lines        = $lines > 1 ? $lines : 1;
			$lines_height = $this->line_height_mm * $lines;

			// Get the start point for the rectangle (after line break)
			$start_y = $this->GetY();

			$input_padding = $this->px_to_mm( 10 );

			$this->column_width = $this->column_width - $input_padding;

			// Set start point to the start of the column
			$this->SetX( $this->column_start_x + $input_padding );

			$this->column_start_x = $original_column_start_x + $input_padding;

			$this->SetXY( $this->GetX(), $start_y + $input_padding );

			$page_before = $this->PageNo();

			// Write value
			$this->Write( $this->line_height_mm, $value );

			$page_after = $this->PageNo();

			$rect_start_x = $original_column_start_x + 1.25;
			$rect_end_x   = $this->column_width - $this->row_padding;

			$rect_start_y = $start_y;
			$rect_end_y   = $this->line_height_mm * $lines + $input_padding * 2;

			if ( 'textarea' === $type ) {
				$rect_end_y = $this->line_height_mm * $rows + $input_padding * 2;
			}

			if ( $page_before !== $page_after ) {
				$rect_start_y = $this->tMargin;
			}

			// Draw rectangle around value
			$this->Rect(
				$rect_start_x,
				$rect_start_y,
				$rect_end_x,
				$rect_end_y,
				'D'
			);

			$this->SetY( $rect_end_y + $rect_start_y );

			$this->column_width   = $original_column_width;
			$this->column_start_x = $original_column_start_x;
			$this->column_end_x   = $original_column_end_x;
			$this->column_start_y = $original_column_start_y;
			$this->column_end_y   = $original_column_end_y;
		} elseif ( 'checkbox' === $type ) {
			$checked = $data['attributes']['checked'] ?? false;

			if ( is_string( $checked ) ) {
				$checked = $checked === 'true' ? true : false;
			}

			$fill = $checked ? 'F' : 'D';

			$checkbox_size       = $this->px_to_mm( 8 ); // Checkbox size in mm (8px x 8px)
			$checkbox_margin_top = $this->line_height_mm / 2 - $checkbox_size / 2;

			$this->Rect(
				$this->GetX(),
				$this->GetY() + $checkbox_margin_top,
				$checkbox_size,
				$checkbox_size,
				$fill,
			);

			$this->SetXY( $this->GetX() + $checkbox_size, $this->GetY() );
		}
	}

	/**
	 * Write label to PDF-file
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
					$grandchild_tag = $grandchild['tag'];

					if ( 'input' === $grandchild_tag ) {
						$this->write_input( $grandchild );
					}
				}
			} elseif ( 'span' === $tag ) {
				$this->write_text( $child );
			}
		}
	}

	/**
	 * Handle output
	 *
	 * @param array $data Data
	 * @param int   $col_index Column index
	 * @param int   $row_index Row index
	 *
	 * @return void
	 */
	protected function handle_output( array $data, int $col_index, int $row_index ): void {
		$tag      = ! empty( $data['tag'] ) ? sanitize_text_field( $data['tag'] ) : '';
		$class    = ! empty( $data['attributes']['class'] ) ? sanitize_text_field( $data['attributes']['class'] ) : '';
		$value    = ! empty( $data['value'] ) ? sanitize_text_field( $data['value'] ) : '';
		$children = ! empty( $data['children'] ) ? $data['children'] : array();

		if ( 'img' !== $tag && ! $value && ! $children ) {
			return;
		}

		if ( 'div' === $tag || 'figure' === $tag ) {
			if ( $children ) {
				foreach ( $children as $child ) {
					$this->handle_output( $child, $col_index, $row_index );
				}
			}
		} else {
			$content_end = $this->GetPageWidth() - $this->rMargin;
			$current_x   = $this->GetX();
			$current_y   = $this->GetY();

			$this->column_start_y = $current_y + $this->row_padding;

			if ( $this->last_row_index === $row_index ) { // Same row
				if ( $this->last_col_index !== $col_index ) { // Different column
					// If we are on the same row, but different column, we need to reset the column start x and end x
					$column_start_x = $current_x;

					// Column end point is the current x + the column width
					$column_end = $current_x + $this->column_width;

					// If the column end point is greater than the content end point, we need to reset the column start and end xes
					if ( $column_end > $content_end ) {
						$column_start_x = $this->lMargin;
						$column_end     = $column_start_x + $this->column_width;
					}

					$this->column_start_x = $column_start_x;
					$this->column_end_x   = $column_end;

					// If we have enough room on the row for the column, use the last column start y
					if ( round( $this->room_left, 3 ) >= round( $this->column_width, 3 ) ) {
						$this->column_start_y = $this->last_column_start_y;
					} else {
						$max_height = ! empty( $this->row_heights ) ? max( $this->row_heights ) : 0;
						$y          = $this->last_column_start_y + $max_height + $this->row_padding;

						$this->Ln();
						$current_y            = $this->GetY();
						$this->column_start_y = $y + $this->row_padding;

						$this->row_heights = array();
					}

					$this->last_column_start_y = $this->column_start_y;

					// Calculate room left on the row for the next column
					$this->room_left = $content_end - $this->column_end_x;
				} else { // Same column
					$this->column_start_y = $current_y;
					$this->column_start_x = $this->last_column_end_x + $this->row_padding;
				}
			} else { // Different row
				if ( 1 === $row_index ) {
					$this->column_start_y = $current_y;
				} else {
					$this->Ln();
					$current_y            = $this->GetY();
					$this->column_start_y = $current_y + $this->row_padding;
				}

				$this->column_start_y += $this->row_padding;

				// Top line
				$this->Line(
					$this->lMargin - $this->row_padding,
					$this->column_start_y - $this->row_padding,
					$this->GetPageWidth() - $this->rMargin + $this->row_padding,
					$this->column_start_y - $this->row_padding
				);

				$this->last_column_start_y = $this->column_start_y;
				$this->last_column_start_x = $this->column_start_x;

				$column_start_x = $current_x;

				// Column end point is the current x + the column width
				$column_end = $current_x + $this->column_width;

				// If the column end point is greater than the content end point, we need to reset the column start and end xes
				if ( $column_end > $content_end ) {
					$column_start_x = $this->lMargin;
					$column_end     = $column_start_x + $this->column_width;
				}

				$this->column_start_x = $column_start_x;
				$this->column_end_x   = $column_end;

				// Calculate room left on the row for the next column
				$this->room_left = $content_end - $this->column_end_x;

				$this->row_heights = array();
			}

			$this->SetXY( $this->column_start_x, $this->column_start_y );

			if ( 'ul' === $tag || 'ol' === $tag ) {
				$this->write_list( $data );
			} elseif ( 'img' === $tag ) {
				$this->write_image( $data );
			} elseif ( 'label' === $tag ) {
				$this->write_label( $data );
			} else {
				$this->write_text( $data );
			}

			$row_height = $this->GetY() - $this->column_start_y;

			$this->row_heights[] = $row_height;

			$this->last_column_end_x = $this->GetX();

			if ( $this->column_end_x >= $content_end ) {
				$this->SetX( $this->lMargin );
			} else {
				$this->SetX( $this->column_end_x );
			}

			$this->last_col_index = $col_index;
			$this->last_row_index = $row_index;
		}
	}
}
