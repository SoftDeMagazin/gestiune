<?php
require_once("common.php");
$xajax->processRequest();

function load($filtre) {
	$rpt = new RptIncasari($filtre);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid", "innerHTML", $rpt -> getHtml());
	return $objResponse;
}

function loadSocietate($filtre) {
	global $db;
	$gest = new Gestiuni();
	$gest -> getGestiuniCuDrepturi();
	$in = "'".implode("','", $filtre['societate_id'])."'";
	$gestiuni = new Gestiuni(" where societate_id in (". $in .");");
	$selected = array();	
	foreach($gestiuni as $gestiune) {
		$selected[] = $gestiune -> id;
	}	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_gestiuni", "innerHTML", $gest -> selectMulti($selected));
	$objResponse -> script("	$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadGestiune(xajax.getFormValues('frmFiltre'));
				});	");
				
	$posuri = new Posuri(" where gestiune_id ".$db -> inArray($selected)."");
	$objResponse -> assign("div_posuri", "innerHTML", $posuri -> select_multiple());
	$objResponse -> script("$('#pos_id').multiSelect()");				
	return $objResponse;			
}

function loadGestiune($filtre) {
	global $db;
	$gestiuni_selectate = new Gestiuni();
	$gestiuni_selectate -> fromArrayOfId($filtre['gestiune_id']);
	foreach($gestiuni_selectate as $gest) {
		$selected[] = $gest -> societate_id;
	}
	$objResponse = new xajaxResponse();
	$soc = new Societati("where 1 order by `denumire` asc");
	$objResponse -> assign("div_societati", "innerHTML", $soc -> select($selected));
	$objResponse -> script(" $('#societate_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadSocietate(xajax.getFormValues('frmFiltre'));
				});	");
	$posuri = new Posuri(" where gestiune_id ".$db -> inArray($filtre['gestiune_id'])."");
	$objResponse -> assign("div_posuri", "innerHTML", $posuri -> select_multiple());
	$objResponse -> script("$('#pos_id').multiSelect()");			
	return $objResponse;			
}

function loadPosuri($filtre) {
}
?>