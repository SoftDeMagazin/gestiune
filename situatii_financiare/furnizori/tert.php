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
		$('#meniu').accordion('activate', <?=SITUATII_FINANCIARE?>);
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('.calendar').mask('99.99.9999');
		$('#tabs').tabs();
		
		xajax_cautare(xajax.getFormValues('frmCautare'));	
		<?php
		$action = $_GET['action'];
		switch($action) {
			case "plata": {
				$factura_id = $_GET['factura_id'];
				if($factura_id) {
					echo "xajax_frmPlata(0, '$factura_id', $('#tert_id').val(), $('#societate_id').val());";
				}
			}break;
		}
		?>
	}
);

</script>

<title>Situatii Financiare &gt; Furnizori</title>
</head>

<body>
<?php
$tert_id = $_GET['tert_id'];
$tert = new Terti($tert_id);

$societate_id = $_GET['societate_id'];
$societate = new Societati($societate_id);

?>
<div id="left">
    <div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
        <div id="tabs">
            <ul id="tabs-meniu">
                <li><a href="#tert">FURNIZOR: <?php echo $tert -> denumire, " ", $tert -> cod_fiscal ?></a></li>
                <li><a href="#situatie_actuala" onClick="xajax_situatieActuala($('#tert_id').val())">SITUATIE ACTUALA</a></li>
                 <li><a href="#situatie_globala">SITUATIE GLOBALA</a></li>
            </ul>
            <div id="tert" class="tab">
                <form action="" method="post" name="frmCautare" id="frmCautare" onSubmit="return false;">
                  <input name="achitat" type="hidden" id="achitat" value="1">
                  <input name="operat" type="hidden" id="operat" value="1">
                  <input name="asociat" type="hidden" id="asociat" value="1">                      
                  <?php
					   echo $tert -> tert_id();
					   echo $societate -> societate_id();
					   ?>
                </form>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>
    			<td width="50%">
                <fieldset>
                	<legend>FACTURI
                	<input type="checkbox" onClick="if(this.checked) $('#achitat').val(1); else $('#achitat').val(0); xajax_cautare(xajax.getFormValues('frmCautare'));" value="1" checked>
(doar facturi neachitate)</legend>
              <form name="frmFacturi" id="frmFacturi">
                    <div id="grid_facturi" style="height:480px; overflow:scroll; overflow-x:hidden; clear:both;"></div>
                    </form>
                    <input type="button" id="btnAddPlata" value="Adauga Plata" onClick="if($('#selected_factura_intrare_id') && $('#selected_factura_intrare_id').val() != 0) xajax_frmPlata(0, $('#selected_factura_intrare_id').val(), $('#tert_id').val(), $('#societate_id').val()); else xajax_alert('Selectati o factura!');">
                </fieldset>
                </td>
   				<td width="50%">
                <fieldset>
                	<legend>PLATI
                	<input type="checkbox" onClick="if(this.checked) $('#asociat').val(1); else $('#asociat').val(0); xajax_cautare(xajax.getFormValues('frmCautare'));" checked>
(doar plati neasociate)</legend>
           	  <form name="frmIncasari" id="frmFacturi">
                	<div id="grid_incasari" style="height:200px; overflow:scroll; overflow-x:hidden; clear:both;"></div>
                	</form>
                    <input type="button" id="btnAddPlata" value="Adauga Plata" onClick="xajax_frmPlata(0, 0, $('#tert_id').val(), $('#societate_id').val())">
                    <input type="button" id="btnEditPlata" value="Edit Plata" onClick="if($('#selected_plata_id').val()) xajax_frmPlata($('#selected_plata_id').val(), 0, $('#tert_id').val(), $('#societate_id').val()); else xajax_alert('Selectati o plata');">
                    <input type="button" id="btnDelPlata" value="Sterge Plata" onClick="if($('#selected_plata_id').val()) xajax_stergePlata($('#selected_plata_id').val()); else xajax_alert('Selectati o incasare!');">
            <div align="left" style="margin-top:4px;">
            	<input type="button" name="btnAsociazaIncasare" value="Asociaza Plata" onClick="if($('#selected_factura_intrare_id').val() && $('#selected_plata_id').val()) xajax_asociazaPlata($('#selected_factura_intrare_id').val(), $('#selected_plata_id').val()); else xajax_alert('Selectati o factura si o plata');" />
                <input type="button" name="btnDisociazaIncasare" value="Disociaza Plata" onClick="if($('#selected_plata_id').val()) xajax_disociazaPlata($('#selected_plata_id').val()); else xajax_alert('Selectati o plata!');" />
            </div>
                </fieldset>
                <fieldset>
                	<legend>EFECTE DE COMERT
                    <input type="checkbox" onClick="if(this.checked) $('#operat').val(1); else $('#operat').val(0); xajax_cautare(xajax.getFormValues('frmCautare'));" checked>
(doar efecte neincasate)</legend>
                <div id="grid_efecte" style="height:200px; overflow:scroll; overflow-x:hidden; clear:both;"></div>
                    <input type="button" id="btnAddEfect" value="Adauga Efect" onClick="xajax_frmEfect(0, $('#tert_id').val(), $('#societate_id').val())">
                    <input type="button" id="btnEditEfect" value="Edit Efect" onClick="if($('#selected_plata_efect_id').val(), $('#societate_id').val()) xajax_frmEfect($('#selected_plata_efect_id').val(), $('#tert_id').val()); else xajax_alert('Selectati un efect comercial!');">
                    <input type="button" id="btnDelEfect" value="Sterge Efect" onClick="if($('#selected_plata_efect_id').val()) xajax_stergeEfect($('#selected_plata_efect_id').val()); else xajax_alert('Selectati o incasare!');">
                    <input type="button" id="btnOperareEfect" value="Operare Efect" onClick="if($('#selected_plata_efect_id').val()) xajax_operareEfect($('#selected_plata_efect_id').val()); else xajax_alert('Selectati un efect comercial!');">
                </fieldset>
                </td>
  			</tr>
			</table>
            <fieldset>
            <legend>SOLD</legend>
            	<div style="text-align:right; font-weight:bold">
                	<div id="div_sold">
         			<?php
						echo money($tert -> soldPlati(), $tert -> valuta);
					?>   
                    </div>
           		</div>
            </fieldset>
            </div> 
            <div id="situatie_actuala" class="tab">
            	<?php
				echo Html::overflowDiv("", "500px", "", array("id" => "grid_situatie_actuala"));
				?>
            </div>
            <div id="situatie_globala" class="tab">
            	<?php
				echo Html::overflowDiv("", "500px", "", array("id" => "grid_situatie_globala"));
				?>
            </div>
        </div> <!-- end tabs -->
    </div>	 <!-- end left content -->
</div>    <!-- end left -->

<div id="right" style="height:100%">
<?php
	echo menu();
?>
</div> 

<div id="windows">
</div> 
</body>
</html>