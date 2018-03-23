<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$aviz_id = $_GET['aviz_id'];

$aviz = new Avize($aviz_id);
switch($aviz -> tip_aviz) {
	case "la_transfer": {
		if(AVIZ_PRET_VANZARE) {
			$print = new PrintAvizPentruTransferPvPdf($aviz_id);
		} else {
			$print = new PrintAvizPentruTransferPvPdf($aviz_id);
		}
	}break;
	case "la_factura": {
		$print = new PrintAvizPentruFacturaPdf($aviz_id);
	}break;
	case "doc_pa": {
		$print = new PrintAvizPretAchizitiePdf($aviz_id);
	}break;
	case "doc_pv": {
		$print = new PrintAvizPretVanzarePdf($aviz_id);
	}break;
}
$print -> getPdf();
?>