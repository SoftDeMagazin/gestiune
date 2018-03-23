<?php
require_once("common.php");
$xajax->processRequest();

function load() {
	$objResponse = new xajaxResponse();
	$clienti = new Terti("where 1 order by `denumire` asc");
	$objResponse -> assign("div_frm_client", "innerHTML", $clienti -> select());
	return $objResponse;
}

function cautareClient($frm) {
	$sql = "WHERE 1 ";
	
	if($frm['tert_id']) $sql .= "and `tert_id` = '". $frm['tert_id'] ."'";
	
	if($frm['from'] && $frm['end']) {
		$sql .= " and `data_factura` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}
	if(!$frm['gestiune_id']) {
		$frm['gestiune_id'] = $_SESSION['user'] -> gestiuni_asociate;
	}
	
	if($frm['gestiune_id']) {
		$in = "'".implode("','", $frm['gestiune_id'])."'";
		$sql .= " and gestiune_id in (". $in .")";
	}

	if($frm['txt_numar']) {
		$sql .= " and numar_doc like '%". $frm['txt_numar'] ."%'";
	}
	
	$sql .= " and `salvat` = 'DA'";
	$sql .= " ORDER BY `data_factura` ASC";
	$facturi = new Facturi($sql);
	return afiseazaFacturi($facturi);
}


function afiseazaFacturi($facturi) {
	$objResponse = new xajaxResponse();
	if(count($facturi)) {
		$objResponse -> assign("grid", "innerHTML", $facturi -> lista("", "xajax_sumarFactura('<%factura_id%>');"));
		copyResponse($objResponse, switchTab("lista"));
		copyResponse($objResponse, initControl());
		return $objResponse;
	}
	else {
		return alert('Cautarea nu a returnat nici un rezultat'); 
	}
	
}

function sumarFactura($factura_id) {
	$factura = new Facturi($factura_id);
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
	$dialog -> addButton("Renunta");
	$dialog -> addButton("Editeaza", "window.location.href = '/iesiri/introducere_factura/?factura_id=".$factura -> factura_id."';");
	$dialog -> addButton("Tiparire", "popup('". DOC_ROOT ."print/factura_pdf.php?factura_id=". $factura -> id ."', 'factura_pdf');");
	$objResponse = openDialog($dialog);
	return $objResponse;
}
?>