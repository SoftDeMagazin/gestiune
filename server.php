<?php
require_once("common.php");
$xajax->processRequest();

function switchGest($gestiune_id) {
	$_SESSION['user'] -> gestiune_id = $gestiune_id;
	setcookie("gestiune_id", $_SESSION['user'] -> gestiune_id, time()+60*60*24*30);
	$gestiune = new Gestiuni($gestiune_id);
	$dialog = new Dialog(400, 300, '', "win_info_gestiune");
	$dialog -> title = "Info";
	$dialog -> modal = true;
	$dialog -> addButton("Ok", "<%close%>");
	$dialog -> append('Lucrati pe gestiune '. $gestiune -> denumire .' - '. $gestiune -> punct_lucru -> societate -> denumire .'');	
	return $dialog -> open();
}

function logOut() {
	session_destroy();
	$objResponse = new xajaxResponse();
	$objResponse -> script("window.location.href = 'index.php'");
	return $objResponse;
}

function more() {
	$objResponse = new xajaxResponse();
	$dialog = new Dialog(400, 300, '', "win_more_gests");
	$dialog -> modal = true;
	foreach($_SESSION['user'] -> gestiuni_asociate as $id) {
		$gest = new Gestiuni($id);
		$dialog -> append(
			'<input type="button" style="width:100%" value="'. $gest -> denumire .'" onClick="xajax_switchGest('. $gest -> id .');$(\'#gest_name\').html(this.value);$(\'#win_more_gests\').dialog(\'close\');"><br />'
		); 
	}
	return $dialog -> open();
}
?>