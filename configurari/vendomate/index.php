<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript" >
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=NOMENCLATOR?>);
		$('#tabs').tabs();
		$('#tabs').tabs('disable', 1);
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		
		$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function() {
			xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		});	
	}
);
</script>
<title>Configurari -  Vendomate</title>

</head>
<body>
<div id="left" style="height:100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#lista" onClick="xajax_cancel();">Lista </a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
    </ul>
<div id="lista" class="tab">
     <div id="filtre" class="filtre" style=" width:50%; float:left">
        <form id="frmFiltre" onSubmit="return false;">
        Gestiune: 
		<?php
		$gestiune = new Gestiuni("where 1");
		echo $gestiune -> selectMulti();	
		?>
        </form>
    </div>

    <div id="pager" class="pager" style="text-align:right; width:50%; float:left">
    <?php
	include_once(DOC_ROOT."app/templates/pager.php");
	?>
    </div>
	<?php
	session_start();
	echo toolbar('unitate_masura_id',$_SESSION['user']->permissions['3']);
	?>
    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    &nbsp;
    </div>
</div>
<div id="frm" class="tab">
</div>
</div>    </div>	
</div>    
<div id="right" style="height:100%">
<?php
	echo menu();
?>
</div> 
<div id="windows">
</div> 
</body>
</html>