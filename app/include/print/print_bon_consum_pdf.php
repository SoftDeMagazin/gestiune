<?php
class PrintBonConsumPdf extends PdfHelper {

	function __construct($bc_id) {
		$this -> pdfPropertiesLandscape();
		$print = new PrintBonConsum($bc_id);
		$this -> pdf -> AddPage();
		$this -> pdf -> writeHTML($this -> antetSocietateHtml($print -> bc -> gestiune -> societate_id));
		$this -> pdf -> writeHTML($print -> getHtml());
	}	
	
	function getPdf() {
		parent::getPdf('bon_consum.pdf');
	}
}
?>