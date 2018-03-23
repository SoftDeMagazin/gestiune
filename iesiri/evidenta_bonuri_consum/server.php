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

	$bonuriconsum = new BonuriConsum($sql);
	return afiseaza($bonuriconsum);
}


function afiseaza($bonuriconsum) {
	$objResponse = new xajaxResponse();
	if(count($bonuriconsum)) {
		$objResponse -> assign("grid", "innerHTML", $bonuriconsum -> lista("", "xajax_sumar('<%bon_consum_id%>')"));
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

function sumar($bon_consum_id) {
	$model = new BonuriConsum($bon_consum_id);
	$dialog = new Dialog(800, 600, '', 'win_sumar_document');
	$dialog -> title = "Validare document";
	$dialog -> append("<fieldset><legend>Info Document</legend>");
	$dialog -> append("Numar Document: ". $model -> numar_doc);
	$dialog -> append("<br>");
	$dialog -> append("Data Document: ". c_data($model -> data_doc));
	$dialog -> append("<br>");
	$dialog -> append("Intocmit de: ". $model -> utilizator -> nume);
	$dialog -> append("</fieldset>");
	$dialog -> append("<fieldset><legend>Continut Document</legend>");
	$dialog -> append(Html::overflowDiv($model -> continut -> lista(), "300px"));
	$dialog -> append("</fieldset>");
	
	$dialog -> addButton("Renunta");
	$dialog -> addButton("Tiparire",  "xajax_xPrintBonConsum('". $bon_consum_id ."')");
	$dialog -> addButton("Editare", "xajax_location('".DOC_ROOT."iesiri/introducere_bon_consum/?bon_consum_id=". $bon_consum_id ."')");

	
	return $dialog -> open();
}
?>