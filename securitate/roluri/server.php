<?php
session_start();
header("Cache-control: private"); // IE 6 Fix 
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	if($frmFiltre['rol']) {
	$model = new Roluri("where rol like '%". $frmFiltre['rol'] ."%' order by rol asc");
	}
	else {
	$model = new Roluri("where 1 order by rol asc");
	}
	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	$model -> pageLength($frmPager['pagesize']);
	
	$info = paginator($action, $model, $frmPager['curentpage']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%rol_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}

function frm($id=0)
{
	$model = new Roluri($id);
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> script("$('#tabs').tabs('enable', 1);");
	if($model -> id) {
		$objResponse -> script("$('#tabs').tabs('enable', 2);");
	}
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_roluri\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
   <input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
 </div>

	';
	
	$objResponse -> append("frm", "innerHTML", $btn);
	copyResponse($objResponse, switchTab('frm'));
	$objResponse -> script("\$('#rol').focus().select();");
	return $objResponse;
}

function cancel() 
{
	$objResponse = switchTab('lista');
	$objResponse -> assign("frm", "innerHTML", "");
	$objResponse -> script("$('#tabs').tabs('disable', 1);$('#tabs').tabs('disable', 2);");
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array()) 
{
	$model = new Roluri($frmValues);
	$objResponse = new xajaxResponse();
	if(!$model -> validate($objResponse)) {
		return $objResponse;
	}
	$model -> save();
	$objResponse = lista($frmFiltre, $frmPager, "default", $model -> id);
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array()) {

	$model = new Roluri($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

function loadDrepturi($rol_id, $modul_id) {
	$rol = new Roluri($rol_id);
	$modul = new Module($modul_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_modul", "innerHTML", $modul -> denumire);
	$objResponse -> assign("div_load_drepturi", "innerHTML", $rol -> afisareDrepturi($modul_id));
	return $objResponse;
}

function clickDrept($rol_id, $modul_id, $drept_id, $state) {
	$drept = new Drepturi($drept_id);
	if($state) {
		$rd = new RoluriDrepturi();
		$rd -> rol_id = $rol_id;
		$rd -> modul_id = $modul_id;
		$rd -> drept_id = $drept_id;
		$rd -> save();
	} else {
		$rd = new RoluriDrepturi("where rol_id = '$rol_id' and modul_id = '$modul_id' and drept_id = '$drept_id'");
		if(count($rd) == 1) $rd -> delete();
	}
	$objResponse = new xajaxResponse();
	return $objResponse;
}

?>