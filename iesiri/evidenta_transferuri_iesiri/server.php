<?php
header("Cache-control: private"); // IE 6 Fix 
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$sql = "WHERE gestiune_sursa_id=".$_SESSION['user']->gestiune_id;
	if($frmFiltre['data']) {
		$sql .= " and DATEDIFF(data,'". data_c($frmFiltre['data'])."')=0";
	}
	
	if($frmFiltre['gestiune_id']) {
		$in = implode(",", $frmFiltre['gestiune_id']);
		$sql .= " and gestiune_destinatie_id in (". $in .")";
	}
	
	if($frmFiltre['isValid']){
		$sql .=" and valid=1";
	}
		
	$model = new Transferuri($sql);

	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	$model -> pageLength($frmPager['pagesize']);
	
	$info = paginator($action, $model, $frmPager['curentpage']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%transfer_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}

function frm($id)
{
	$objResponse = new xajaxResponse();
	$objResponse->redirect(DOC_ROOT."/iesiri/transferuri?transfer_id=".$id);
	return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array()) {

	if(!$id)
		return alert("Selectati un trasfer");
	$model = new Transferuri($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	return $objResponse;
}

function listeaza($transfer_id)
{
	if(!$transfer_id)
		return alert("Selectati un transfer.");
		
	$transfer_print = new TransferPrint($transfer_id);
	$objResponse = new xajaxResponse();
	//$objResponse -> script("$('#tabs').tabs('enable', 1);");
	//copyResponse($objResponse, switchTab('printare'));
	$objResponse->assign("transfer_print","innerHTML",$transfer_print->getHtml());
	$objResponse -> script("CallPrintContent('transfer_print');");
	return $objResponse;
}

?>