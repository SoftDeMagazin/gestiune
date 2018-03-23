<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}
	$model = new ViewProduseGestiuni();
	
	$sql = " left join stocuri on (view_produse_gestiuni.produs_id = stocuri.produs_id and view_produse_gestiuni.gestiune_id = stocuri.gestiune_id) 
	where view_produse_gestiuni.gestiune_id = ". $_SESSION['user'] -> gestiune_id ." 
	and view_produse_gestiuni.tip_produs in ('marfa', 'mp')
	";
	if($frmFiltre['denumire']) {
		$sql .= " and denumire like '%". $frmFiltre['denumire'] ."%'";
	}
	
	if($frmFiltre['categorie_id']) {
		$in = implode(",", $frmFiltre['categorie_id']);
		$sql .= " and categorie_id in (". $in .")";
	}
	
	if($frmFiltre['tip_produs']) {
		$in = "'".implode("','", $frmFiltre['tip_produs'])."'";
		$sql .= " and tip_produs in (". $in .")";
	}
	
	if($frmFiltre['cu_stoc']) {
		$sql .= " and stoc > 0";
	}
		
	$sql .= " order by denumire asc";
	$model -> prepareQuery($sql);

	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = $model -> expectedResult();
	
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> listaStocuri("", "xajax_evidentaLoturi('<%produs_id%>');", $selected));
	//$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function evidentaLoturi($produs_id, $filtre=array()) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
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
	if(!$_SESSION['user']) {
		return xLogin();
	}
	$sql = "where `lot_id` = '$lot_id'";
	$sql .= " and `gestiune_id` = '". $_SESSION['user'] -> gestiune_id ."'";
	$facturiIesiri = new FacturiIesiri($sql);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_iesiri", "innerHTML", "<h4>Facturi</h4>".$facturiIesiri -> evidentaIesiri());
	
	$transferuri = new TransferuriIesiri($sql); 
	$objResponse -> append("grid_iesiri", "innerHTML", "<h4>Transferuri</h4>". $transferuri -> evidentaIesiri());
	$avize = new AvizeIesiri($sql); 
	$objResponse -> append("grid_iesiri", "innerHTML", "<h4>Avize</h4>". $avize -> evidentaIesiri());
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
	$objResponse -> assign("grid_fisa_magazie", "innerHTML", "");
	return $objResponse;
} 

function printDoc() {
	$dialog = new Dialog(800, 600, '', 'win_print_stoc');
	$dialog -> title = "Printare stoc";
	$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
	
	$dialog -> append(Html::form("frmPrintStoc", array("action" => DOC_ROOT."print/raport.php", "target" => "print", "method" => "post", "onSubmit" => "return popup('', this.target);")));
	$dialog -> append(Html::hidden("rpt_name", "RptStocuri"));
	$dialog -> append($gestiune -> gestiune_id());
	
	$categorii = new Categorii("inner join categorii_gestiuni using(categorie_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
	$txt = $categorii -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");

	$dialog -> append("Categorie<br>".$txt);
	
	$tip = new TipuriProduse("where cu_stoc = 1 order by descriere asc");
	$txt = $tip -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
	
	$dialog -> append("<br>Tip Produs<br>".$txt);
	
	
	$dialog -> append("<br>".Html::submit("print", "Print"));
	
	$dialog -> append(Html::formEnd());
	
	
	$objResponse = $dialog -> open();
	$objResponse -> script("$('#frmPrintStoc #categorie_id').multiSelect()");
	$objResponse -> script("$('#frmPrintStoc #tip_produs').multiSelect()");
	return $objResponse;
}
?>