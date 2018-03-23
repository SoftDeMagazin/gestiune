<?php
require_once("common.php");
require_once("test_login.php");
require_once(DOC_ROOT."app/templates/meta-head-lite.php");
$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/'); 
?>
<script type="text/javascript">
	$(document).ready(
		function() {
			$('#gestiune_id').change(
				function() {
					xajax_loadGestiune(xajax.getFormValues('frmFiltre'));
				}
			);
			
			$('#societate_id').change(
				function() {
					xajax_loadSocietate(xajax.getFormValues('frmFiltre'));
				}
			);
		}
	)
</script>
</head>
<body>
<a href="index.php">Home</a>	
	 <form id="frmFiltre" action="<?php echo DOC_ROOT;?>print/raport.php" method="post" onSubmit="return popup('', this.target)" target="print">
    <input type="hidden" name="rpt_name" value="RptVanzari">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="middle">De la</td>
        <td valign="middle">Pana la</td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td valign="middle"><input type="text" name="from" id="from" class="calendar" value="<?=date("01.m.Y");?>"></td>
        <td valign="middle"><input type="text" name="end" id="end" class="calendar" value="<?=date("d.m.Y");?>"></td>
        <td valign="middle">&nbsp;</td>
      </tr>
      
      
      <tr>
        <td valign="middle">Societatea</td>
        <td valign="middle">
        <div id="div_societati">
		<?php
		$gestiune = new Gestiuni(1);
		$soc = new Societati("where 1 order by `denumire` asc");
		echo $soc -> select(array($gestiune -> punct_lucru -> societate_id));
		?>
        </div>        </td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td valign="middle">Gestiunea</td>
        <td valign="middle">
		<div id="div_gestiuni">
		<?php
        $gest = new Gestiuni("where 1");
		$selected = array(1);
		echo $gest -> selectMulti($selected);
		?>
        </div>        </td>
        <td valign="middle">&nbsp;</td>
      </tr>
	        <tr>
        <td valign="middle">Pos:</td>
        <td valign="middle">
		<div id="div_gestiuni">
		<?php
        $posuri = new Posuri("where 1");
		echo $posuri -> select_multiple();
		?>
        </div>        </td>
        <td valign="middle">&nbsp;</td>
      </tr>
	   
      <tr>
        <td valign="middle" align="center" colspan="3" ><input type="submit" name="btnCauta" id="btnCauta2" value="Afiseaza Raport" ></td>
      </tr>
    </table>
    </form>
<div id="windows"></div>
</body>