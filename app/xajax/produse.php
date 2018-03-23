<?php
function infoNC8($code) {
	$dialog = new Dialog(500, 400, "", "win_gbl_info_nc8");
	$dialog -> title = $code;
	$cn = new Cn2009($code);
	$dialog -> append($cn -> getDescription());
	$dialog -> addButton("Ok", "<%close%>");
	return openDialog($dialog);
}
$xajax -> registerFunction("infoNC8");

function gblInfoLoturi($produs_id, $gestiune_id) {
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '$gestiune_id' and cantitate_ramasa <> 0");
	$produs = new Produse($produs_id);
	if(count($loturi)) {
		$dialog = new Dialog(600, 400, "", "win_gbl_info_loturi");
		$dialog -> title = "Loturi: ".$produs -> denumire;
		$dialog -> append(Html::overflowDiv($loturi -> lista(), "200px"));
		$dialog -> addButton("Inchide");
		$objResponse = $dialog -> open();
		
		$objResponse -> script("$('#win_gbl_info_loturi .tablesorter').tablesorter()");
		return $objResponse;
	}
	else return alert('Nu sunt loturi inregistrate pentru acest produs');
}
$xajax -> registerFunction("gblInfoLoturi");
?>