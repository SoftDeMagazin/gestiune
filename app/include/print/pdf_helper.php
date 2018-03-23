<?php
class PdfHelper {
	var $pdf;
	
	function antetSocietateHtml($societate_id) {
		if(get_class($societate_id) == "Societati") {
			$societate = $societate_id;
		} else {
			$societate = new Societati($societate_id);
		}
		return '<div>'. $societate -> denumire .'		
				, '. $societate -> cod_fiscal .'		
				, '. $societate -> reg_com .'
				, '. $societate -> capital_social .'
				</div>
				<hr>
				';
	} 
	
	function pdfProperties() {
		$this -> pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		$this -> pdf->setPrintHeader(false);
		$this -> pdf->setPrintFooter(false);
		
		// set default monospaced font
		$this -> pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$this -> pdf->SetMargins(5, 5, 5);
		
		//set auto page breaks
		$this -> pdf->SetAutoPageBreak(FALSE, 5);
		
		//set image scale factor
		$this -> pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
		
		//set some language-dependent strings
		$this -> pdf->setLanguageArray($l); 
		
		// ---------------------------------------------------------
		
		// set font
		$this -> pdf->SetFont(PDF_FONT, '', 10);
	}
	
	function pdfPropertiesLandscape() {
		$this -> pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
		$this -> pdf->setPrintHeader(false);
		$this -> pdf->setPrintFooter(false);
		
		// set default monospaced font
		$this -> pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$this -> pdf->SetMargins(5, 5, 5);
		
		//set auto page breaks
		$this -> pdf->SetAutoPageBreak(TRUE, 5);
		
		//set image scale factor
		$this -> pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
		
		//set some language-dependent strings
		$this -> pdf->setLanguageArray($l); 
		
		// ---------------------------------------------------------
		
		// set font
		$this -> pdf->SetFont(PDF_FONT, '', 10);
	}
	
	/**
	 * afiseaza text
	 * @param string $txt - text de afisat
	 * @param int $x - coordonata x 
	 * @param int $y - coordonata y
	 * @param int $size [optional] - marime font
	 * @param string $style [optional] - N - normal, B - bold, I - italic
	 * @return 
	 */
	function text($txt, $x,$y, $size=9, $style="N") {
		$this -> pdf ->SetFont(PDF_FONT, $style, $size);
		$this -> pdf-> Text($x, $y, $txt);		
	}
	
	function getPdf($file) {
		$this->pdf->Output($file, 'I');
	}
	
	/**
	 * salveaza pdf ca fiser pe server
	 * @param string $file - nume fiser
	 * @return 
	 */	
	function savePdf($file) {
		$this -> buildPdf();
		$this -> pdf -> Output($file, 'F');
	}
}
?>