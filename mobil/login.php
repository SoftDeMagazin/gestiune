<?php
require_once("common.php");
if($_SERVER['REQUEST_METHOD'] == "POST") {
	$login_frm = $_POST;
	$username = $login_frm['username'];
	$pass = $login_frm['pass'];
	$gest_id = $login_frm['gestiune_id'];

	$sql = "SELECT u.utilizator_id,u.user_name,u.nume,u.rol_id FROM ".
		"utilizatori u ".
		"WHERE u.user_name='".$username."' AND u.parola='".$pass."'";
		
	$user_data = $db->getRow($sql);
	$ok = TRUE;
	if(!$user_data)
	{
		$ok = false;
		header("Location: login.php");	
	}
	if($ok) {
		$user_profile = new UserProfile($user_data);
		$_SESSION['user'] = $user_profile;
		setcookie("test", "nicu");
		setcookie("uid", $user_profile -> user_id, time()+60*60*24*30);
		setcookie("gestiune_id", $user_profile -> gestiune_id, time()+60*60*24*30);
		header("Location: index.php ");
	}
}
require_once(DOC_ROOT."app/templates/meta-head-lite.php");
$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/'); 
?>
<script type="text/javascript">
	$(document).ready(
	)
</script>
</head>
<body>
	<FORM name="login_frm"  id="login_frm" METHOD="POST" ACTION="" onsubmit="">
<center>
<div class="login" >
	<div class="title">AUTENTIFICARE GESTIUNE</div>
	<br><br>
	<p>
		<label>Nume utilizator:</label><br>
		<input type="text" id="username" name="username" size="25" tabindex=1 style="width:200px" >

	</p>
	<p>

		<label>Parola: </label><br>
		<input  type="password" id="pass" name = "pass" size="25" tabindex=2 style="width:200px" >
	</p>
	<p>
	</p>
   <div style="float: right;padding-top:35px;padding-right:10px;">
		<input type="submit" value="Autentificare" />
	</div>	
</div>
</center>
</form>
<div id="windows"></div>
</body>