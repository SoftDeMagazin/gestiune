<?php
require_once("common.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<title>Configurari - Puncte de lucru</title>
<script type="text/javascript" >
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=CONFIGURARI?>);
		$('#tabs').tabs();
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		//interface initialisation
		$('#societate_id').multiSelect(null, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
	}
);
</script>
</head>
<body>
<div id="left" style="height:100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#lista">Lista </a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
    </ul>
<div id="lista" class="tab">
     <div id="filtre" class="filtre" style=" width:50%; float:left">
        <form id="frmFiltre" onSubmit="return false;">
        Filtre: <input name="denumire" type="text" size="25" onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}">     
        Soc:
<?php
$soc = new Societati("where 1 order by `denumire` asc");
echo $soc -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
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
	echo toolbar('punct_lucru_id',$_SESSION['user']->permissions['24']);
	?>
    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    &nbsp;
    </div>
    <div id="buttons" style="margin-top:5px;">
    	<input type="button" value="Adauga" id="btnAdd" onClick="xajax_frm(0);" >
        &nbsp;
        <input type="button" value="Edit" id="btnEdit" onClick="xajax_frm($('#selected_gestiune_id').val());" >
        &nbsp;
        <input type="button" value="Sterge" id="btnDel" onClick="xajax_confirm('Stergeti gestiune', 'xajax_sterge($(\'#selected_gestiune_id\').val(), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'));');" >
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