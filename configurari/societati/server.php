<?php
session_start();
header("Cache-control: private"); // IE 6 Fix 
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	if($frmFiltre['denumire']) {
	$model = new Societati("where denumire like '%". $frmFiltre['denumire'] ."%' order by denumire asc");
	}
	else {
	$model = new Societati("where 1 order by denumire asc");
	}
	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	$model -> pageLength($frmPager['pagesize']);
	
	$info = paginator($action, $model, $frmPager['curentpage']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%societate_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function frm($id=0)
{
	$model = new Societati($id);
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_societati\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
   <input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
 </div>

	';
	$objResponse -> append("frm", "innerHTML", $btn);
	
	copyResponse($objResponse, switchTab('frm'));
	
	if($id) {
		copyResponse($objResponse, lista_conturi($id));
	}
	$objResponse -> script("\$('#denumire').focus().select();");
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
	$model = new Societati($frmValues);
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

	$model = new Societati($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

function lista_conturi($societate_id) {
	$conturi = new SocietatiConturi("where societate_id = '$societate_id'");
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_conturi", "innerHTML", $conturi -> lista());
	return $objResponse;
}

function frm_cont($id, $societate_id) {
	$cont = new SocietatiConturi($id);
	$cont -> societate_id = $societate_id;
	$dialog = new Dialog(800, 600, "", "win_frm_cont");
	$dialog -> title = "Adaugare Editare Cont";
	$dialog -> append($cont -> frmDefault());
	
	$dialog -> addButton("Salveaza", "xajax_save_cont(xajax.getFormValues('frm_societati_conturi'));<%close%>");
	$dialog -> addButton("Renunta");
	return $dialog -> open();
}


function save_cont($frm) {
	$cont = new SocietatiConturi($frm);
	$cont -> save();
	
	$objResponse = lista_conturi($cont -> societate_id);
	return $objResponse;
}

function sterge_cont($id) {
	if(!$id) return alert('Selectati un cont bancar!');
	$cont = new SocietatiConturi($id);
	$societate_id = $cont -> societate_id;
	$cont -> delete();
	return lista_conturi($societate_id);
}
?>