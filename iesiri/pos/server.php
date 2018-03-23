<?php
require_once("common.php");
$xajax->processRequest();

function cautare($frm) {
	$sql = "
	inner join posuri using(pos_id)
	WHERE 1 ";
	
	
	if($frm['from'] && $frm['end']) {
		$sql .= " and `data_economica` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}
	
	if(!$frm['gestiune_id']) {
		$frm['gestiune_id'] = $_SESSION['user'] -> gestiuni_asociate;
	}
	
	if($frm['gestiune_id']) {
		$in = "'".implode("','", $frm['gestiune_id'])."'";
		$sql .= " and gestiune_id in (". $in .")";
	}

	$vanzari = new VanzariPos($sql);
	return afiseaza($vanzari);
}


function afiseaza($vanzari) {
	$objResponse = new xajaxResponse();
	if(count($vanzari)) {
		$objResponse -> assign("grid", "innerHTML", $vanzari -> lista("", "xajax_continut('<%vp_id%>')"));
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


function continut($vp_id) {
	$sql = " WHERE `vp_id` = '$vp_id'";
	$vanzare = new VanzariPos($vp_id);
	$continut = new VanzariPosContinut($sql);
	
	if(count($continut)) {
	
		$objResponse = switchTab("continut");
		if($vanzare -> validat == "DA") {
			$objResponse -> assign("grid_continut", "innerHTML", $continut -> listaPreturiAchizitie());
		} else {
			$objResponse -> assign("grid_continut", "innerHTML", $continut -> lista());
		}
		
		return $objResponse;
	} else {
		$objResponse = switchTab("continut");
		$objResponse -> assign("grid_continut", "innerHTML", "Exportul nu contine inregistrari");
		return $objResponse;
	}
}

function validare($vp_id) {
	$vanzari = new VanzariPos($vp_id);
	if($vanzari -> validat == "DA") {
		return alert('Vanzarile din acea data au fost scazute deja!');
	}
	$vanzari -> scadStoc();
	$vanzari -> validat = 'DA';
	$vanzari -> save();
	
	$dialog = new Dialog(800, 600, "", "win_vanzare_save");
	$dialog -> append("Vanzarile au fost scazute din stoc");
	$objResponse = $dialog -> open();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
} 

function anulare($vp_id) {
	$vanzari = new VanzariPos($vp_id);
	$vanzari -> anulareScaderi();
	$vanzari -> validat = 'NU';
	$vanzari -> save();
	
	$dialog = new Dialog(800, 600, "", "win_vanzare_save");
	$dialog -> append("Vanzarile au fost anulate");
	$objResponse = $dialog -> open();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}
?>