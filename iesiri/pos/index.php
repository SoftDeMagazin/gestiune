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
		$('#meniu').accordion('activate', <?=IESIRI?>);
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('.calendar').mask('99.99.9999');
		$('#tabs').tabs();
		$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function() {
			xajax_cautare(xajax.getFormValues('frmCautare'));
		});			
	}
);

</script>

<title>Evidenta facturi</title>
</head>
<body>
<div id="left">
    <div id="left-content">
        <div id="tabs">
            <ul id="tabs-meniu">
                <li><a href="#cautare">EVIDENTA IESIRI POS</a></li>
                 <li><a href="#continut">CONTINUT IESIRE</a></li>
            </ul>
            <div id="cautare" class="tab">
            <fieldset>
           	<legend>CAUTARE</legend>
                <form action="" method="post" name="frmCautare" id="frmCautare" onSubmit="return false;">
                   <table width="95%" border="0" align="center" cellpadding="2" cellspacing="2">
                    <tr>
                      <td width="27%"><strong>De la:</strong>                      
                        <input type="text" name="from" id="from" class="calendar" value="<?php echo date('01.m.Y'); ?>"></td>
                      <td width="73%"><strong>Pana la:</strong>
                        <input type="text" name="end" id="end" class="calendar" value="<?php echo date('d.m.Y'); ?>"></td>
                    </tr>
            <tr>
                    <td  scope="col"><strong>Gestiune</strong></td>
                    <td scope="col">		<?php
        $gest = new Gestiuni();
		$gest -> getGestiuniCuDrepturi();
		$selected = array($_SESSION['user'] -> gestiune_id);
		echo $gest -> selectMulti($selected);
		?></td></tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td>
                          <div align="right">
                            <input type="button" name="btnCauta2" id="btnCauta2" value="Afisare" onClick="xajax_cautare(xajax.getFormValues('frmCautare'));">
                          </div></td>
                    </tr>
                  </table>
            </form>
                </fieldset>
                <div id="grid" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both; margin-top:10px;">
                &nbsp;
                </div>
			 	<div style="margin-top:10px">
				<input type="button" value="Validare" onclick="xajax_validare($('#selected_vp_id').val());">
				<input type="button" value="Anulare" onclick="xajax_anulare($('#selected_vp_id').val());">
				</div>
            </div> 
            
            
                    <div id="continut" class="tab">
            <div id="grid_continut" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both; margin-top:10px;">
                &nbsp;
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