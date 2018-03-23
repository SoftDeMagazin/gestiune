<?php
session_start();
header("Cache-control: private"); // IE 6 Fix
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$model = new RoluriDrepturi();
	$sql = "where 1 ";
	
	if($frmFiltre['rol_id'] && $frmFiltre['rol_id'] > 0){
		$sql .= " and rol_id = '". $frmFiltre['rol_id'] ."'";
	}	
	
	if($frmFiltre['modul_id'] && $frmFiltre['modul_id'] > 0){
		$sql .= " and modul_id = '". $frmFiltre['modul_id'] ."'";
	}	
	
	if($frmFiltre['drept_id'] && $frmFiltre['drept_id'] > 0){
		$sql .= " and drept_id = '". $frmFiltre['drept_id'] ."'";
	}	
	
	$sql .=" order by rol_id asc";
	
	$model -> prepareQuery($sql);
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%rol_drept_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}

function frm($id=0)
{
	$model = new RoluriDrepturi($id);
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_roluri_drepturi\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
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
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array())
{
	$model = new RoluriDrepturi($frmValues);
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

	$model = new RoluriDrepturi($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

?>