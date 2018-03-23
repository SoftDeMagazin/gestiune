<?php
require_once("common.php");
$xajax->processRequest();

function load($filtre) {
	$sql = "WHERE operat = 'NU'";
	if($filtre['gestiune_id']) {
		$in = "'".implode("','", $f['gestiune_id'])."'";
		$sql .= " and incasari_efecte.gestiune_id in (". $in .")";
	}		
	
	if($filtre['tert_id']) {
		$in = "'".implode("','", $f['tert_id'])."'";
		$sql .= " and incasari_efecte.tert_id in (". $in .")";
	}		
	$efecte = new IncasariEfecte("where operat = 'NU'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid", "innerHTML", $efecte -> listaRptNeincasate());
	copyResponse($objResponse, initControl());
	return $objResponse;
}


function salveazaEfecte($frm) {
	foreach($frm['incasare_efect_id'] as $key => $value) {
		$efect = new IncasariEfecte($value);
		switch($frm['action'][$key]) {
			case "accept_total": {
				$efect -> operareEfect(0, 'OK');
			}break;
			case "accept_partial": {
				$efect -> operareEfect($frm['suma_acceptata'][$key], 'PA');
			}break;
			case "refuz": {
				$efect -> operareEfect(0, 'NA');
			}break;
			case "depunere": {
				$efect -> depunere(data_c($frm['data_depunere'][$key]));
			};
		}
	}
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_load(xajax.getFormValues('frmFiltre'))");
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