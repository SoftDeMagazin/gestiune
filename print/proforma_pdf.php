<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$factura_id = $_GET['factura_id'];

$factura = new FacturiProforme($factura_id);

switch($factura -> tert -> tip) {
	case "intern": {
		$print = new PrintProformaInternaPdf($factura_id);
	}break;
	case "extern_ue": {
		$print = new PrintProformaExternaPdf($factura_id);
	}break;
}
$print -> getPdf();
?>