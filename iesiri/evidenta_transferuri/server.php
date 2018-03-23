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
	
	$sql .= " and anulat = 'NU'";

	$model = new Transferuri($sql);
	return afiseaza($model);
}


function afiseaza($model) {
	$objResponse = new xajaxResponse();
	if(count($model)) {
		$objResponse -> assign("grid", "innerHTML", $model -> lista("", "xajax_sumar('<%transfer_id%>')"));
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

function sumar($transfer_id) {
	$model = new Transferuri($transfer_id);
	$dialog = new Dialog(800, 600, '', 'win_sumar_document');
	$dialog -> title = "Informatii Transfer";
	$dialog -> append($model -> sumar());
	if($model -> tip_doc == "factura") {
		$factura = new Facturi($model -> doc_id);
		$dialog -> append($factura -> sumar());
	}
		
	if($model -> tip_doc == "aviz") {
		$aviz = new Avize($model -> doc_id);
		$dialog -> append($aviz -> sumar());
	}
			
	
	$dialog -> append("<fieldset><legend>Continut Document</legend>");
	
	$continut = $model -> continut;
	
	$dialog -> append(Html::overflowDiv($continut -> lista(), "300px"));
	$dialog -> append("</fieldset>");
	
	$dialog -> addButton("Renunta");
	
	if($model -> salvat == 'NU') {
		$dialog -> addButton("Editare", "xajax_location('".DOC_ROOT."iesiri/introducere_transfer/?transfer_id=".$model->id."')");	
	}
	else { 
		if($model -> tip_doc == "factura") {
			$dialog -> addButton("Tipareste Factura", "xajax_xPrintFactura(". $model -> doc_id .")");
		} else if($model -> tip_doc == "aviz"){
			$dialog -> addButton("Tipareste Aviz", "xajax_xPrintAviz(". $model -> doc_id .")");
		} else {
			$dialog -> addButton("Tipareste Nota", "xajax_xPrintNotaTransfer(". $model -> id .")");
		}
		$dialog -> addButton("Tipareste Nir", "xajax_xPrintNir(". $model -> nir_id .")");
		$dialog -> addButton("Anuleaza Transfer", "xajax_anuleazaTransfer(". $model -> id .")");
	}
	return $dialog -> open();
}

function anuleazaTransfer($transfer_id) {
	
	$transfer = new Transferuri($transfer_id);
	$nir = new Niruri($transfer -> nir_id);
	if($nir -> suntLoturiScazute()) {
		return alert("Transferul nu poate fi anulat. S-au scazut din loturile generate!");
	}
	
	$transfer -> sterge();
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	copyResponse($objResponse, closeDialog("win_sumar_document"));
	copyResponse($objResponse, alert("Transferul a fost anulat"));
	return $objResponse;
}
?>