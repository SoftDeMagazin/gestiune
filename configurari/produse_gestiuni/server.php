<?php 
header("Cache-control: private"); // IE 6 Fix
require_once ("common.php");
$xajax->processRequest();

function lista($frmFiltre = array(), $frmPager = array(), $action = "first", $selected = 0) {
    $model = new ViewProduseGestiuni();
    
    $sql = " where 1";
    
    if ($frmFiltre['denumire']) {
        $sql .= " and denumire like '%".$frmFiltre['denumire']."%'";
    }
    
    if ($frmFiltre['filtru_gestiune']) {
        $in = implode(",", $frmFiltre['filtru_gestiune']);
        $sql .= " and gestiune_id in (".$in.")";
    }
    
    $sql .= " order by denumire asc";
    $model->prepareQuery($sql);
    
    if ($frmPager['pagesize'] == 1)
        $frmPager['pagesize'] = $model->expectedResult();
        
    $info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplay", "value", $info['pagedisplay']);
    $objResponse->assign("curentpage", "value", $info['curentpage']);
    $objResponse->assign("grid", "innerHTML", $info['page']->lista_gestiuni("", "$('#tabs').tabs('enable', 1);xajax_frm('<%produs_gestiune_id%>')", $selected));
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function selectProdus($produs_id) {
    $produs = new Produse($produs_id);
	
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", $produs->id);
    $objResponse->assign("produs", "value", $produs->denumire);
	
    return $objResponse;
}

function save($frmValues,$frmFiltre = array(), $frmPager = array()) {
    if (!$frmValues['gestiune_id'])
        return alert("Selectati o gestiune.");
        
    $produs_id = $frmValues['produs_id'];
    $pret_ron = $frmValues['pret_ron'];
    $pret_val = $frmValues['pret_val'];
	
	$gestiune_id = $frmValues['gestiune_id'];
	$sql = "WHERE produs_id=".$produs_id." AND gestiune_id=".$gestiune_id;
    $pg = new ProduseGestiuni($sql);
        
    $pg->pret_ron = $pret_ron;
    $pg->pret_val = $pret_val;
    $pg->modificat = 1;
        
    $pg->save();
    
    /*foreach ($frmValues['gestiune_id'] as $gestiune_id) {
        $sql = "WHERE produs_id=".$produs_id." AND gestiune_id=".$gestiune_id;
        $pg = new ProduseGestiuni($sql);
        
        $pg->pret_ron = $pret_ron;
        $pg->pret_val = $pret_val;
        $pg->modificat = 1;
        
        $pg->save();
    }*/
    
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", "");
    $objResponse->assign("produs", "value", "");
    $objResponse->assign("pret_ron", "value", "");
    $objResponse->assign("pret_val", "value", "");
	$objResponse->assign("gestiune_id", "value", "");
	copyResponse($objResponse, switchTab('lista'));
	copyResponse($objResponse, lista($frmFiltre, $frmPager, "default"));
    return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array()) {
    $model = new ProduseGestiuni($id);
    $model->delete();
    $objResponse = lista($frmFiltre, $frmPager, "default");
    copyResponse($objResponse, switchTab('lista'));
    return $objResponse;
}

function frm($id = 0) {
    $model = new ProduseGestiuni($id);
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, switchTab('edit'));
   
    if ($id) {  
        copyResponse($objResponse, selectProdus($model->produs_id));
        $objResponse->assign('pret_ron', 'value', $model->pret_ron);
        $objResponse->assign('pret_val', 'value', $model->pret_val);
		
		$gestiune = new Gestiuni($model->gestiune_id);
		$objResponse->assign('gestiune_id', 'value', $model->gestiune_id);
		$objResponse->assign("gestiune", "value", $gestiune->denumire);
    }
	
    return $objResponse;
}

?>
