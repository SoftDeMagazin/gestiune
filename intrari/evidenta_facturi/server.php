<?php
session_start();
header("Cache-control: private"); // IE 6 Fix 
require_once("common.php");
$xajax->processRequest();

function load() {
	$objResponse = new xajaxResponse();
	$furnizor = new Terti("where 1 order by `denumire` asc");
	$objResponse -> assign("div_frm_furnizor", "innerHTML", $furnizor -> select());
	return $objResponse;
}

function cautare($frmValues) {
	$sql = "WHERE 1";
	
	if($frmValues['txt_numar']) {
		$sql .= " and `numar_doc` like '%". $frmValues['txt_numar'] ."%' and `salvat` = 'DA' ORDER BY `data_factura` ASC";
	}
	$facturi = new FacturiIntrari($sql);
	return afiseazaFacturi($facturi);
}

function cautareFurnizor($frm) {
	$sql = "WHERE 1";
	
	if($frm['tert_id']) $sql .= " and `tert_id` = '". $frm['tert_id'] ."'";
	
	if($frm['from'] && $frm['end']) {
		$sql .= " and `data_factura` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}
	
	if($frm['txt_numar']) {
		$sql .= " and `numar_doc` like '%". $frm['txt_numar'] ."%'";
	}
	
	$sql .= " and `cheltuieli` = 'DA' and `salvat` = 'DA'";
	$sql .= " ORDER BY `data_factura` ASC";
	$facturi = new FacturiIntrari($sql);
	return afiseazaFacturi($facturi);
}


function afiseazaFacturi($facturi) {
	$objResponse = new xajaxResponse();
	if(count($facturi)) {
		$objResponse -> assign("grid", "innerHTML", $facturi -> lista("", "xajax_sumarFactura('<%factura_intrare_id%>');"));
		copyResponse($objResponse, initControl());
		return $objResponse;
	}
	else {
		$objResponse = alert('Cautarea nu a returnat nici un rezultat'); 
		$objResponse -> assign("grid", "innerHTML", "");
		return $objResponse;
	}
	
}

function sumarFactura($factura_id) {
	$factura = new FacturiIntrari($factura_id);
	$dialog = new Dialog(800, 600, "", "win_sumar_factura");
	$dialog -> title = "Sumar Factura";
	$dialog -> append($factura -> sumar());
	$dialog -> append(
	'<fieldset>
	<legend>Continut factura</legend>
	<div style="height:300px;overflow:scroll; overflow-x:hidden;">
	'. $factura -> continut -> lista() .'
	</div>
	</fieldset>
	'
	);
	$dialog -> addButton("Editeaza Factura", "xajax_edit_factura($factura_id)");
	$dialog -> addButton("Renunta");
	$objResponse = openDialog($dialog);
	return $objResponse;
}

function edit_factura($factura_id)
{
	$objResponse = new xajaxResponse();
	$objResponse->redirect(DOC_ROOT."/intrari/introducere_factura?factura_id=".$factura_id);
	return $objResponse;
}
?>