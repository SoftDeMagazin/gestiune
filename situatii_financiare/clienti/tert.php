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
		$('#meniu').accordion('activate',  <?=SITUATII_FINANCIARE?>);
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('.calendar').mask('99.99.9999');
		$('#tabs').tabs();
		xajax_cautare(xajax.getFormValues('frmCautare'));		
	}
);
</script>

<title>Situatii Financiare &gt; Clienti</title>
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
                <li><a href="#tert">CLIENT: <?php echo $tert -> denumire, " ", $tert -> cod_fiscal, " - ", $societate -> denumire ?></a></li>
                 <li><a href="#situatie_actuala" onClick="xajax_situatieActuala($('#tert_id').val())">SITUATIE ACTUALA</a></li>
                  <li><a href="#situatie_globala" onClick="xajax_situatieGlobala($('#tert_id').val())">SITUATIE GLOBALA</a></li>
            </ul>
            <div id="tert" class="tab">
			<?php
			?>
           
                <form action="" method="post" name="frmCautare" id="frmCautare" onSubmit="return false;">
                  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <input name="achitat" type="hidden" id="achitat" value="1">
                        <input name="operat" type="hidden" id="operat" value="1">
                        <input name="asociat" type="hidden" id="asociat" value="1">
                        <?php
					    echo $tert -> tert_id();
					    echo $societate -> societate_id();
					    ?>
                       
                      </td>
                    </tr>
                  </table>
            </form>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>
    			<td width="50%" valign="top">
                <fieldset>
                	<legend>FACTURI <input type="checkbox" onClick="if(this.checked) $('#achitat').val(1); else $('#achitat').val(0); xajax_cautare(xajax.getFormValues('frmCautare'));" value="1" checked> 
                	(doar facturi neachitate)
                </legend>
                    <div id="grid_facturi" style="height:480px; overflow:scroll; overflow-x:hidden; clear:both;"></div>
                    <input type="button" id="btnAddPlata" value="Adauga Incasare" onClick="if($('#selected_factura_id').val()) xajax_frmIncasare(0, $('#selected_factura_id').val(), $('#tert_id').val(), $('#societate_id').val()); else xajax_alert('Selectati o factura!');">
                </fieldset>
                </td>
   				<td width="50%" valign="top">
                <fieldset>
                	<legend>INCASARI <input type="checkbox" onClick="if(this.checked) $('#asociat').val(1); else $('#asociat').val(0); xajax_cautare(xajax.getFormValues('frmCautare'));" checked> 
               	(doar incasari neasociate)</legend>
                    <div id="grid_incasari" style="height:200px; overflow:scroll; overflow-x:hidden; clear:both;"></div>
                    <input type="button" id="btnAddPlata" value="Adauga Incasare" onClick="xajax_frmIncasare(0, 0,$('#tert_id').val(), $('#societate_id').val())">
                    <input type="button" id="btnEditPlata" value="Edit Incasare" onClick="if($('#selected_incasare_id').val()) xajax_frmIncasare($('#selected_incasare_id').val(), 0, $('#tert_id').val(), $('#societate_id').val()); else xajax_alert('Selectati o incasare');">
                    <input type="button" id="btnDelPlata" value="Sterge Incasare" onClick="if($('#selected_incasare_id').val()) xajax_stergeIncasare($('#selected_incasare_id').val()); else xajax_alert('Selectati o incasare!');">
                    <div align="left" style="margin-top:4px;">
            	<input type="button" name="btnAsociazaIncasare" value="Asociaza Incasare" onClick="xajax_asociazaIncasare($('#selected_factura_id').val(), $('#selected_incasare_id').val());" />
                <input type="button" name="btnDisociazaIncasare" value="Disociaza Incasare" onClick="if($('#selected_incasare_id').val()) xajax_disociazaIncasare($('#selected_incasare_id').val()); else xajax_alert('Selectati o incasare!');" />
            </div>
                </fieldset>
                
                <fieldset>
                	<legend>EFECTE DE COMERT <input type="checkbox" onClick="if(this.checked) $('#operat').val(1); else $('#operat').val(0); xajax_cautare(xajax.getFormValues('frmCautare'));" checked>
                	(doar efecte neincasate)
               	</legend>
                    <div id="grid_efecte" style="height:200px; overflow:scroll; overflow-x:hidden; clear:both;"></div>
                    <input type="button" id="btnAddEfect" value="Adauga Efect" onClick="xajax_frmEfect(0, $('#tert_id').val(), $('#societate_id').val())">
                    <input type="button" id="btnEditEfect" value="Edit Efect" onClick="if($('#selected_incasare_efect_id').val()) xajax_frmEfect($('#selected_incasare_efect_id').val(), $('#tert_id').val(), $('#societate_id').val()); else xajax_alert('Selectati un efect comercial!');">
                    <input type="button" id="btnDelEfect" value="Sterge Efect" onClick="if($('#selected_incasare_efect_id').val()) xajax_stergeEfect($('#selected_incasare_efect_id').val()); else xajax_alert('Selectati o incasare!');">
                    <input type="button" id="btnOperareEfect" value="Operare Efect" onClick="if($('#selected_incasare_efect_id').val()) xajax_operareEfect($('#selected_incasare_efect_id').val()); else xajax_alert('Selectati un efect comercial!');">
                </fieldset>
                </td>
  			</tr>
			</table>
            
            <fieldset>
            <legend>SOLD</legend>
            	<div style="text-align:right; font-weight:bold">
                	<div id="div_sold">
         			<?php
						echo money($tert -> soldIncasari(), $tert -> valuta);
					?>   
                    </div>
           		</div>
            </fieldset>
            </div> 
            <div id="situatie_actuala" class="tab">
            	<?php
				echo Html::overflowDiv("", "500px", "", array("id" => "grid_situatie_actuala"));
				?>
                <div style="margin-top:3px;">
                <input type="button" value="Tiparire" onClick="CallPrintContent('grid_situatie_actuala');">
                </div>
            </div>
            <div id="situatie_globala" class="tab">
           	<fieldset>
           	<legend>Filtre</legend>
                <form action="" method="post" name="frmFiltreSG" id="frmFiltreSG" onSubmit="return false;">
                  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
                    <tr>
                      <td><strong>De la:</strong> 
                        <input type="text" name="from" id="from" class="calendar" value="<?=date("01.m.Y");?>">
                        <strong>Pana la:</strong>
                        <input type="text" name="end" id="end" class="calendar" value="<?=date("d.m.Y");?>"></td>
                      <td>
                          <div align="right">
                            <input type="button" name="btnCauta2" id="btnCauta2" value="Cautare" onClick="xajax_situatieGlobala($('#tert_id').val(), xajax.getFormValues('frmFiltreSG'));">
                          </div></td>
                    </tr>
                  </table>
            </form>
                </fieldset>
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