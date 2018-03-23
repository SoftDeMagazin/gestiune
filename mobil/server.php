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
	$objResponse -> script("	
				$('#gestiune_id').change(
				function() {
					xajax_loadGestiune(xajax.getFormValues('frmFiltre'));
				}
			);
	");
	return $objResponse;			
}

function loadGestiune($filtre) {
	$gestiuni_selectate = new Gestiuni();
	$gestiuni_selectate -> fromArrayOfId($filtre['gestiune_id']);
	foreach($gestiuni_selectate as $gest) {
		$selected[] = $gest -> societate_id;
	}
	$objResponse = new xajaxResponse();
	$soc = new Societati("where 1 order by `denumire` asc");
	$objResponse -> assign("div_societati", "innerHTML", $soc -> select($selected));
	$objResponse -> script("	
						$('#societate_id').change(
				function() {
					xajax_loadSocietate(xajax.getFormValues('frmFiltre'));
				}
			);
		");
	return $objResponse;			
}

function loadPosuri($filtre) {
}
?>