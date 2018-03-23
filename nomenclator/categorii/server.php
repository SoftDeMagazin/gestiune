<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$sql = " inner join categorii_gestiuni using(categorie_id) ";
	$sql .= " where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";	
	if($frmFiltre['denumire']) {
		$sql .= " and denumire like '%". $frmFiltre['denumire'] ."%'";
	}
	$sql .= " order by denumire asc";
	$model = new Categorii($sql);
	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	$model -> pageLength($frmPager['pagesize']);
	
	$info = paginator($action, $model, $frmPager['curentpage']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%categorie_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function frm($id=0)
{
	$model = new Categorii($id);
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_categorii\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
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
	$objResponse -> script("$('#tabs').tabs('disable', 1);");
	$objResponse -> assign("frm", "innerHTML", "");
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array()) 
{
	global $db;
	$model = new Categorii($frmValues);
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

	$model = new Categorii($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

?>