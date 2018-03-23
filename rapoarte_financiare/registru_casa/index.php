<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript">
var OnKeyRequestBuffer = 
    {
        bufferText: false,
        bufferTime: 350,
        fnc: false,
        modified : function(strId, fun, time)
        {
				this.fnc = fun;
				this.bufferTime = time;
                setTimeout('OnKeyRequestBuffer.compareBuffer("'+strId+'","'+xajax.$(strId).value+'");', this.bufferTime);
				
        },
        
        compareBuffer : function(strId, strText)
        {
            if (strText == xajax.$(strId).value && strText != this.bufferText)
            {
                this.bufferText = strText;
                OnKeyRequestBuffer.makeRequest(xajax.$(strId).value);
            }
        },
        
        makeRequest : function(str, fnc)
        {
            setTimeout(''+this.fnc+'("'+str+'");', 1);
        }
    }

$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=RAPOARTE_FINANCIARE?>);
		$('#tabs').tabs();
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });					
	}
);
</script>
<title>Layout</title>

</head>
<body>
<div id="left" style="height: 100%;">
<div id="left-content">
<?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
<ul id="tabs-meniu">
	<li><a href="#vanzari" onClick="">Registru Casa</a></li>
	</ul>
<div id="vanzari" class="tab">
    <div id="filtre" class="filtre" style="width:100%; float: left">
    <form id="frmFiltre" action="<?php echo DOC_ROOT;?>print/raport_pdf.php" method="post" onSubmit="return popup('', this.target)" target="print">
    <input type="hidden" name="rpt_name" value="RptRegistruCasa">
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
      <tr>
        <td valign="middle"><b>De la</b></td>
        <td valign="middle"><b>Pana la</b></td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td valign="middle"><input type="text" name="from" id="from" class="calendar" value="<?=date("01.m.Y");?>"></td>
        <td valign="middle"><input type="text" name="end" id="end" class="calendar" value="<?=date("d.m.Y");?>"></td>
        <td valign="middle">&nbsp;</td>
      </tr>
 
      <tr>
        <td valign="middle"><b>Gestiunea</b></td>
        <td valign="middle">
		<div id="div_gestiuni">
		<?php
        $gest = new Gestiuni();
		$gest -> getGestiuniCuDrepturi();
		$selected = array($_SESSION['user'] -> gestiune_id);
		echo $gest -> selectOne($selected);
		?>
        </div>        
	 </td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" valign="middle"></td>
        <td valign="middle"><input type="submit" name="btnCauta" id="btnCauta2" value="Afiseaza Raport" ></td>
      </tr>
    </table>
    </form>
    </div>


</div>
</div>
</div>

</div>
<div id="right" style="height: 100%">
<?php
	echo menu();
?></div>
<div id="windows"></div>
</body>
</html>
