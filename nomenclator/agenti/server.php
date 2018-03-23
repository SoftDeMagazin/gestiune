<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$sql = " inner join agenti_gestiuni using(agent_id) WHERE gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";
	if($frmFiltre['denumire']) {
		$sql .= " and nume like '%". $frmFiltre['denumire'] ."%'";
	}
	
	$sql .=  " order by `nume` asc";
	$model = new Agenti($sql);
	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	$model -> pageLength($frmPager['pagesize']);
	
	$info = paginator($action, $model, $frmPager['curentpage']);
	
	$list_columns = array("nume" => "Nume Agent"
		);
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> listaDefault($list_columns,"", "xajax_frm('<%agent_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function frm($id=0)
{
	$model = new Agenti($id);
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_agenti\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
   <input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
 </div>

	';
	$objResponse -> append("frm", "innerHTML", $btn);
	$gest = new Gestiuni("where 1");
	if($id) {
		$selected = $model -> getGestiuniAsociate();
	}
	else {
		$selected = array($_SESSION['user'] -> gestiune_id);
	}
	$objResponse -> script("$('#tabs').tabs('enable', 1);");
	copyResponse($objResponse, switchTab('frm'));
	$objResponse -> assign("div_frm_gest", "innerHTML", "Gestiune<br />
".$gest -> selectMulti($selected));
	$objResponse -> script("$('#gestiune_id').multiSelect()");
	$objResponse -> script("\$('#denumire').focus().select();");
	return $objResponse;
}

function cancel() 
{
	$objResponse = switchTab('lista');
	$objResponse -> assign("frm", "innerHTML", "");
	$objResponse -> script("$('#tabs').tabs('disable', 1);");
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array()) 
{
	global $db;
	$model = new Agenti($frmValues);
	$objResponse = new xajaxResponse();
	if(!$model -> validate($objResponse)) {
		return $objResponse;
	}
	$model -> save();
	$model -> disociazaGestiuni($frmValues['gestiune_id']);
	$model -> asociazaCuGestiuni($frmValues['gestiune_id']);
	$objResponse = lista($frmFiltre, $frmPager, "default", $model -> id);
	copyResponse($objResponse, switchTab('lista'));
	$objResponse -> script("$('#tabs').tabs('disable', 1);");
	return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array()) {

	$model = new Agenti($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

function afiseazaComisioane($agent_id) {
	$ats = new AgentiTerti("where agent_id = '$agent_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid-comisioane", "innerHTML", $ats -> lista());
	return $objResponse;
}

function salveazaComisioane($frm) {
	$nr_r = count($frm['agent_tert_id']);
	for($i=0;$i<$nr_r;$i++) {
		$at = new AgentiTerti($frm['agent_tert_id'][$i]);
		$at -> comision = $frm['comision'][$i];
		$agent_id = $at -> agent_id;
		$at -> save();
		
	}
	$objResponse = afiseazaComisioane($agent_id);
	return $objResponse;
}

function filtruProduse($filtru) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	if($filtru) {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."' 
		and denumire like '$filtru%' 
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}
	else {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."'
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}		
}


function selectProdus($produs_id) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("produs_id", "value", $produs -> id);
	$objResponse -> assign("div_info_produs", "innerHTML", $produs -> denumire);
	if($produs -> pret_val) $pret = $produs -> pret_val;
	else $pret = "0";
	
	$stoc = new Stocuri("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	if(count($stoc)) {
		$stoc_cant = $stoc -> stoc;
		$stoc_val = $stoc -> valoare_stoc_ron;
		$pmp = douazecimale($stoc_val/$stoc_cant);
	}
	else {
		$stoc_cant = 0;
		$stoc_val = 0;
		$pmp = 0;
	}
	
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and cantitate_ramasa <> 0");
	
	$objResponse -> assign('div_detalii_produs', 'innerHTML', '
	<strong>Ambalare:</strong>'. $produs -> ambalare .'</br> 
	<strong>Stoc:</strong> <a href="#" onClick="xajax_infoLoturi(\''. $produs_id .'\'); return false;">'. $stoc_cant .'</a><br />
	');
	
	$objResponse -> append("div_detalii_produs", "innerHTML", Html::overflowDiv($loturi -> infoLoturi(), "130px"));
	$objResponse -> assign("div_frm_unitate_masura", "innerHTML", $produs -> unitate_masura());
	$objResponse -> script("\$('#cantitate').focus().select();");
	return $objResponse;
}

function infoLoturi($produs_id) {
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and cantitate_ramasa <> 0");
	$produs = new Produse($produs_id);
	if(count($loturi)) {
		$dialog = new Dialog(600, 400, "", "win_info_loturi");
		$dialog -> title = "Loturi: ".$produs -> denumire;
		$dialog -> append($loturi -> lista());
		$objResponse = $dialog -> open();
		$objResponse -> script("$('#win_info_loturi .tablesorter').tablesorter()");
		return $objResponse;
	}
	else return alert('Nu sunt loturi inregistrate pentru acest produs');
}
?>