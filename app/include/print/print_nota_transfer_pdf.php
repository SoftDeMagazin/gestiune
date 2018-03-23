<?php
class PrintNotaTransferPdf extends PdfHelper {

	function __construct($doc_id) {
		$this -> pdfPropertiesLandscape();
		$print = new PrintNotaTransfer($doc_id);
		$this -> pdf -> AddPage();
		$this -> pdf -> writeHTML($this -> antetSocietateHtml($print -> doc -> gestiune -> societate_id));
		$this -> pdf -> writeHTML($print -> getHtml());
	}	
	
	function getPdf() {
		parent::getPdf('nota_transfer.pdf');
	}
}
?>