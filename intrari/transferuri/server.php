<?php 
header("Cache-control: private"); // IE 6 Fix
require_once ("common.php");
$xajax->processRequest();

function lista($frmFiltre = array(), $frmPager = array(), $action = "first", $selected = 0) {
    $sql = "WHERE gestiune_destinatie_id=".$_SESSION['user']->gestiune_id;
    $sql .= " AND salvat=1 and valid=0";
    if ($frmFiltre['data']) {
        $sql .= " and DATEDIFF(data,'".data_c($frmFiltre['data'])."')=0";
    }
    
    if ($frmFiltre['gestiune_id']) {
        $in = implode(",", $frmFiltre['gestiune_id']);
        $sql .= " and gestiune_sursa_id in (".$in.")";
    }
	
    if($frmFiltre['isValid']){
		$sql .=" and valid=1";
	}
	
    $model = new Transferuri($sql);
    if ($frmPager['pagesize'] == 1)
        $frmPager['pagesize'] = count($model);
    $model->pageLength($frmPager['pagesize']);
    
    $info = paginator($action, $model, $frmPager['curentpage']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplay", "value", $info['pagedisplay']);
    $objResponse->assign("curentpage", "value", $info['curentpage']);
    $objResponse->assign("grid", "innerHTML", $info['page']->lista("", "xajax_show_transfer('<%transfer_id%>')", $selected));
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function validateTransfer($id, $frmFiltre = array(), $frmPager = array()) {
	if (!$id) {
        return alert('Selectati un transfer!');
    }
    
    $transfer = new Transferuri($id);
    $transfer->valid = 1;
	$transfer->data = data_c($transfer->data);
    $transfer->save();
    $objResponse = new xajaxResponse();
	$objResponse = lista($frmFiltre, $frmPager, "default");
    return $objResponse;
}

function show_transfer($transfer_id)
{
	$transfer = new Transferuri($transfer_id);
	
	$dialog = new Dialog(800, 600, "", "win_sumar_transfer");
	$dialog -> title = "Sumar Transfer";
	$dialog -> append($transfer -> sumar());
	$dialog -> append(
	'<fieldset>
	<legend>Continut transfer</legend>
	<div style="height:300px;overflow:scroll; overflow-x:hidden;">
	'. $transfer -> continut -> lista() .'
	</div>
	</fieldset>
	'
	);
	$dialog -> addButton("Renunta");
	$dialog -> addButton("Editeaza", "window.location.href = '/iesiri/introducere_factura/?factura_id=".$factura -> factura_id."';");
	$dialog -> addButton("Tiparire");
	$objResponse = openDialog($dialog);
	return $objResponse;
}
?>
