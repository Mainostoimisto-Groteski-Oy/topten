<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

require_once get_template_directory() . '/includes/fpdf/fpdfa.php';

define( 'FPDF_FONTPATH', get_template_directory() . '/fonts/fpdf' ); // phpcs:ignore

/**
 * Class createPDF
 *
 * Perustuu http://www.fpdf.org/en/script/script53.php
 */
class Topten_PDF extends FPDFA {
	/**
	 * Sallitut tagit
	 *
	 * @var string[]
	 */
	protected $allowed_tags = array(
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
	 * Kielletyt tagit
	 *
	 * @var array
	 */
	protected $disallowed_values = array(
		'\n',
		'\t',
	);

	/**
	 * Fontin koko
	 *
	 * @var int Fontin koko (px)
	 */
	protected $font_size = 16;

	/**
	 * Fontin tyyli
	 *
	 * @var string Fontin tyyli (B = bold, I = italic, U = underline, tyhjä = normaali)
	 */
	protected $font_style = '';

	/**
	 * Line height (px) (1.5 * font size), asetetaan set_size-metodissa
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
	 * Muuttaa HTML-koodin PDF-tiedostoksi
	 *
	 * @param array $data HTML-koodi
	 */
	public function generate_pdf( $data ) {
		// Luodaan PDF
		$this->AddFont( 'Roboto', '', 'Roboto-Regular.php' );
		$this->AddFont( 'Roboto', 'B', 'Roboto-Bold.php' );
		$this->AddFont( 'Roboto', 'I', 'Roboto-Italic.php' );

		$this->SetFont( 'Roboto', $this->font_style, $this->font_size );

		$this->AddPage();

		foreach ( $data['rows'] as $index => $row ) {
			$this->write_columns( $row );
		}
	}

	/**
	 * Muuttaa pikselit millimetreiksi
	 *
	 * @param int $px Pikselit
	 */
	private function px_to_mm( $px ) {
		return $px * 0.264583333;
	}

	/**
	 * Luodaan sarakkeet
	 *
	 * @param array $row Rivi
	 */
	private function write_columns( $row ) {
		// Sarakkeiden määrä
		$columns = $row['columns'] ?? array();
		$count   = count( $columns );

		// Lasketaan yksittäisen sarakkeen leveys (sivun leveys - vasen ja oikea marginaali / sarakkeiden määrä)
		$column_width = ( $this->GetPageWidth() - $this->lMargin - $this->rMargin ) / $count;

		// Sarakkeen alkupiste (Y-akseli)
		$y = $this->GetY();

		// Kerätään sarakkeiden korkeudet, jotta voidaan määrittää sarakkeen reunusten korkeus
		$column_height = array();

		// Rivin sarakkeet
		foreach ( $columns as $index => $column ) {
			// Sarakkeen alkupiste (X-akseli)
			$x = $this->lMargin + ( $column_width * $index );

			$this->SetXY( $x, $y );

			// Muuttuja sarakkeen korkeuden laskemista varten
			$column_height = 0;

			// Sarakkaeen lapsielementit
			foreach ( $column as $column_children ) {
				$tag = $column_children['tag'];

				// Asetetaan parentin tagi
				$this->set_size( $tag );
				$this->set_style( $tag );

				// Siirretään Y-koordinaattia rivin korkeuden verran, otetaan aluksi talteen nykyinen pos
				$child_y_position = $this->GetY();

				// Jos tagi on lista, ei lisätä rivin korkeutta, koska se lisätään myöhemmin
				if ( 'ul' === $tag || 'ol' === $tag ) {
					$column_child_height = 0;

					$this->write_list( $column_children, $column_child_height );
				} elseif ( 'img' === $tag || 'picture' === $tag ) {
					$this->write_image( $column_children );
				} else {
					$column_child_height = $this->line_height_mm;

					$this->write_text( $column_children );
				}

				$column_height += $column_child_height;

				// Asetetaan XY-koordinaatit seuraavan lapsen alkuun. X-koordinaatti ei muutu, Y-koordinaattiin lisätään rivin korkeuden verran
				$next_child_position = $child_y_position + $column_child_height;

				$this->SetXY( $x, $next_child_position );
			}

			$column_heights[] = $column_height;
		}

		// Kirjoitetaan sarakkeen reunat
		foreach ( $columns as $index => $column ) {
			// Sarakkeen alkupiste (X-akseli)
			$x = $this->lMargin + ( $column_width * $index );

			// Asetetaan alkupiste
			$this->SetXY( $x, $y );

			// Kirjoitetaan sarake, korkeus = suurin sarakkeen korkeus
			$this->Cell( $column_width, max( $column_heights ), '', 1 );
		}
	}

	/**
	 * Kirjoittaa kuvan
	 *
	 * @param array $data Data, jossa on tagi ja arvo (esim. array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 */
	private function write_image( $data ) {
		$tag = $data['tag'];
	}

	/**
	 * Kirjoittaa listan
	 *
	 * @param array $data Data, jossa on tagi ja arvo (esim. array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 * @param int   $column_height Sarakkeen korkeus
	 */
	private function write_list( $data, &$column_height ) {
		$list_type = $data['tag'];

		foreach ( $data['children'] as $datum ) {
			$tag = sanitize_text_field( $datum['tag'] );

			if ( 'li' !== $tag ) {
				continue;
			}

			// Jos arvo ei ole sallittu (kuten \n), poistetaan se
			$value = str_replace( $this->disallowed_values, '', $datum['value'] );

			$this->set_style( $tag );

			// Todo: Lisää numeroitu lista
			if ( 'ol' === $list_type ) {
				// Jos listan tyyppi on numeroitu, lisätään numero
				$char = '1.'; // todo
			} else {
				// Muuten lisätään bullet
				$char = chr( 149 );
			}

			$this->Write( $this->line_height_mm, $char . ' ' );

			$this->Write( $this->line_height_mm, sanitize_text_field( $value ) );

			$this->Ln();

			$column_height += $this->line_height_mm;
		}
	}

	/**
	 * Kirjoittaa dataa PDF-tiedostoon
	 *
	 * @param array $data Data, jossa on tagi ja arvo (esim. array( 'tag' => 'h1', 'value' => 'Otsikko' )
	 */
	private function write_text( $data ) {
		// Lapsielementtien määrä
		$child_count = count( $data['children'] );

		foreach ( $data['children'] as $index => $datum ) {
			// Jos lapsi ei ole viimeinen, lisätään välilyönti, jotta sanat eivät liity toisiinsa
			$space = $index < $child_count - 1 ? true : false;

			// Jos arvo ei ole sallittu (kuten \n), poistetaan se
			$value = str_replace( $this->disallowed_values, '', $datum['value'] );
			$tag   = sanitize_text_field( $datum['tag'] );

			$this->set_style( $tag );

			$this->Write( $this->line_height_mm, sanitize_text_field( $value ) );

			if ( $space ) {
				$this->Write( $this->line_height_mm, ' ' );
			}
		}
	}

	/**
	 * Asettaa fontin tagin mukaan
	 *
	 * @param string $tag Tagi
	 */
	private function set_size( $tag ) {
		switch ( $tag ) {
			case 'h2':
				$this->font_size = 24;
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
	 * Asettaa fontin tyylin tagin mukaan
	 *
	 * @param string $tag Tagi
	 */
	private function set_style( $tag ) {
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
