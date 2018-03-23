<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$model = new ViewClienti();
	$sql = " inner join terti_gestiuni using(tert_id) WHERE gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";
	if($frmFiltre['denumire']) {
		$sql .= " and (`denumire` like '%". $frmFiltre['denumire'] ."%' or cod_fiscal  like '%". $frmFiltre['denumire'] ."%')";	
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
	
	
	
	//if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	//$model -> pageLength($frmPager['pagesize']);
	
	$gestiune = new Gestiuni();
	$gestiune -> getGestiuneActiva();
	$societate_id = $gestiune -> punct_lucru -> societate_id;
	
	$sf = new SituatieFinanciaraClienti("where `tip` = '{$frmFiltre['tip']}' and societate_id = '$societate_id'");
	
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	if(count($info['page'])) $objResponse -> assign("grid", "innerHTML", $info['page'] -> listaSituatiiFinanciareIncasari("", "window.location.href = 'tert.php?tert_id=<%tert_id%>&societate_id=<%societate_id%>'", $selected, $frmFiltre['sold']));
	else $objResponse -> assign("grid", "innerHTML", "");
	$objResponse -> assign("total", "innerHTML", $sf -> afisare());
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}

function cautare($frm) {
	global $db;
	$tert = new ViewClienti(" where `tert_id` = '". $frm['tert_id'] ."' and `societate_id` = '". $frm['societate_id'] ."'");
	if($frm['tert_id']) $sql = "WHERE `tert_id` = '". $frm['tert_id'] ."'";
	else $sql = "INNER JOIN `terti` using(`tert_id`) WHERE `terti`.`tip` = '". $frm['tip'] ."'";
	if($frm['from'] && $frm['end']) {
		$sql .= " and `". $frm['tip_data'] ."` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}
	if($frm['achitat']) {
		$sql .= " and `achitat` = 'NU'";
	}
	$sql .= " and societate_id = '". $frm['societate_id'] ."'";
	$sql .= " ORDER BY `data_factura` ASC";
		
	$facturi = new ViewFacturi($sql);
	
	$sqlIncasari = "WHERE `tert_id` = '". $frm['tert_id'] ."' and `societate_id` = '". $frm['societate_id'] ."'";
	if($frm['from'] && $frm['end']) {
		$sqlIncasari .= " and `data_doc` between '". data_c($frm['from']) ."' and '". data_c($frm['end']) ."'";
	}	
	
	if($frm['asociat']) {
		$sqlIncasari .= " and (round(ramas, 2) > 0)";
	}
	$incasari = new ViewIncasari($sqlIncasari);	
	
	$sqlEfecte = "WHERE tert_id = '". $frm['tert_id'] ."' and `societate_id` = '". $frm['societate_id'] ."'";
	if($frm['operat']) {
		$sqlEfecte .= " and operat = 'NU'";
	}
	$efecte = new IncasariEfecte($sqlEfecte);
	
	$objResponse = afiseazaFacturi($facturi);
	copyResponse($objResponse, afiseazaIncasari($incasari));
	copyResponse($objResponse, afiseazaEfecte($efecte));
	copyResponse($objResponse, initControl());
	$objResponse -> assign("div_sold", "innerHTML", "<div><strong>Sold: </strong>". money($tert -> soldIncasari(), $tert -> valuta));
	$objResponse -> append("div_sold", "innerHTML",	"<div><strong>Sold Acoperit: </strong>". money($tert -> situatieEfecte(), $tert -> valuta));
	return $objResponse;
}


function afiseazaFacturi($facturi) {
	$objResponse = new xajaxResponse();
	if(count($facturi)) {
		$objResponse -> assign("grid_facturi", "innerHTML", $facturi -> listaIncasari("", "xajax_sumarFactura('<%factura_id%>')"));
		return $objResponse;
	}
	else {
		$objResponse -> assign("grid_facturi", "innerHTML", "Nu sunt facturi");
		return $objResponse;
	}	
}

function afiseazaIncasari($incasari) {
	$objResponse = new xajaxResponse();
	if(count($incasari)) {
		$objResponse -> assign("grid_incasari", "innerHTML", $incasari -> lista("", "xajax_frmIncasare('<%incasare_id%>', '0', '<%tert_id%>', '<%societate_id%>')"));
	}
	else {
		$objResponse -> assign("grid_incasari", "innerHTML", "Nu sunt incasari");
		return $objResponse;
	}
	return $objResponse;
}


function frmIncasare($incasare_id=0, $factura_id=0, $tert_id=0, $societate_id=0) {
	$incasare = new Incasari($incasare_id);
	$incasare -> tert_id = $tert_id;
	$incasare -> societate_id = $societate_id;
	if($incasare_id) {
		$incasare -> data_doc = c_data($incasare -> data_doc);
		if($incasare -> incasare_efect_id > 0) {
			return alert('Nu puteti edita aceasta incasare deoarece provine din operarea unui efect de comert! Daca este o inregistrare gresita, o puteti sterge si edita efectul de comert!');
		}
	}
	else {
		if($factura_id) {
			$factura = new Facturi($factura_id);
			if($factura -> achitat == 'DA') {
				return alert('Factura este achitata');
			}
			$incasare -> suma = $factura -> sold();
			$incasare -> factura_id = $factura_id;			
		}
		$incasare -> data_doc = c_data(data());
	}

	$dialog = new Dialog(800, 600, '', 'win_frm_plata');
	$dialog -> title = 'Adaugare/Editare Incasare';
	$dialog -> append($incasare -> frmDefault());
	$dialog -> addButton("Renunta");
	if($incasare_id) $dialog -> addButton('Sterge', "xajax_stergeIncasare($incasare_id);<%close%>");
	$dialog -> addButton("Salveaza", 'xajax_salveazaIncasare(xajax.getFormValues(\'frm_incasari\'));<%close%>');
	
	$objResponse = openDialog($dialog); 
	copyResponse($objResponse, initControl());
	$objResponse -> script("\$('#numar_doc').focus().select();");
	return $objResponse;
}

function salveazaIncasare($frmValues) {
	$incasare = new Incasari($frmValues);	
	$objResponse = new xajaxResponse();
	$incasare -> gestiune_id = $_SESSION['user'] -> gestiune_id;
	if(!$incasare -> validate($objResponse)) {
		return $objResponse;
	}
	$incasare -> data_doc = data_c($incasare -> data_doc);
	$incasare -> save();
	if(!$frmValues['incasare_id']) { 
		if($incasare -> factura_id) {
			asociazaIncasare($incasare -> factura_id, $incasare -> id);
		}
	}
	else {
		disociazaIncasare($incasare -> id);
	}
	
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}


function asociazaIncasare($factura_id, $incasare_id) {
	if(!$incasare_id) {
		return alert('Selectati o incasare!');
	}
	if(!$factura_id) {
		return alert('Selectati o factura!');
	}
	$incasare = new Incasari($incasare_id);
	$factura = new Facturi($factura_id);
	if($factura -> totalFactura() < 0) {
		return alert('Factura este negativa, nu puteti asocia plata!');
	}
	if($incasare -> sumaNeasociata() == 0) {
		return alert('Plata este asociata in totalitate!');
	}
	
	if($factura -> sold() == 0) {
		return alert('Factura este acoperita in totalitate!');
	}
	
	if($incasare -> sumaNeasociata() > $factura -> sold()) {
		$suma = $factura -> sold();
	}
	else {
		$suma = $incasare -> sumaNeasociata();
	}
	
	$asociere = new IncasariAsocieri();
	$asociere -> incasare_id = $incasare -> id;
	$asociere -> factura_id = $factura -> id;
	$asociere -> suma = $suma;
	$asociere -> save();
	
	if($factura -> sold() == 0) {
		$factura -> achitat = 'DA';
		$factura -> save();
	}
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'))");
	return $objResponse;
}

function disociazaIncasare($incasare_id) {
	global $db;
	if(!$incasare_id) {
		return alert('Selectati o incasare');
	}
	$asocieri = new IncasariAsocieri("where incasare_id = '$incasare_id'");
	foreach($asocieri as $asociere) {
		$factura = new Facturi($asociere -> factura_id);
		$factura -> achitat = 'NU';
		$factura -> save();
		$asociere -> delete();
	}
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'))");
	return $objResponse;
}

function stergeIncasare($incasare_id) {
	if(!$incasare_id) {
		return alert('Selectati o incasare!');
	}
	$incasare = new Incasari($incasare_id);
	if($incasare -> incasare_efect_id > 0) {
		$efect = new IncasariEfecte($incasare -> incasare_efect_id);
		$efect -> operat = 'NU';
		$efect -> suma_acceptata = '0.00';
		$efect -> raspuns = '??';
		$efect -> save();
	}
	disociazaIncasare($incasare_id);
	$incasare -> delete();
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'))");
	return $objResponse;
}



//functii efecte cmerciala

function frmEfect($efect_id=0, $tert_id=0, $societate_id=0) {
	$efect = new IncasariEfecte($efect_id);
	$efect -> tert_id = $tert_id;
	$efect -> societate_id = $societate_id;
	if($efect_id) {
		$efect -> data_emitere = c_data($efect -> data_emitere);
		$efect -> data_scadenta = c_data($efect -> data_scadenta);
		if($efect -> operat == 'DA') {
			return alert('Efectul a fost operat si nu poate fi editat!');
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
	$dialog -> addButton("Salveaza", 'xajax_salveazaEfect(xajax.getFormValues(\'frm_incasari_efecte\'));<%close%>');
	
	$objResponse = openDialog($dialog); 
	copyResponse($objResponse, initControl());
	$objResponse -> script("\$('#numar_doc').focus().select();");
	return $objResponse;
}

function salveazaEfect($frmValues) {
	$efect = new IncasariEfecte($frmValues);
	$objResponse = new xajaxResponse();
	if(!$efect -> validate($objResponse)) {
		return $objResponse;
	}
	$efect -> data_emitere = data_c($efect -> data_emitere);
	$efect -> data_scadenta = data_c($efect -> data_scadenta);
	$efect -> gestiune_id = $_SESSION['user'] -> gestiune_id;
	$efect -> save();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}

function stergeEfect($efect_id) {
	global $db;
	if(!$efect_id) {
		return alert("Selectati un efect comercial!");
	}
	
	$incasare = new Incasari("where incasare_efect_id = '$efect_id'");
	if(count($incasare)) {		
		$db -> query("delete from `incasari_asocieri` where `incasare_id` = '". $incasare -> id ."'");
		$incasare -> delete();
	}	
	
	$efect = new IncasariEfecte($efect_id);
	$efect -> delete();
	
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_cautare(xajax.getFormValues('frmCautare'));");
	return $objResponse;
}


function operareEfect($efect_id) {
	if(!$efect_id) {
		return alert('Selectati un efect de comert!');
	}
	$efect = new IncasariEfecte($efect_id);
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
	$efect = new IncasariEfecte($efect_id);
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

function sumarFactura($factura_id) {
	$factura = new Facturi($factura_id);
	$dialog = new Dialog(800, 600, "", "win_sumar_factura");
	$dialog -> title = "Sumar Factura";
	$dialog -> append('<div id="acord" >');
	$dialog -> append('<div><h3><a href="#">Info</a></h3><div>');
	$dialog -> append($factura -> sumar());
	$dialog -> append('</div></div>');
	$dialog -> append(
	'	
			<div >
				<h3><a href="#">Continut factura</a></h3>
				<div><div style="height:300px;overflow:scroll; overflow-x:hidden;">
				'. $factura -> continut -> lista() .'</div>
				</div>
			</div>	
	'
	);
	$dialog -> append(
	'		<div >
				<h3><a href="#">Plati asociate</a></h3>
				<div><div style="height:300px;overflow:scroll; overflow-x:hidden;">
			'. $factura -> listaIncasariAsociate() .'</div>
				</div>
			</div>
		</div>	
			
	'
	);
	
	$dialog -> addButton("Renunta");
	$objResponse = openDialog($dialog);
	$objResponse -> script("$('#acord').accordion({header: 'h3', animated: false});");
	return $objResponse;
}

function situatieActuala($tert_id) {
	$tert = new Terti($tert_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_situatie_actuala", "innerHTML", $tert -> situatieActualaClient($_SESSION['user'] -> gestiune_id));
	return $objResponse;
}

function situatieGlobala($tert_id, $filtre) {
	$objResponse = new xajaxResponse();
	$sql = "WHERE tert_id = '$tert_id'";
	if($filtre['from'] && $filtre['from']) {
		$sql .= " and data_factura between '".$filtre['from']."' and '".$filtre['end']."'";
	}
	$gestiune = new Gestiuni();
	$gestiune -> getGestiuneActiva();
	$sql .= " and societate_id = '". $gestiune -> punct_lucru -> societate_id ."'";
	$facturi = new ViewFacturi($sql);
	$out .= '<h2 align="center">SITUATIE GLOBALA CLIENT</h2>';
	$out .= '<div align="center">SITUATIE GLOBALA CLIENT</div>';	
	$out .= $facturi -> listaSituatieGlobala();
	$objResponse -> assign("grid_situatie_globala", "innerHTML", $out);
	return $objResponse;
}
?>