<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

require_once get_template_directory() . '/includes/fpdf/fpdfa.php';

define( 'FPDF_FONTPATH', get_template_directory() . '/fonts' ); // phpcs:ignore

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
	 * Fontin koko
	 *
	 * @var int Fontin koko
	 */
	protected $font_size = 16;

	/**
	 * Fontin tyyli
	 *
	 * @var string Fontin tyyli (B = bold, I = italic, U = underline, tyhjä = normaali)
	 */
	protected $font_style = '';

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

		// Otetaan talteen nykyinen Y-koordinaatti (rivin alku)
		$y = $this->GetY();

		foreach ( $columns as $index => $column ) {
			// Siirretään X-koordinaattia sarakkeen leveyden verran
			if ( 0 === $index ) {
				$x = $this->lMargin;

				$this->SetXY( $x, $y );
			} else {
				$x = $this->GetX() + $column_width;

				$this->SetXY( $x, $y );
			}

			// Kirjoitetaan sarake
			$this->Cell( $column_width, 10, '', 1 );

			foreach ( $column as $column_children ) {
				$this->SetX( $x );

				$this->set_size( $column_children['tag'] );
				$this->set_style( $column_children['tag'] );

				foreach ( $column_children['children'] as $child ) {
					$this->set_style( $child['tag'] );

					// $this->Write( 20, $child['value'] );

					$this->Write( 5, 'test test test test' );

					$this->SetX( $x );
				}
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
				$this->font_size = 32;
				break;
			default:
				$this->font_size = 16;
				break;
		}

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
