<?php
require_once("common.php");
$xajax->processRequest();

function cautare($frm) {
	$sql = "
	WHERE 1 ";
	
	
	if($frm['from'] && $frm['end']) {
		$sql .= " and `data_doc` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}
	
	if(!$frm['gestiune_id']) {
		$frm['gestiune_id'] = $_SESSION['user'] -> gestiuni_asociate;
	}
	
	if($frm['gestiune_id']) {
		$in = "'".implode("','", $frm['gestiune_id'])."'";
		$sql .= " and gestiune_id in (". $in .")";
	}

	$model = new Avize($sql);
	return afiseaza($model);
}


function afiseaza($model) {
	$objResponse = new xajaxResponse();
	if(count($model)) {
		$objResponse -> assign("grid", "innerHTML", $model -> lista("", "xajax_sumar('<%aviz_id%>')"));
		copyResponse($objResponse, switchTab("lista"));
		copyResponse($objResponse, initControl());
		return $objResponse;
	}
	else {
		$objResponse = alert('Cautarea nu a returnat nici un rezultat'); 
		$objResponse -> assign("grid", "innerHTML", "");
		return $objResponse;
	}
	
}

function sumar($aviz_id) {
	$model = new Avize($aviz_id);
	$dialog = new Dialog(800, 600, '', 'win_sumar_document');
	$dialog -> title = "Info Aviz ".$model -> numar_doc;

	$dialog -> append($model -> sumar());
	
	$dialog -> append("<fieldset><legend>Continut Document</legend>");
	if($model -> tip_aviz == "la_factura") $continut = $model -> factura -> continut;
	if($model -> tip_aviz == "la_transfer") $continut = $model -> transfer -> continut;
	if($model -> tip_aviz == "doc_pa") $continut = $model -> continut;
	if($model -> tip_aviz == "doc_pv") {
		$continut = $model -> continut;
		$dialog -> append(Html::overflowDiv($continut -> listaDocPv(), "300px"));
	} else {
		$dialog -> append(Html::overflowDiv($continut -> lista(), "300px"));
	}
	$dialog -> append("</fieldset>");
	
	$dialog -> addButton("Renunta");
	$dialog -> addButton("Tiparire",  "xajax_xPrintAviz('". $aviz_id ."')");
	$dialog -> addButton("Editare", "xajax_location('".DOC_ROOT."iesiri/introducere_aviz/?aviz_id=". $aviz_id ."')");
	if($model -> tip_aviz == "doc_pv") {
		$dialog -> addButton("Emite Factura", "xajax_emiteFactura('". $model -> id ."');");
	}
	return $dialog -> open();
}

function emiteFactura($aviz_id) {
	$aviz = new Avize($aviz_id);
	$factura = $aviz -> emiteFactura();
	$objResponse = closeDialog('win_sumar_document');
	copyResponse($objResponse, sumar($aviz_id));
	copyResponse($objResponse, infoFactura($factura -> id));
	return $objResponse;
}

function infoFactura($factura_id) {
	$dialog = new Dialog(400, 300, "", "win_info_factura");
	$factura = new Facturi($factura_id);
	$dialog -> append($factura -> sumar());
	$dialog -> addButton("Print Factura", "xajax_xPrintFactura('". $factura_id ."')");
	return  $dialog -> open();
}
?>