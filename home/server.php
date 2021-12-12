<?php
header("Cache-control: private"); // IE 6 Fix 
require_once("common.php");
require_once(DOC_ROOT.'app/thirdparty/nusoap/nusoap.php');
$xajax->processRequest();
function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$model = new ViewProduseGestiuni();
	$sql = " where gestiune_id = ". $_SESSION['user'] -> gestiune_id ." ";
	if($frmFiltre['denumire']) {
		$sql .= " and denumire like '%". $frmFiltre['denumire'] ."%'";
	}
	
	if(isset($frmFiltre['categorie_id']) && $frmFiltre['categorie_id'][0]) {
		$in = implode(",", $frmFiltre['categorie_id']);
		$sql .= " and categorie_id in (". $in .")";
	}
	
	if(isset($frmFiltre['tip_produs']) && $frmFiltre['tip_produs'][0]) {
		$in = "'".implode("','", $frmFiltre['tip_produs'])."'";
		$sql .= " and tip_produs in (". $in .")";
	}
		
	$sql .= " order by denumire asc";
	$model -> prepareQuery($sql);

	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = $model -> expectedResult();
	
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}
function actualizareCurs($curs_val, $valuta) {
	global $db;
	if(!$_SESSION['user']) {
		return xLogin();
	}
	$curs = new Cursuri();
	$curs -> gestiune_id = $_SESSION['user'] -> gestiune_id;
	$curs -> valoare = $curs_val;
	$curs -> valuta = $valuta;
	$curs -> save();
	if($valuta == 'EUR') {
	$db -> query("
		update produse_gestiuni inner join produse using(produs_id) set pret_val = round(pret_ron/". $curs_val .",2)
		where produse_gestiuni.gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' 
		and produse.pret_referinta = 'LEI'
	");
	$db -> query("
		update produse_gestiuni inner join produse using(produs_id) set pret_ron = round(". $curs_val ."*pret_val,2)
		where produse_gestiuni.gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' 
		and produse.pret_referinta = 'EUR'
	");
	}
	$objResponse = alert('Cursul '. $valuta .' a fost actualizat');
	$objResponse -> assign("curs_valutar", "value", $curs_val);
	$objResponse -> script("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));");
	return $objResponse;
}

function afiseazaCurs($valuta) {
	$curs = new Cursuri();
	$curs -> getLast($valuta);
	if(count($curs)) {
		$valoare = $curs -> valoare;
	} else {
		$valoare = '0.00';
	}
	$objResponse = new xajaxResponse();
	$objResponse -> assign("curs_valutar", "value", $valoare);
	return $objResponse;
}


function infoCursValutar() {
	$dialog = new Dialog(400, 300, "", "win_info_curs");
	$dialog -> title = "Info curs euro BNR";
	$wsdl = "http://www.infovalutar.ro/curs.asmx?WSDL";
	$client = new nusoap_client($wsdl, true);
	$result = $client->call('GetLatestValue', array('parameters' => array('Moneda'=>'EUR')));
	$val_EUR= $result['GetLatestValueResult'];	
	$dialog -> append("1 EUR = ".$val_EUR." LEI");
	$dialog -> addButton("Inchide");
	$dialog -> addButton("Seteaza acest curs in gestiune", "xajax_actualizareCurs('$val_EUR', 'EUR');<%close%>");
	return $dialog -> open();
}
?>