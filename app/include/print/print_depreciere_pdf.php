<?php
class PrintDeprecierePdf extends PdfHelper {

	function __construct($depreciere_id) {
		$this -> pdfPropertiesLandscape();
		$print = new PrintDepreciere($depreciere_id);
		$this -> pdf -> AddPage();
		$this -> pdf -> writeHTML($this -> antetSocietateHtml($print -> depreciere -> gestiune -> societate_id));
		$this -> pdf -> writeHTML($print -> getHtml());
	}	
	
	function getPdf() {
		parent::getPdf('depreciere.pdf');
	}
}
?>