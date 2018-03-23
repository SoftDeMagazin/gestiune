<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript">
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=SECURITATE?>);
		$('#tabs').tabs();
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
	}
);
</script>
<title>Securitate - Utilizatori</title>

</head>
<body>
<div id="left" style="height: 100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
<ul id="tabs-meniu">
	<li><a href="#lista">Lista </a></li>
	<li><a href="#frm">Adaugare/Editare</a></li>
	<li><a href="#gestiuni">Asociere Gestiuni</a></li>
</ul>
<div id="lista" class="tab">
<div id="filtre" class="filtre" style="width: 50%; float: left">
<form id="frmFiltre" onSubmit="return false;">Nume acces: <input
	name="user_name" type="text" size="25"
	onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}">
Rol: <?php
$roluri = new Roluri("where 1 order by rol asc");
echo $roluri -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?></form>
</div>

<div id="pager" class="pager"
	style="text-align: right; width: 50%; float: left"><?php
	include_once(DOC_ROOT."app/templates/pager.php");
	?></div>

<?php
	session_start();
	echo toolbar('utilizator_id',$_SESSION['user']->permissions['21']);
	?>

<div id="grid"
	style="height: 500px; overflow: scroll; overflow-x: hidden; clear: both;">
&nbsp;</div>
</div>
<div id="frm" class="tab"></div>
<div id="gestiuni" class="tab"></div>
</div>
</div>

</div>
<div id="right" style="height: 100%"><?php
echo menu();
?></div>
<div id="windows"></div>
</body>
</html>
