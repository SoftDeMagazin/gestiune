<?php
function xLogin() {
	$dialog = new Dialog(800, 600, "", "x_win_login");
	$dialog -> modal = "true";
	$dialog -> title = "Log In";
	$dialog -> close = false;
	$dialog -> append("Sesiunea a expirat. Reintroduceti parola!");
	
	$user = new Utilizatori($_COOKIE['uid']);
	
	$dialog -> append('
<div class="login" >
	<p>
		<label>Nume utilizator:</label><br>
		<input type="text" id="x_username" value="'. $user -> user_name .'" name="x_username" size="25" tabindex=1 style="width:200px" readonly>

	</p>
	<p>

		<label>Parola: </label><br>
		<input  type="password" id="x_parola" name="x_parola" size="25" tabindex=2 style="width:200px" >
	</p>
	<p>
	</p>
	<div style="float: right;padding-top:35px;padding-right:10px;">
		<a href="#" id="btnAccept" class="ui-state-default ui-state-active" style="padding: .5em 1em; text-decoration: none;" onClick="xajax_xDoLogin($(\'#x_username\').val(), $(\'#x_parola\').val());" >Accepta</a>
	</div>	
</div>	
	');
	return $dialog -> open();
}

function xDoLogin($username, $pass) {
	$objResponse = new xajaxResponse();
	
	global $db;
	$sql = "SELECT u.utilizator_id,u.user_name,u.nume,u.rol_id FROM ".
		"utilizatori u ".
		"WHERE u.user_name='".$username."' AND u.parola='".$pass."'";
		
	$user_data = $db->getRow($sql);
	if($user_data == NULL)
	{
		$dialog = new Dialog(300, 200, "", "x_win_err_login"); 
		$dialog -> append('<div>Utilizator/parola gresite.</div>');
		$dialog->title = "Autentificare esuata";
		$dialog -> addButton("Ok", "<%close%>");
		return openDialog($dialog);	
	}
	
	$user_profile = new UserProfile($user_data);
	$_SESSION['user'] = $user_profile;
	$_SESSION['user'] -> gestiune_id = $_COOKIE['gestiune_id'];
	$objResponse =  closeDialog("x_win_login");
	return $objResponse;
}

$xajax -> registerFunction("xLogin");
$xajax -> registerFunction("xDoLogin");
?>