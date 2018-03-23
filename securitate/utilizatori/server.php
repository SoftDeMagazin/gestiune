<?php
session_start();
header("Cache-control: private"); // IE 6 Fix 
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$model = new Utilizatori();
	$sql = "where 1 ";
	
	if($frmFiltre['user_name']){
		$sql .= " and user_name like '%". $frmFiltre['user_name'] ."%'";
	}
	if($frmFiltre['rol_id'] && $frmFiltre['rol_id'] > 0){
		$sql .= " and rol_id = '". $frmFiltre['rol_id'] ."'";
	}		

	$sql .=" order by user_name asc";
	
	//$model = new Utilizatori($sql);
	$model -> prepareQuery($sql);
	//if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	//$model -> pageLength($frmPager['pagesize']);
	
	//$info = paginator($action, $model, $frmPager['curentpage']);
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%utilizator_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function frm($id=0)
{
	$model = new Utilizatori($id);
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_utilizatori\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
   <input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
 </div>

	';
	$objResponse -> append("frm", "innerHTML", $btn);
	if($model -> id) {
		$objResponse -> assign("gestiuni", "innerHTML", $model -> afisareGestiuni());
	}
	copyResponse($objResponse, switchTab('frm'));
	$objResponse -> script("\$('#user_name').focus().select();");
	return $objResponse;
}

function cancel() 
{
	$objResponse = switchTab('lista');
	$objResponse -> assign("frm", "innerHTML", "");
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array()) 
{
	$model = new Utilizatori($frmValues);
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

	$model = new Utilizatori($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

function clickGestiune($user_id, $gestiune_id, $state) {
	if($state) {
		$gu = new GestiuniUtilizatori();
		$gu -> utilizator_id = $user_id;
		$gu -> gestiune_id = $gestiune_id;
		$gu -> save();
	} else {
		$gu = new GestiuniUtilizatori("where utilizator_id = '$user_id' and gestiune_id='$gestiune_id'");
		$gu -> delete();
	}
	$objResponse = new xajaxResponse();
	return $objResponse;
}

?>