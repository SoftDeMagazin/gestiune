<?php
require_once("common.php");
require_once(DOC_ROOT."common/user_profile.php");
$xajax->processRequest();
function login($login_frm=array()){
	$objResponse = new xajaxResponse();
	global $db;
	$username = addslashes($db -> escape($login_frm['username']));
	$pass = addslashes($db -> escape($login_frm['pass']));
	$gest_id = $login_frm['gestiune_id'];

	global $db;
	$sql = "SELECT u.utilizator_id,u.user_name,u.nume,u.rol_id FROM ".
		"utilizatori u ".
		"WHERE u.user_name='".$username."' AND u.parola='".$pass."'";
		
	$user_data = $db->getRow($sql);
	if($user_data == NULL)
	{
		$dialog = new Dialog(300, 200, "", "win_window_name"); 
		$dialog -> append('<div>Utilizator/parola gresite.</div>');
		$dialog->title = "Autentificare esuata";
		$dialog -> addButton("Ok", "<%close%>");
		return openDialog($dialog);
		//return alert("Utilizator/parola gresite sau utilizatorul nu este asociat cu gestiunea.");
		
	}
	
	$user_profile = new UserProfile($user_data);
	$_SESSION['user'] = $user_profile;
	setcookie("test", "nicu");
	setcookie("uid", $user_profile -> user_id, time()+60*60*24*30);
	setcookie("gestiune_id", $user_profile -> gestiune_id, time()+60*60*24*30);
	
	$objResponse->redirect(DOC_ROOT."frame.php");
	return $objResponse;
}
?>