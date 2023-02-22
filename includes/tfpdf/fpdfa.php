<?php
require 'tfpdf.php';

class FPDFA extends TFPDF {

	protected $n_colorprofile;
	protected $n_metadata;

	protected function _putcolorprofile() {
		$icc = file_get_contents( __DIR__ . '/sRGB2014.icc' );
		if ( ! $icc ) {
			$this->Error( 'Could not load the ICC profile' );
		}
		$this->_newobj();
		$this->n_colorprofile = $this->n;
		$this->_put( '<<' );
		$this->_put( '/Length ' . strlen( $icc ) );
		$this->_put( '/N 3' );
		$this->_put( '>>' );
		$this->_putstream( $icc );
		$this->_put( 'endobj' );
	}

	function _getxmpdescription( $prefix, $ns, $body ) {
		return sprintf( "\t<rdf:Description rdf:about=\"\" xmlns:%s=\"%s\">\n%s\t</rdf:Description>\n", $prefix, $ns, $body );
	}

	function _getxmpsimple( $tag, $value ) {
		$value = htmlspecialchars( $value, ENT_XML1, 'UTF-8' );
		return sprintf( "\t\t<%s>%s</%s>\n", $tag, $value, $tag );
	}

	function _getxmpseq( $tag, $value ) {
		$value = htmlspecialchars( $value, ENT_XML1, 'UTF-8' );
		return sprintf( "\t\t<%s>\n\t\t\t<rdf:Seq>\n\t\t\t\t<rdf:li>%s</rdf:li>\n\t\t\t</rdf:Seq>\n\t\t</%s>\n", $tag, $value, $tag );
	}

	function _getxmpalt( $tag, $value ) {
		$value = htmlspecialchars( $value, ENT_XML1, 'UTF-8' );
		return sprintf( "\t\t<%s>\n\t\t\t<rdf:Alt>\n\t\t\t\t<rdf:li xml:lang=\"x-default\">%s</rdf:li>\n\t\t\t</rdf:Alt>\n\t\t</%s>\n", $tag, $value, $tag );
	}

	function _putmetadata() {
		$pdf = $this->_getxmpsimple( 'pdf:Producer', $this->metadata['Producer'] );
		if ( isset( $this->metadata['Keywords'] ) ) {
			$pdf .= $this->_getxmpsimple( 'pdf:Keywords', $this->metadata['Keywords'] );
		}

		$date = @date( 'c', $this->CreationDate );
		$xmp  = $this->_getxmpsimple( 'xmp:CreateDate', $date );
		if ( isset( $this->metadata['Creator'] ) ) {
			$xmp .= $this->_getxmpsimple( 'xmp:CreatorTool', $this->metadata['Creator'] );
		}

		$dc = '';
		if ( isset( $this->metadata['Author'] ) ) {
			$dc .= $this->_getxmpseq( 'dc:creator', $this->metadata['Author'] );
		}
		if ( isset( $this->metadata['Title'] ) ) {
			$dc .= $this->_getxmpalt( 'dc:title', $this->metadata['Title'] );
		}
		if ( isset( $this->metadata['Subject'] ) ) {
			$dc .= $this->_getxmpalt( 'dc:description', $this->metadata['Subject'] );
		}

		$pdfaid  = $this->_getxmpsimple( 'pdfaid:part', '3' );
		$pdfaid .= $this->_getxmpsimple( 'pdfaid:conformance', 'B' );

		$s  = '<?xpacket begin="" id="W5M0MpCehiHzreSzNTczkc9d"?>' . "\n";
		$s .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";
		$s .= $this->_getxmpdescription( 'pdf', 'http://ns.adobe.com/pdf/1.3/', $pdf );
		$s .= $this->_getxmpdescription( 'xmp', 'http://ns.adobe.com/xap/1.0/', $xmp );
		if ( $dc ) {
			$s .= $this->_getxmpdescription( 'dc', 'http://purl.org/dc/elements/1.1/', $dc );
		}
		$s .= $this->_getxmpdescription( 'pdfaid', 'http://www.aiim.org/pdfa/ns/id/', $pdfaid );
		$s .= '</rdf:RDF>' . "\n";
		$s .= '<?xpacket end="r"?>';

		$this->_newobj();
		$this->n_metadata = $this->n;
		$this->_put( '<<' );
		$this->_put( '/Type /Metadata' );
		$this->_put( '/Subtype /XML' );
		$this->_put( '/Length ' . strlen( $s ) );
		$this->_put( '>>' );
		$this->_putstream( $s );
		$this->_put( 'endobj' );
	}

	function _putresources() {
		parent::_putresources();
		$this->_putcolorprofile();
		$this->_putmetadata();
	}

	function _putcatalog() {
		parent::_putcatalog();
		$oi  = '<</Type /OutputIntent /S /GTS_PDFA1 ';
		$oi .= '/OutputConditionIdentifier (sRGB2014.icc) /Info (sRGB2014.icc) /RegistryName (http://www.color.org) ';
		$oi .= '/DestOutputProfile ' . $this->n_colorprofile . ' 0 R>>';
		$this->_put( '/OutputIntents [' . $oi . ']' );
		$this->_put( '/Metadata ' . $this->n_metadata . ' 0 R' );
	}

	protected function _putheader() {
		$this->_put( '%PDF-1.4' );
		$this->_put( "%\xE2\xE3\xCF\xD3" );
	}

	function _puttrailer() {
		parent::_puttrailer();
		$id = uniqid();
		$this->_put( "/ID [($id)($id)]" );
	}

	function _enddoc() {
		if ( ! isset( $this->metadata['Producer'] ) ) {
			$this->Error( 'Unsupported FPDF version' );
		}
		foreach ( $this->fonts as $font ) {
			if ( $font['type'] == 'Core' ) {
				$this->Error( 'All fonts must be embedded in PDF/A' );
			}
		}
		$this->CreationDate = time();
		parent::_enddoc();
	}

	function Circle($x, $y, $r, $style='D') {
		$this->Ellipse($x,$y,$r,$r,$style);
	}

	function ClippingText($x, $y, $txt, $outline=false) {
        $op=$outline ? 5 : 7;
        $this->_out(sprintf('q BT %.2F %.2F Td %d Tr (%s) Tj ET',
            $x*$this->k,
            ($this->h-$y)*$this->k,
            $op,
            $this->_escape($txt)));
    }

    function ClippingRect($x, $y, $w, $h, $outline=false) {
        $op=$outline ? 'S' : 'n';
        $this->_out(sprintf('q %.2F %.2F %.2F %.2F re W %s',
            $x*$this->k,
            ($this->h-$y)*$this->k,
            $w*$this->k,-$h*$this->k,
            $op));
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function ClippingRoundedRect($x, $y, $w, $h, $r, $outline=false) {
        $k = $this->k;
        $hp = $this->h;
        $op=$outline ? 'S' : 'n';
        $MyArc = 4/3 * (sqrt(2) - 1);

        $this->_out(sprintf('q %.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out(' W '.$op);
    }

    function ClippingEllipse($x, $y, $rx, $ry, $outline=false) {
        $op=$outline ? 'S' : 'n';
        $lx=4/3*(M_SQRT2-1)*$rx;
        $ly=4/3*(M_SQRT2-1)*$ry;
        $k=$this->k;
        $h=$this->h;
        $this->_out(sprintf('q %.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x+$rx)*$k,($h-$y)*$k,
            ($x+$rx)*$k,($h-($y-$ly))*$k,
            ($x+$lx)*$k,($h-($y-$ry))*$k,
            $x*$k,($h-($y-$ry))*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x-$lx)*$k,($h-($y-$ry))*$k,
            ($x-$rx)*$k,($h-($y-$ly))*$k,
            ($x-$rx)*$k,($h-$y)*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x-$rx)*$k,($h-($y+$ly))*$k,
            ($x-$lx)*$k,($h-($y+$ry))*$k,
            $x*$k,($h-($y+$ry))*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c W %s',
            ($x+$lx)*$k,($h-($y+$ry))*$k,
            ($x+$rx)*$k,($h-($y+$ly))*$k,
            ($x+$rx)*$k,($h-$y)*$k,
            $op));
    }

    function ClippingCircle($x, $y, $r, $outline=false){
        $this->ClippingEllipse($x, $y, $r, $r, $outline);
    }

    function ClippingPolygon($points, $outline=false){
        $op=$outline ? 'S' : 'n';
        $h = $this->h;
        $k = $this->k;
        $points_string = '';
        for($i=0; $i<count($points); $i+=2){
            $points_string .= sprintf('%.2F %.2F', $points[$i]*$k, ($h-$points[$i+1])*$k);
            if($i==0)
                $points_string .= ' m ';
            else
                $points_string .= ' l ';
        }
        $this->_out('q '.$points_string . 'h W '.$op);
    }

    function UnsetClipping(){
        $this->_out('Q');
    }

    function ClippedCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
        if($border || $fill || $this->y+$h>$this->PageBreakTrigger)
        {
            $this->Cell($w,$h,'',$border,0,'',$fill);
            $this->x-=$w;
        }
        $this->ClippingRect($this->x,$this->y,$w,$h);
        $this->Cell($w,$h,$txt,'',$ln,$align,false,$link);
        $this->UnsetClipping();
    }

	function Ellipse($x, $y, $rx, $ry, $style='D'){
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='B';
		else
			$op='S';
		$lx=4/3*(M_SQRT2-1)*$rx;
		$ly=4/3*(M_SQRT2-1)*$ry;
		$k=$this->k;
		$h=$this->h;
		$this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
			($x+$rx)*$k,($h-$y)*$k,
			($x+$rx)*$k,($h-($y-$ly))*$k,
			($x+$lx)*$k,($h-($y-$ry))*$k,
			$x*$k,($h-($y-$ry))*$k));
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
			($x-$lx)*$k,($h-($y-$ry))*$k,
			($x-$rx)*$k,($h-($y-$ly))*$k,
			($x-$rx)*$k,($h-$y)*$k));
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
			($x-$rx)*$k,($h-($y+$ly))*$k,
			($x-$lx)*$k,($h-($y+$ry))*$k,
			$x*$k,($h-($y+$ry))*$k));
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
			($x+$lx)*$k,($h-($y+$ry))*$k,
			($x+$rx)*$k,($h-($y+$ly))*$k,
			($x+$rx)*$k,($h-$y)*$k,
			$op));
	}
}
