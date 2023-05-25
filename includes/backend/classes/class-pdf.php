<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

require_once get_template_directory() . '/includes/tfpdf/fpdfa.php';

define( 'FPDF_FONTPATH', get_template_directory() . '/fonts/tfpdf' ); // phpcs:ignore

/**
 * Class createPDF
 *
 * Based on http://www.fpdf.org/en/script/script53.php
 */
class Topten_PDF extends FPDFA {
	/**
	 * Whitelisted tags
	 *
	 * @var string[]
	 */
	protected $tag_whitelist = array(
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
	 * @var array
	 */
	protected $tag_blacklist = array(
		'\n',
		'\t',
	);

	/**
	 * Font size (px)
	 *
	 * @var int Font size (px)
	 */
	protected $font_size = 16;

	/**
	 * Font style
	 *
	 * @var string Font style (B = bold, I = italic, U = underline, empty = normal)
	 */
	protected $font_style = '';

	/**
	 * Line height (px) (1.5 * font size), set in set_size-method
	 *
	 * @var int Line height
	 */
	protected $line_height = 24;

	/**
	 * Line height (mm)
	 *
	 * @var int Line height
	 */
	protected $line_height_mm = 6.35;

	/**
	 * Row padding (mm)
	 *
	 * @var int Row padding
	 */
	protected $row_padding = 2;

	/**
	 * Current column width
	 *
	 * @var int Current column width
	 */
	protected $column_width = 0;

	/**
	 * Converts HTML to PDF
	 *
	 * @param array $data HTML data
	 */
	public function generate_pdf( $data ) {
		// $this->AddFont( 'Roboto', '', 'Roboto-Regular.php' );
		// $this->AddFont( 'Roboto', 'B', 'Roboto-Bold.php' );
		// $this->AddFont( 'Roboto', 'I', 'Roboto-Italic.php' );

		$this->AddFont( 'Roboto', '', 'Roboto-Regular.ttf', true );
		$this->AddFont( 'Roboto', 'B', 'Roboto-Bold.ttf', true );
		$this->AddFont( 'Roboto', 'I', 'Roboto-Italic.ttf', true );

		$this->SetFont( 'Roboto', $this->font_style, $this->font_size );

		$this->AddPage();

		foreach ( $data['rows'] as $index => $row ) {
			if ( $index > 0 ) {
				break;
			}

			$this->write_columns( $row );
		}
	}

	/**
	 * Convert px to mm
	 *
	 * @param int $px Pikselit
	 */
	private function px_to_mm( $px ) {
		return $px * 0.264583333;
	}

	/**
	 * Get string width (mm)
	 *
	 * @param array $data Data
	 */
	private function get_string_width( $data ) {
		if ( ! isset( $data['children'] ) ) {
			return 0;
		}

		$width       = 0;
		$child_count = count( $data['children'] );

		foreach ( $data['children'] as $index => $datum ) {
			// If child is not last, add space, so words don't stick together
			$space = $index < $child_count - 1 ? true : false;

			// If value is not allowed (like \n), remove it
			$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

			$tag = sanitize_text_field( $datum['tag'] );

			$this->set_style( $tag );

			$string = sanitize_text_field( $value );

			if ( $space ) {
				$string .= ' ';
			}

			$width += $this->GetStringWidth( $string );
		}

		return $width;
	}

	/**
	 * Create columns
	 *
	 * @param array $row Row data
	 */
	private function write_columns( $row ) {
		// Columns count
		$columns = $row['columns'] ?? array();
		$count   = $row['count'] ?? 0;

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

			// Column children
			foreach ( $column as $column_children ) {
				$tag = $column_children['tag'];

				// Set parent tag
				$this->set_size( $tag );
				$this->set_style( $tag );

				// Move Y-axis by line height, save current position
				$child_y_position = $this->GetY();

				// If tag is list, don't add line height, because it will be added later
				if ( 'ul' === $tag || 'ol' === $tag ) {
					// $this->write_list( $column_children, $column_child_height );
				} elseif ( 'img' === $tag || 'picture' === $tag ) {
					// $this->write_image( $column_children );
				} else {
					// Get total string width
					$string_width = $this->get_string_width( $column_children );

					// How many lines string takes
					$lines = $column_width / $string_width;

					// Calculate column child height
					$column_height = $this->line_height_mm * $lines;

					$this->write_text( $column_children );
				}

				// Set XY-coordinates to next child start. X-coordinate doesn't change, Y-coordinate is increased by line height
				$next_child_position = $child_y_position + $column_height;

				$this->SetXY( $x, $next_child_position );

				$this->handle_column_children( $column, $x );
			}

			// $max_column_height = max( $column_heights );

			// // Write column borders
			// for ( $i = 0; $i < $count; $i++ ) {
			// Add borders to top and right, and if it's first column, also add left border
			// $borders = 0 === $i ? 'TRL' : 'TR';

			// $x = $this->lMargin + ( $this->column_width * $i );

			// Set start point
			// $this->SetXY( $x, $y - $this->row_padding );

			// Write column, height = highest column height
			// $this->Cell( $this->column_width, $max_column_height, '', $borders );
			// }

			// Set Y-axis to highest column height
			// $this->SetXY( $this->lMargin, $y + $max_column_height );
			$this->SetXY( $this->lMargin, $y + $this->line_height_mm );
		}
	}

	/**
	 * Handle column children
	 *
	 * @param array $column Column data
	 */
	private function handle_column_children( $column, $x ) {
		// Column children
		foreach ( $column as $column_children ) {
			json_log( $column_children );

			// if ( isset( $column_children['children'] ) && $column_children['children'] ) {
			// foreach ( $column_children['children'] as $child ) {
			// $this->handle_column_children( $child );
			// }
			// } else {
			$tag   = $column_children['tag'] ?? '';
			$class = $column_children['attributes']['class'] ?? '';

			// Set parent tag
			$this->set_size( $tag, $class );
			$this->set_style( $tag );

			// Move Y-axis by line height, save current position
			$child_y_position = $this->GetY();

			// If tag is list, don't add line height, because it will be added later
			// if ( 'ul' === $tag || 'ol' === $tag ) {
			// $this->write_list( $column_children, $column_child_height );
			// } elseif ( 'img' === $tag || 'picture' === $tag ) {
			// $this->write_image( $column_children );
			// } else {
			// Get total string width
			$string_width = $this->get_string_width( $column_children );

			// How many lines string takes
			$lines = ceil( $string_width / $this->column_width );

			// Calculate column child height
			$column_height = ( $this->line_height_mm * $lines ) + ( $this->row_padding * 2 );

			$this->write_text( $column_children );
			// }

			// Set XY-coordinates to next child start. X-coordinate doesn't change, Y-coordinate is increased by line height
			$next_child_position = $child_y_position + $column_height;

			$this->SetXY( $x, $next_child_position );

			// $column_heights[] = $column_height;
			// }

			// return $column_heights;
		}
	}

		/**
		 * Write image
		 *
		 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
		 */
	private function write_image( $data ) {
		$tag = $data['tag'];
	}

		/**
		 * Write list
		 *
		 * @param array $data Data, where is tag and value (for example array( 'tag' => 'h1', 'value' => 'Otsikko' )
		 * @param int   $column_height Column height
		 */
	private function write_list( $data, &$column_height ) {
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
		 */
	private function write_text( $data ) {
		if ( ! isset( $data['children'] ) ) {
			return;
		}

		// Children count
		$child_count = count( $data['children'] );

		foreach ( $data['children'] as $index => $datum ) {
			// If child is not last, add space, so words don't stick together
			$space = $index < $child_count - 1 ? true : false;

			// If value is not allowed (like \n), remove it
			$value = str_replace( $this->tag_blacklist, '', $datum['value'] );

			$tag = sanitize_text_field( $datum['tag'] );

			$this->set_style( $tag );

			$this->Write( $this->line_height_mm, sanitize_text_field( $value ) );

			if ( $space ) {
				$this->Write( $this->line_height_mm, ' ' );
			}
		}
	}

		/**
		 * Set font size by tag
		 *
		 * @param string $tag Element tag (for example h1, h2, h3, p)
		 * @param string $class Element class
		 */
	private function set_size( string $tag, string $class = '' ) {
		switch ( $tag ) {
			case 'h1':
				$this->font_size = 32;
				break;
			case 'h2':
				if ( str_contains( $class, 'desc' ) ) {
					$this->font_size = 12;
				} else {
					$this->font_size = 24;
				}

				break;
			default:
				$this->font_size = 12;
				break;
		}

		$this->line_height    = $this->font_size * 1.5;
		$this->line_height_mm = $this->px_to_mm( $this->line_height );

		$this->SetFont( 'Roboto', $this->font_style, $this->font_size );
	}

	/**
	 * Set font style by tag
	 *
	 * @param string $tag Tag
	 */
	private function set_style( string $tag ) {
		switch ( $tag ) {
			case 'b':
			case 'strong':
				$this->font_style = 'B';
				break;
			case 'i':
			case 'em':
				$this->font_style = 'I';
				break;
			default:
				$this->font_style = '';
				break;
		}

		$this->SetFont( 'Roboto', $this->font_style, $this->font_size );
	}
}
