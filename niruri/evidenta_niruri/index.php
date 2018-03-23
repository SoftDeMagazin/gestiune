<?php
require_once("common.php");
require_once (DOC_ROOT."test_login.php");
require_once (DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript" >
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=INTRARI?>);
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('#tabs').tabs();
		xajax_load();
		xajax_cautareFurnizor(xajax.getFormValues('frmCautareFurnizor'));
		$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function() {
			xajax_cautareFurnizor(xajax.getFormValues('frmCautareFurnizor'));
		});			
		$('#txt_numar').keypress(
			function(event) {
				if(event.keyCode == 13) {
					xajax_cautare(xajax.getFormValues('frmCautareNr'));
				}
			}
		);
	}
);

</script>

<title>Evidenta NIR-uri</title></head>
<body>
<div id="left">
    <div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
        <div id="tabs">
            <ul id="tabs-meniu">
                <li><a href="#cautare">EVIDENTA NIR-uri</a></li>
            </ul>
            <div id="cautare" class="tab">
            <fieldset>
           	<legend>Cautare</legend>
                <form action="" method="post" name="frmCautareFurnizor" id="frmCautareFurnizor" onSubmit="return false;">
                  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
                    <tr>
                      <td scope="col"><strong>Numar Factura</strong></td>
                      <td width="87%" scope="col"><input type="text" name="txt_numar" id="txt_numar"></td>
                    </tr>
                    <tr>
                      <td scope="col"><strong>Gestiune</strong></td>
                      <td scope="col">		<?php
        $gest = new Gestiuni();
		$gest -> getGestiuniCuDrepturi();
		$selected = array($_SESSION['user'] -> gestiune_id);
		echo $gest -> selectMulti($selected);
		?>
</td>
                    </tr>
                    <tr>
                      <td width="13%" scope="col"><strong>Furnizor</strong></td>
                      <td scope="col"><span id="div_frm_furnizor"></span><input type="button" value="..." onClick="xajax_xWinCautareTert();"></td>
                    </tr>
                    <tr>
                      <td><strong>De la</strong></td>
                      <td><input type="text" name="from" id="from" class="calendar" value="<?php echo date('01.m.Y') ?>">
                        <strong>Pana la</strong>
                        <input type="text" name="end" id="end" class="calendar" value="<?php echo date('d.m.Y') ?>"></td>
                    </tr>
                    
                    <tr>
                      <td>&nbsp;</td>
                      <td>
                          <div align="right">
                            <input type="button" name="btnCauta2" id="btnCauta2" value="Cautare" onClick="xajax_cautareFurnizor(xajax.getFormValues('frmCautareFurnizor'));">
                          </div></td>
                    </tr>
                  </table>
            </form>
                </fieldset>
                <div id="grid" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both; margin-top:10px;">
                &nbsp;
                </div>

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