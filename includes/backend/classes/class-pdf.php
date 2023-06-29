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
	 * Line height (px) (1.5 * font size), set in set_size-method
	 *
	 * @var float Line height
	 */
	protected float $line_height = 24;

	/**
	 * Line height (mm)
	 *
	 * @var float Line height
	 */
	protected float $line_height_mm = 6.35;

	/**
	 * Row padding (mm)
	 *
	 * @var float Row padding (mm)
	 */
	protected float $row_padding = 3.96875;

	/**
	 * Row padding (px)
	 *
	 * @var float Row padding (<px></px>)
	 */
	protected float $row_padding_px = 15;

	/**
	 * Current column width
	 *
	 * @var float Current column width
	 */
	protected float $column_width = 0;

	/**
	 * Convert px to mm
	 *
	 * @param int|float $px Pixels
	 *
	 * @return float Pixels converted to mm
	 */
	private function px_to_mm( int|float $px ): float {
		return (float) $px * 0.264583333;
	}

	/**
	 * Convert mm to px
	 *
	 * @param int|float $mm Millimeters
	 *
	 * @return float Millimeters converted to px
	 */
	private function mm_to_px( int|float $mm ): float {
		return (float) $mm * 3.779527559;
	}

	/**
	 * Convert px to pt
	 *
	 * @param int|float $px Pixels
	 *
	 * @return float Pixels converted to points
	 */
	private function px_to_pt( int|float $px ): float {
		return (float) $px * 0.75;
	}

	/**
	 * Set font size by tag
	 *
	 * @param string $tag Element tag (for example h1, h2, h3, p)
	 * @param string $class Element class
	 *
	 * @return void
	 */
	private function set_size( string $tag, string $class = '' ): void {
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
			default:
				$this->font_size = 16;
		}

		$this->font_size_pt = $this->px_to_pt( $this->font_size );

		$this->line_height    = $this->font_size * 1.5;
		$this->line_height_mm = $this->px_to_mm( $this->line_height );

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
	private function set_style( string $tag, string $class = '' ): void {
		switch ( $tag ) {
			case 'b':
			case 'strong':
				$this->font_style = 'B';
				break;
			case 'i':
			case 'em':
				// TODO: ITALICS NOT WORKING
				// $this->font_style = 'I';
				$this->font_style = '';
				break;
			default:
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
	 * Get string width (mm)
	 *
	 * @param array $data Data array
	 * @param bool  $is_top_row Is top row?
	 * @param float $width Width for recursive calls
	 *
	 * @return float String width (mm)
	 */
	private function get_string_width( array $data, bool $is_top_row = false, float &$width = 0 ): float {
		if ( ! isset( $data['children'] ) ) {
			return 0;
		}

		$child_count = count( $data['children'] );

		$parent_tag   = sanitize_text_field( $data['tag'] );
		$parent_class = $data['attributes']['class'] ?? '';

		foreach ( $data['children'] as $index => $datum ) {
			if ( ! empty( $datum['children'] ) ) {
				$this->get_string_width( $datum, $is_top_row, $width );
			} else {
				// If child is not last, add space, so words don't stick together
				$space = $index < $child_count - 1 ? true : false;

				// If value is not allowed (like \n), remove it
				$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

				$this->set_style( $parent_tag );

				if ( $is_top_row ) {
					$this->set_size( $parent_tag, $parent_class . ' top_row' );

				}

				$string = sanitize_text_field( $value );

				if ( $space ) {
					$string .= ' ';
				}

				$width += $this->GetStringWidth( $string );
			}
		}

		return $width;
	}

	/**
	 * Converts HTML to PDF
	 *
	 * @param array $data HTML data
	 *
	 * @return void
	 */
	public function generate_pdf( array $data ): void {
		$this->AddFont( 'Blinker', '', 'Blinker-Regular.ttf', true );
		$this->AddFont( 'Blinker', 'B', 'Blinker-Bold.ttf', true );

		// Todo: italic font is not working, Blinker-Italic does not exist
		// $this->AddFont( 'Blinker', 'I', 'Blinker-Regular.ttf', true );

		$this->SetFont( 'Blinker', $this->font_style, $this->font_size_pt );

		$this->AddPage();

		foreach ( $data['rows'] as $index => $row ) {
			$last_row = count( $data['rows'] ) - 1 === $index;

			$this->write_columns( $row, $last_row );
		}
	}

	/**
	 * Create columns
	 *
	 * @param array $row Row data
	 * @param bool  $last_row Is last row?
	 *
	 * @return void
	 */
	private function write_columns( array $row, bool $last_row = false ): void {
		// Columns count
		$columns = $row['columns'] ?? array();
		$count   = $row['count'] ?? 0;

		$row_class = $row['attributes']['class'] ?? '';

		$column_heights = array();

		// If class includes "top", it is topmost row and is a special case
		if ( str_contains( $row_class, 'top' ) ) {
			$this->write_top_row( $row );

			$this->SetY( $this->GetY() + $this->row_padding * 2 );
		} else {
			// Calculate column width (page width - left and right margin / columns count)
			$this->column_width = ( $this->GetPageWidth() - $this->lMargin - $this->rMargin ) / $count;

			// Column start point (Y-axis)
			$y = $this->GetY();

			// Row columns
			for ( $i = 0; $i < $count; $i++ ) {
				$column = $columns[ $i ] ?? array();

				// Column start point (X-axis)
				$x = $this->lMargin + ( $this->column_width * $i );

				$this->SetXY( $x, $y );

				$column_height = 0;

				// Column children
				foreach ( $column as $column_children ) {
					$column_height += $this->handle_output( $column_children );
				}

				$column_heights[] = $column_height;
			}

			$max_column_height = max( $column_heights );

			$x_after_columns = $this->GetX();
			$y_after_columns = $this->GetY();

			// Write column borders
			for ( $i = 0; $i < $count; $i++ ) {
				// Add borders to top and right, and if it's first column, also add left border
				$borders = 0 === $i ? 'TRL' : 'TR';

				$last_row ? $borders .= 'B' : '';

				$x = $this->lMargin + ( $this->column_width * $i );

				// Set start point
				$this->SetXY( $x, $y - $this->row_padding );

				// Write column, height = highest column height
				$this->Cell( $this->column_width, $max_column_height + $this->row_padding, '', $borders );

				$this->SetXY( $x, $y );
			}

			// Set X-axis to after columns
			$this->SetXY( $x_after_columns, $y_after_columns + $this->row_padding );
		}
	}

	/**
	 * Handle top row
	 *
	 * @param array $row Row data
	 *
	 * @return void
	 */
	private function write_top_row( array $row ): void {
		$columns = $row['columns'] ?? array();
		$count   = $row['count'] ?? 0;

		$this->column_width = ( $this->GetPageWidth() - $this->lMargin - $this->rMargin ) / $count;

		$original_x = $this->GetX();
		$original_y = $this->GetY();

		for ( $i = 0; $i < $count; $i++ ) {
			$column = $columns[ $i ] ?? array();

			$x = $this->lMargin + ( $this->column_width * $i );

			$this->SetXY( $x, $original_y );

			// We want to align top row text column to right, so we need to get the width of the widest string in the column
			$string_widths = array();

			foreach ( $column as $column_children ) {
				$tag = $column_children['tag'];

				if ( 'div' === $tag ) {
					$string_width = $this->get_string_width( $column_children, true );

					$string_widths[] = $string_width;
				}
			}

			if ( ! empty( $string_widths ) ) {
				$max_string_width = max( $string_widths );
			} else {
				$max_string_width = 0;
			}

			foreach ( $column as $column_children ) {
				$tag   = $column_children['tag'];
				$class = $column_children['attributes']['class'] ?? '';

				// Set parent tag
				$this->set_size( $tag, $class );
				$this->set_style( $tag );

				// Move Y-axis by line height, save current position
				$child_y_position = $this->GetY();

				if ( 'img' === $tag ) {
					$this->write_image( $column_children );

					$img_width = $column_children['attributes']['width'] ?? 0;
					$img_width = $this->px_to_mm( $img_width );

					$this->SetXY( $x + $img_width, $original_y );
				} elseif ( 'div' === $tag ) {
					$x = $this->GetPageWidth() - $this->lMargin - $this->rMargin - $max_string_width;

					$this->SetX( $x );

					foreach ( $column_children['children'] as $children ) {
						$this->write_text( $children, true, true );
					}

					// Set XY-coordinates to next child start. X-coordinate doesn't change, Y-coordinate is increased by line height
					$next_child_position = $child_y_position + $this->line_height_mm;

					$this->SetXY( $x, $next_child_position );
				}
			}
		}

		// $this->SetXY( $x, $next_child_position );
	}

	/**
	 * Write image
	 *
	 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 *
	 * @return void
	 */
	private function write_image( array $data ): void {
		$src    = $data['attributes']['src'] ?? '';
		$alt    = $data['attributes']['alt'] ?? '';
		$width  = $data['attributes']['width'] ?? '';
		$height = $data['attributes']['height'] ?? '';

		$width  = $this->px_to_mm( $width );
		$height = $this->px_to_mm( $height );

		$this->Image( $src, $this->GetX(), $this->GetY(), $width, $height );
	}

	/**
	 * Write list
	 *
	 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param int   $column_height Column height
	 *
	 * @return void
	 */
	private function write_list( array $data, int &$column_height ): void {
		$list_type = $data['tag'];

		foreach ( $data['children'] as $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'li' !== $tag ) {
				continue;
			}

			// If value is not allowed (like \n), remove it
			$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

			$this->set_style( $tag );

			// Todo: Add ordered list
			if ( 'ol' === $list_type ) {
				// If list type is ordered, add number
				$char = '1.'; // todo
			} else {
				// Otherwise add bullet
				$char = chr( 149 );
			}

			$this->Write( $this->line_height_mm, $char . ' ' );
			$this->Write( $this->line_height_mm, sanitize_text_field( $value ) );

			$this->Ln();

			$column_height += $this->line_height_mm;
		}
	}

	/**
	 * Write data to PDF-file
	 *
	 * @param array $data Data, containing tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param bool  $uppercase Should text output be in UPPERCASE?
	 * @param bool  $is_top_row Is this top row?
	 */
	private function write_text( array $data, bool $uppercase = false, bool $is_top_row = false ): void {
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
				/*
				 * If parent tag is div and there is no children, don't write anything
				 * WYSIWYG doesn't add div-tags, so the only div tags are coming from our code
				 */
				if ( 'div' === $parent_tag ) {
					continue;
				}

				// If child is not last, add space so words don't stick together
				$space = $index < $child_count - 1 ? true : false;

				// If value is not allowed (like \n), remove it
				$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

				$this->set_style( $parent_tag );

				$value = sanitize_text_field( $value );
				$value = $uppercase ? strtoupper( $value ) : $value;

				if ( $is_top_row ) {
					$this->set_size( $parent_tag, $parent_class . ' top_row' );

					$old_y = $this->GetY();

					if ( 'p' === $parent_tag && ! str_contains( $parent_class, 'date-title' ) ) {
						$strong_line_height    = 20 * 1.5;
						$strong_line_height_mm = $this->px_to_mm( $strong_line_height );

						$offset = ( $strong_line_height_mm - $this->line_height_mm ) / 2;

						// Top row 'strong' tag is 20px and 'p' tag is 16px, and we want to align them, so we need to move the 'p' down based on the line height difference
						$this->SetXY( $this->GetX(), $this->GetY() + $offset );
					}

					if ( 'strong' === $parent_tag && str_contains( $parent_class, 'smaller' ) ) {
						$strong_line_height    = 12 * 1.5;
						$strong_line_height_mm = $this->px_to_mm( $strong_line_height );

						$offset = ( $strong_line_height_mm - $this->line_height_mm ) / 2;

						// Top row 'strong' tag is 20px and 'p' tag is 16px, and we want to align them, so we need to move the 'p' down based on the line height difference
						$this->SetXY( $this->GetX(), $this->GetY() - $offset );
					}
				} else {
					$this->set_size( $parent_tag, $parent_class );
					$this->set_style( $parent_tag, $parent_class );
				}

				$this->Write( $this->line_height_mm, $value );

				if ( $space ) {
					$this->Write( $this->line_height_mm, ' ' );
				}

				if ( $is_top_row ) {
					// Reset Y-coordinate
					$this->SetXY( $this->GetX(), $old_y );
				}
			}
		}
	}

	/**
	 * Handle output
	 *
	 * @param array $data Data
	 *
	 * @return float Column height
	 */
	private function handle_output( array $data ): float {
		$tag   = ! empty( $data['tag'] ) ? sanitize_text_field( $data['tag'] ) : '';
		$class = ! empty( $data['attributes']['class'] ) ? sanitize_text_field( $data['attributes']['class'] ) : '';

		// error_log( $class . ' - ' . $tag );

		$column_height = 0;

		if ( 'div' === $tag ) {
			if ( ! empty( $data['children'] ) ) {
				foreach ( $data['children'] as $child ) {
					return $this->handle_output( $child );
				}
			}
		} else {
			$child_y_position = $this->GetY();
			$child_x_position = $this->GetX();

			if ( 'ul' === $tag || 'ol' === $tag ) { // phpcs:ignore
				// $this->write_list( $data, $column_child_height );
			} elseif ( 'img' === $tag || 'picture' === $tag ) { // phpcs:ignore
				$this->write_image( $data );
			} else {
				$this->write_text( $data );
			}

			// Get total string width
			$string_width = $this->get_string_width( $data );

			// How many lines string takes
			$lines = ceil( $string_width / $this->column_width );

			// Calculate column child height
			$column_height = $this->line_height_mm * $lines;

			$next_child_position = $child_y_position + $column_height;

			$this->SetXY( $child_x_position, $next_child_position );
		}

		return $column_height;
	}
}
