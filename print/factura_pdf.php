<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$factura_id = $_GET['factura_id'];

$factura = new Facturi($factura_id);

switch($factura -> tip_factura) {
	case "interna": {
		$print = new PrintFacturaInternaPdf($factura_id);
	}break;
	case "extern_ue": {
		$print = new PrintFacturaExternaPdf($factura_id);
	}break;
}
$print -> getPdf();
?>