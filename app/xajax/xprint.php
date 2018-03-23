<?php
function xPrintNir($nir_id) {
	$objResponse = new xajaxResponse();
    $objResponse->script("popup('".DOC_ROOT."print/print_nir.php?nir_id=$nir_id', 'print_nir');");
    return $objResponse;
}

function xPrintAviz($aviz_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/aviz_pdf.php?aviz_id=". $aviz_id."', 'aviz');");
	return $objResponse; 
}
function xPrintFactura($factura_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/factura_pdf.php?factura_id=". $factura_id."', 'factura');");
	return $objResponse; 
}

function xPrintProforma($factura_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/proforma_pdf.php?factura_id=". $factura_id."', 'factura_proforma');");
	return $objResponse; 
}

function xPrintDepreciere($depreciere_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/depreciere_pdf.php?depreciere_id=". $depreciere_id ."', 'depreciere');");
	return $objResponse; 
}

function xPrintBonConsum($bc_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/bon_consum_pdf.php?bon_consum_id=". $bc_id ."', 'bon_consum');");
	return $objResponse; 
}

function xPrintNotaTransfer($doc_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/nota_transfer_pdf.php?transfer_id=". $doc_id ."', 'nota_transfer');");
	return $objResponse; 
}

function xPrintDecizieS($gestiune_id) {
	$objResponse = new xajaxResponse();
	$objResponse->script("popup('". DOC_ROOT ."print/decizie_serii_numerice.php?gestiune_id=". $gestiune_id ."', 'decizie_sn');");
	return $objResponse; 
}
$xajax -> registerFunction("xPrintNir");
$xajax -> registerFunction("xPrintAviz");
$xajax -> registerFunction("xPrintDepreciere");
$xajax -> registerFunction("xPrintFactura");
$xajax -> registerFunction("xPrintProforma");
$xajax -> registerFunction("xPrintBonConsum");
$xajax -> registerFunction("xPrintDecizieS");
$xajax -> registerFunction("xPrintNotaTransfer");
?>