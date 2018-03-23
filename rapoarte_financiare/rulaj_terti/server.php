<?php
require_once("common.php");
$xajax->processRequest();

function load($filtre) {
	switch($filtre['mod']) {
		case "clienti": {
			$rpt = new RptRulajClienti($filtre);
		}break;
		case "furnizori": {
			$rpt = new RptRulajFurnizori($filtre);
		}break;
	}
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid", "innerHTML", $rpt -> getHtml());
	return $objResponse;
}

function loadSocietate($filtre) {
	$gest = new Gestiuni();
	$gest -> getGestiuniCuDrepturi();
	$in = "'".implode("','", $filtre['societate_id'])."'";
	$gestiuni = new Gestiuni(" inner join puncte_lucru on gestiuni.punct_lucru_id = puncte_lucru.punct_lucru_id
		where puncte_lucru.societate_id in (". $in .");
		");
	$selected = array();	
	foreach($gestiuni as $gestiune) {
		$selected[] = $gestiune -> id;
	}	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_gestiuni", "innerHTML", $gest -> selectMulti($selected));
	$objResponse -> script("	$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadGestiune(xajax.getFormValues('frmFiltre'));
				});	");
	return $objResponse;			
}

function loadGestiune($filtre) {
	$gestiuni_selectate = new Gestiuni();
	$gestiuni_selectate -> fromArrayOfId($filtre['gestiune_id']);
	foreach($gestiuni_selectate as $gest) {
		$selected[] = $gest -> punct_lucru -> societate_id;
	}
	$objResponse = new xajaxResponse();
	$soc = new Societati("where 1 order by `denumire` asc");
	$objResponse -> assign("div_societati", "innerHTML", $soc -> select($selected));
	$objResponse -> script("	$('#societate_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadSocietate(xajax.getFormValues('frmFiltre'));
				});	");
	return $objResponse;			
}
?>