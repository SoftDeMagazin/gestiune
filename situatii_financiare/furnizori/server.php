<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$model = new ViewFurnizori();
	$sql = " inner join terti_gestiuni using(tert_id) WHERE gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";
	if($frmFiltre['denumire']) {
		$sql .= " and `denumire` like '%". $frmFiltre['denumire'] ."%'";	
	}
	
	$sql .= " and `tip` = '". $frmFiltre['tip'] ."'";
	if($frmFiltre['societate_id']) {
		$in = implode(",", $frmFiltre['societate_id']);
		$sql .= " and societate_id in (". $in .")";
	}
	
	if($frmFiltre['sold']) {
		$sql .= " and total_ron_cu_tva > 0";
	}
	
	$sql .= " order by `denumire`, cod_fiscal, societate_id asc";
	$model -> prepareQuery($sql);
	
	$gestiune = new Gestiuni();
	$gestiune -> getGestiuneActiva();
	$societate_id = $gestiune -> societate_id;
	$sf = new SituatieFinanciaraFurnizori("where `tip` = '{$frmFiltre['tip']}' and societate_id = '$societate_id'");
	
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	if(count($info['page'])) $objResponse -> assign("grid", "innerHTML", $info['page'] -> listaSituatiiFinanciarePlati("", "window.location.href = 'tert.php?tert_id=<%tert_id%>&societate_id=<%societate_id%>'", $selected, $frmFiltre['sold']));
	else $objResponse -> assign("grid", "innerHTML", "");
	if(count($sf)) $objResponse -> assign("total", "innerHTML", $sf -> afisare());
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}

function cautare($frm) {
	global $db;
	$tert = new ViewFurnizori(" where `tert_id` = '". $frm['tert_id'] ."' and `societate_id` = '". $frm['societate_id'] ."'");
	if($frm['tert_id']) $sql = "WHERE `tert_id` = '". $frm['tert_id'] ."'";
	else $sql = "INNER JOIN `terti` using(`tert_id`) WHERE `terti`.`tip` = '". $frm['tip'] ."'";
	if($frm['from'] && $frm['end']) {
		$sql .= " and `". $frm['tip_data'] ."` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}
	if($frm['achitat']) {
		$sql .= " and `achitat` = 'NU'";
	}
	$sql .= " and `salvat` = 'DA'";
	$sql .= " and societate_id = '". $frm['societate_id'] ."'";
	
	
	if($frm['achitat']) {
		$sql .= " and `achitat` = 'NU'";
	}
	$sql .= " ORDER BY `data_factura` ASC";
	$facturi = new ViewFacturiIntrari($sql);
	
	$sqlPlati = "WHERE `tert_id` = '". $frm['tert_id'] ."' and `societate_id` = '". $frm['societate_id'] ."'";
	if($frm['asociat']) {
		$sqlPlati .= " and (round(ramas, 2) > 0)";
	}
	$plati = new ViewPlati($sqlPlati);
	
	$sqlEfecte = "WHERE tert_id = '". $frm['tert_id'] ."' and `societate_id` = '". $frm['societate_id'] ."'";	
	if($frm['operat']) {
		$sqlEfecte .= " and operat = 'NU'"; 
	}
	$efecte = new PlatiEfecte($sqlEfecte);
	
	$objResponse = afiseazaFacturi($facturi);
	copyResponse($objResponse, afiseazaPlati($plati));
	copyResponse($objResponse, afiseazaEfecte($efecte));
	copyResponse($objResponse, initControl());
	$objResponse -> assign("div_sold", "innerHTML", "<div><strong>Sold: </strong>". money($tert -> soldPlati(), $tert -> valuta));
	$objResponse -> append("div_sold", "innerHTML",	"<div><strong>Sold Acoperit: </strong>". money($tert -> situatieEfecte(), $tert -> valuta));
	return $objResponse;
}


function afiseazaFacturi($facturi) {
	$objResponse = new xajaxResponse();
	if(count($facturi)) {
		$objResponse -> assign("grid_facturi", "innerHTML", $facturi -> listaPlati("", ""));
		return $objResponse;
	}
	else {
		$objResponse -> assign("grid_facturi", "innerHTML", "Nu sunt facturi");
		return $objResponse;
	}	
}

function frmPlata($plata_id=0, $factura_id=0, $tert_id=0, $societate_id=0) {
	$plata = new Plati($plata_id);
	$plata -> tert_id = $tert_id;
	$plata -> societate_id = $societate_id;
	$dialog = new Dialog(800, 600, '', 'win_frm_plata');
	$dialog -> title = 'Adaugare/Editare Plata';
	$dialog -> modal = true;
	if($plata_id) {
		$plata -> data_doc = c_data($plata -> data_doc);
	}
	else {
		if($factura_id) {
			
			$factura = new FacturiIntrari($factura_id);
			if($factura -> sold() > 0) {
				$plata -> suma = $factura -> sold();
				$plata -> factura_intrare_id = $factura_id;
			} else {
				return alert('Factura este achitata!');
			}	
			$dialog -> title .= ' Factura: '. $factura -> numar_doc .'';
		}
		$plata -> data_doc = c_data(data());
	}


	$dialog -> append($plata -> frmDefault());
	$dialog -> addButton("Renunta");
	if($plata_id) $dialog -> addButton('Sterge', "xajax_stergePlata($plata_id);<%close%>");
	$dialog -> addButton("Salveaza", 'xajax_salveazaPlata(xajax.getFormValues(\'frm_plati\'));<%close%>');
	
	$objResponse = openDialog($dialog); 
	copyResponse($objResponse, initControl());
	$objResponse -> script("\$('#numar_doc').focus().select();");
	return $objResponse;
}

function salveazaPlata($frmValues) {

	
	$plata = new Plati($frmValues);	
	$plata -> gestiune_id = $_SESSION['user'] -> gestiune_id;
	$objResponse = new xajaxResponse();
	if(!$plata -> validate($objResponse)) {
		return $objResponse;
	}
	if($frmValues['plata_id']) {
		$before = new Plati($frmValues['plata_id']);
		if($before -> suma != $plata -> suma) {
			disociazaPlata($plata -> id);
			copyResponse($objResponse, alert('Ati modificat suma! Plata a fost disociata de la facturi!'));
		}
	}	
	
	$plata -> data_doc = data_c($plata -> data_doc);
	$plata -> save();
	if(!$frmValues['plata_id']) { 
		if($plata -> factura_intrare_id) {
			asociazaPlata($plata -> factura_intrare_id, $plata -> id);
		}
	}
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}

function stergePlata($plata_id) {
	if(!$plata_id) {
		return alert("Selectati o plata!");
	}
	$plata = new Plati($plata_id);	
	if($plata -> plata_efect_id > 0) {
		$efect = new PlatiEfecte($plata -> plata_efect_id);
		$efect -> operat = 'NU';
		$efect -> suma_acceptata = '0.00';
		$efect -> raspuns = '??';
		$efect -> save();
	}
	disociazaPlata($plata_id);
	$plata -> delete();
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}

function afiseazaPlati($plati) {
	$objResponse = new xajaxResponse();
	if(count($plati)) {
		$objResponse -> assign("grid_incasari", "innerHTML", $plati -> lista("", "xajax_frmPlata('<%plata_id%>', '<%factura_intrare_id%>', '<%tert_id%>', '<%societate_id%>')"));
	}
	else {
		$objResponse -> assign("grid_incasari", "innerHTML", "Nu sunt plati");
		return $objResponse;
	}
	return $objResponse;
}

function asociazaPlata($factura_id, $plata_id) {
	$plata = new Plati($plata_id);
	$factura = new FacturiIntrari($factura_id);
	if($plata -> sumaNeasociata() == 0) {
		return alert('Plata este asociata in totalitate!');
	}
	
	if($factura -> sold() == 0) {
		return alert('Factura este platita!');
	}
	if($plata -> sumaNeasociata() > $factura -> sold()) {
			$asociere = new PlatiAsocieri();
			$asociere -> plata_id = $plata -> id;
			$asociere -> factura_intrare_id = $factura -> id;
			$asociere -> suma = $factura -> sold();
			$asociere -> save();
	}
	else {
			$asociere = new PlatiAsocieri();
			$asociere -> plata_id = $plata -> id;
			$asociere -> factura_intrare_id = $factura -> id;
			$asociere -> suma = $plata -> sumaNeasociata();
			$asociere -> save();
	}
	
	if($factura -> sold() == 0) {
		$factura -> achitat = 'DA';
		$factura -> save();
	}
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'))");
	return $objResponse;
}

function disociazaPlata($plata_id) {
	global $db;
	$asocieri = new PlatiAsocieri("where plata_id = '$plata_id'");
	foreach($asocieri as $asociere) {
		$factura = new FacturiIntrari($asociere -> factura_intrare_id);
		$factura -> achitat = 'NU';
		$factura -> save();
		$asociere -> delete();
	}
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'))");
	return $objResponse;
} 

//functii efecte cmerciala

function frmEfect($efect_id=0, $tert_id=0, $societate_id=0) {
	$efect = new PlatiEfecte($efect_id);
	$efect -> societate_id = $societate_id;
	$efect -> tert_id = $tert_id;
	if($efect_id) {
		$efect -> data_emitere = c_data($incasare -> data_emitere);
		$efect -> data_scadenta = c_data($incasare -> data_scadenta);
		if($efect -> operat == 'DA') {
			return alert('Efectul a fost operat si nu poate fi editat.');
		}
	}
	else {
		$efect -> data_emitere = c_data(data());
		$efect -> data_scadenta = c_data(data());
	}

	$dialog = new Dialog(800, 600, '', 'win_frm_plata');
	$dialog -> title = 'Adaugare/Editare Efect Comercial';
	$dialog -> append($efect -> frmDefault());
	$dialog -> addButton("Renunta");
	if($efect_id) $dialog -> addButton('Sterge', "xajax_stergeEfect($efect_id);<%close%>");
	$dialog -> addButton("Salveaza", 'xajax_salveazaEfect(xajax.getFormValues(\'frm_plati_efecte\'));<%close%>');
	
	$objResponse = openDialog($dialog); 
	copyResponse($objResponse, initControl());
	$objResponse -> script("\$('#numar_doc').focus().select();");
	return $objResponse;
}

function salveazaEfect($frmValues) {
	$efect = new PlatiEfecte($frmValues);
	$efect -> gestiune_id = $_SESSION['user'] -> gestiune_id;
	$objResponse = new xajaxResponse();
	if(!$efect -> validate($objResponse)) {
		return $objResponse;
	}
	$efect -> data_emitere = data_c($efect -> data_emitere);
	$efect -> data_scadenta = data_c($efect -> data_scadenta);
	$efect -> save();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}

function stergeEfect($efect_id) {
	global $db;
	if(!$efect_id) {
		return alert("Selectati un efect comercial!");
	}
	
	$plata = new Plati("where plata_efect_id = '$efect_id'");
	if(count($plata)) {		
		$db -> query("delete from `plati_asocieri` where `plata_id` = '". $plata -> id ."'");
		$plata -> delete();
	}	
	
	$efect = new PlatiEfecte($efect_id);
	$efect -> delete();
	
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}


function operareEfect($efect_id) {
	$efect = new PlatiEfecte($efect_id);
	if($efect -> operat == 'DA') {
		return alert('Efectul comercial a fost deja operat!');
	}
	$dialog = new Dialog(800, 600, '', "win_operare_efect");
	$dialog -> title = "Operare Efect Comercial";
	$dialog -> append('
	<div style="margin-top:30px;">
	<a href="#" id="btnIntern" class="ui-state-default" style="padding: .5em 1em; text-decoration: none; width:10em;" onClick="xajax_opereazaEfect('. $efect_id .', 0, \'OK\'); return false;">ACCEPT TOTAL</a> </div>
	<div style="margin-top:30px;">
<a href="#" id="btnIntern" class="ui-state-default" style="padding: .5em 1em; text-decoration: none; width:10em;" onClick="xajax_opereazaEfect('. $efect_id .', $(\'#suma_acceptata\').val(), \'PA\'); return false;">ACCEPT PARTIAL</a> Suma: <input type="text" id="suma_acceptata" value="" > </div>
<div style="margin-top:30px;">
<a href="#" id="btnIntern" class="ui-state-default" style="padding: .5em 1em; text-decoration: none; width:10em;" onClick="xajax_opereazaEfect('. $efect_id .', 0, \'NA\'); return false;">REFUZ TOTAL</a> 
</div>
	');
	return openDialog($dialog);
}

function opereazaEfect($efect_id, $suma, $stare) {
	$efect = new PlatiEfecte($efect_id);
	$efect -> operareEfect($suma, $stare);
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'))");
	copyResponse($objResponse, closeDialog('win_operare_efect'));
	return $objResponse;
}


function afiseazaEfecte($efecte) {
	$objResponse = new xajaxResponse();
	if(count($efecte)) {
		$objResponse -> assign("grid_efecte", "innerHTML", $efecte -> lista("", "xajax_frmEfect('<%efect_id%>', '<%tert_id%>')"));
	}
	else {
		$objResponse -> assign("grid_efecte", "innerHTML", "Nu sunt efecte comerciale");
		return $objResponse;
	}
	return $objResponse;
}

function situatieActuala($tert_id) {
	$tert = new Terti($tert_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_situatie_actuala", "innerHTML", $tert -> situatieActualaFurnizor($_SESSION['user'] -> gestiune_id));
	return $objResponse;
}
?>