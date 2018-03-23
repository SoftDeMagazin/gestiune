<?php
require_once("common.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<title>Configurari - Gestiuni</title>
<script type="text/javascript" >
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=CONFIGURARI?>);
		$('#tabs').tabs();
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		//interface initialisation
		$('#punct_lucru_id').multiSelect(null, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
	}
);

function CheckAll(fmobj){
	for (var i = 0; i < fmobj.elements.length; i++) {
		var e = fmobj.elements[i];
		if ((e.name != 'chkbox_all') && (e.type == 'checkbox') && (!e.disabled)) {
			e.checked = fmobj.chkbox_all.checked;
		}
	}
}
			
</script>
</head>
<body>
<div id="left" style="height:100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#lista">Lista </a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
		<li><a href="#serii">Serii Numerice</a></li>
    </ul>
<div id="lista" class="tab">
     <div id="filtre" class="filtre" style=" width:50%; float:left">
        <form id="frmFiltre" onSubmit="return false;">
        Filtre: <input name="denumire" type="text" size="25" onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}">     
        Pct:
<?php
$soc = new PuncteLucru("where 1 order by `denumire` asc");
echo $soc -> select_multiple("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?>        
        </form>
    </div>

    <div id="pager" class="pager" style="text-align:right; width:50%; float:left">
    <?php
	include_once(DOC_ROOT."app/templates/pager.php");
	?>
    </div>
<?php
	//session_start();
	echo toolbar('gestiune_id',$_SESSION['user']->permissions['19']);
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

<div id="serii" class="tab">
	<input type="button" value="Tiparire Decizie Facturi" onclick="xajax_xPrintDecizieS($('#gestiune_id').val());">
	<h3>Serii Facturi</h3>
	<div id="div_serii_facturi"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'facturi');">
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'facturi');">
	</div>
		<h3>Serii Facturi Proforme</h3>
	<div id="div_serii_facturi_proforme"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'facturi_proforme');">
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'facturi_proforme');">
	</div>
	<h3>Serii Niruri</h3>
	<div id="div_serii_niruri"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'niruri');">
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'niruri');">
	</div>
	<h3>Serii Transferuri</h3>
	<div id="div_serii_transferuri"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'transferuri');">
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'transferuri');">
	</div>
	<h3>Serii Avize</h3>
	<div id="div_serii_avize"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'avize');">	
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'avize');">
	</div>
	<h3>Serii Deprecieri</h3>
	<div id="div_serii_deprecieri"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'deprecieri');">	
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'deprecieri');">
	</div>
		<h3>Serii Bonuri Consum</h3>
	<div id="div_serii_bonuri_consum"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'bonuri_consum');">	
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'bonuri_consum');">
	</div>
			<h3>Serii Transformari</h3>
	<div id="div_serii_transformari"></div>
	<div style="margin-top:2px" align="right">
	<input type="button" value="Serie Noua" onclick="xajax_adaugSerie($('#gestiune_id').val() ,'transformari');">	
	<input type="button" value="Cautare" onclick="xajax_setSerie($('#gestiune_id').val() ,'transformari');">
	</div>
</div>

</div>    
</div>	
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