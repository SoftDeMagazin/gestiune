<?php
require_once("common.php");
require_once(__DIR__."/../app/templates/meta-head.php");
$xajax->printJavascript('../app/thirdparty/xajax/');
?>

<style>

body{
	/*background:#E2E4E2;*/
	background:url(../app/js/jquery/css/ui-lightness/images/ui-bg_diagonals-thick_20_666666_40x40.png) repeat;
}

div.login {
	background: url(images/bg_frm.png) repeat;
	padding: 5px 5px 0px 20px;
	margin-top:10%;
	
	border:solid;
	border-width: 1px;
	border-color:white;
	
	width: 400px;
	height: 240px;	
	
	text-align: left;
	font-weight: bold;
	font-family: Verdana,Geneva,Kalimati,sans-serif;
	color:white;
}

input{
	border: solid;
	border-width: 1px;
	border-color:#ABB0B0;
	height:25px;
	line-height:25px;
	font-size:22px;
}

select{
	border: solid;
	border-width: 1px;
	border-color:#ABB0B0;
	height:25px;
}

div.title{
	padding-top:5px;
}


</style>

<script type="text/javascript">
function login()
{
	if(dataIsValid() == false)
		return;

	xajax_login(xajax.getFormValues('login_frm'));
}

function dataIsValid()
{
	valid = true;
	
	if(document.login_frm.username.value == "" || document.login_frm.pass.value == "" )
	{
		xajax_alert("Completati nume utilizator si parola.\n");
		valid = false;
	}

	return valid;
}

</script>

<head>
<title>Gestiune - Autentificare</title>
</head>
<html>
<body onkeydown="if(event.keyCode==13) {login();}">

<FORM name="login_frm"  id="login_frm" METHOD="POST" ACTION="login.php" onsubmit="return false;">
<center>
<div class="login" >
	<div class="title">AUTENTIFICARE GESTIUNE - test</div>
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
		<a href="../exit.php" id="btnIesire" class="ui-state-default ui-state-active" style="padding: .5em 1em; text-decoration: none;" >Iesire</a>
	</div>	
	<div style="float: right;padding-top:35px;padding-right:10px;">
		<a href="#" id="btnAccept" class="ui-state-default ui-state-active" style="padding: .5em 1em; text-decoration: none;" onClick="login();" >Accepta</a>
	</div>	
</div>
</center>
</form>
<div id="windows"></div>
</body>
</html>
