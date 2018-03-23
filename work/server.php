<?php
require_once("common.php");
$xajax->processRequest();

function lista($page=1)
{
	$model = new ViewProduseGestiuni();
	$sql = " left join stocuri on (view_produse_gestiuni.produs_id = stocuri.produs_id and view_produse_gestiuni.gestiune_id = stocuri.gestiune_id) where view_produse_gestiuni.gestiune_id = 7 ";

	$sql .= " order by denumire asc";
	$model -> nrPages($pageLength);
	$model -> prepareQuery($sql);
	
	$info = paginated("topage", $model, $page, 19);
	$objResponse = new xajaxResponse();
	$count = $model -> nrPages(19);
	$h = $count*500;
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	if($page==1) $objResponse -> assign("grid-content", "style.height", $h."px");
	$objResponse -> assign("data", "innerHTML", $info['page'] -> listaStocuri("", "xajax_evidentaLoturi('<%produs_id%>');"));
	return $objResponse;
}


function evidentaLoturi($produs_id, $filtre=array()) {
	$sql = "where `produs_id` = '$produs_id'";
	$sql .= " and `gestiune_id` = '". $_SESSION['user'] -> gestiune_id ."'";
	if($filtre['from'] && $filtre['end']) {
		$sql .= " and data_intrare between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'";
	}
	$loturi = new Loturi($sql);
	$produs = new Produse($produs_id);
	$objResponse = new xajaxResponse();
	copyResponse($objResponse, enableTab('1'));
	copyResponse($objResponse, enableTab('2'));
	copyResponse($objResponse, switchTab('loturi'));
	$objResponse -> assign("div_info_produs", "innerHTML", $produs -> denumire);
	$objResponse -> assign("grid_loturi", "innerHTML", $loturi -> evidentaLoturi("xajax_evidentaIesiri('<%lot_id%>');"));
	$objResponse -> assign("produs_id", "value", $produs_id);
	$objResponse -> assign("grid_iesiri", "innerHTML", "");
	return $objResponse;
}

function evidentaIesiri($lot_id) {
	$sql = "where `lot_id` = '$lot_id'";
	$sql .= " and `gestiune_id` = '". $_SESSION['user'] -> gestiune_id ."'";
	$facturiIesiri = new FacturiIesiri($sql);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_iesiri", "innerHTML", "<h4>Facturi</h4>".$facturiIesiri -> evidentaIesiri());
	$objResponse -> append("grid_iesiri", "innerHTML", "<h4>Transferuri</h4>");
	return $objResponse;
}

function fisaMagazie($produs_id, $filtre) {
	global $db;

	$filtre['gestiune_id'] = $_SESSION['user'] -> gestiune_id;
	$fisa = new FisaMagazie($produs_id, $filtre);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_fisa_magazie", "innerHTML", $fisa -> getHtml());
	return $objResponse;			
}

function cancel() {
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_loturi", "innerHTML", "");
	$objResponse -> assign("grid_iesiri", "innerHTML", "");
	copyResponse($objResponse,switchTab('lista'));
	copyResponse($objResponse,disableTab('1'));
	copyResponse($objResponse,disableTab('2'));
	return $objResponse;
} 
?>