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
		$('#categorie_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					
				});
		$('#tip_produs').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					
				});							
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
	<li><a href="#vanzari" onClick="">Raport Adaos</a></li>
	</ul>
<div id="vanzari" class="tab">
    <div id="filtre" class="filtre" style="width:100%; float: left">
    <form id="frmFiltre" action="<?php echo DOC_ROOT;?>print/raport.php" method="post" onSubmit="return popup('', this.target)" target="print">
    <input type="hidden" name="rpt_name" value="RptAdaos">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="middle">Gestiunea</td>
        <td valign="middle">
		<div id="div_gestiuni">
		<?php
        $gest = new Gestiuni($_SESSION['user'] -> gestiune_id);
		echo $gest -> gestiune_id();
		echo $gest -> denumire;
		?>
        </div>        </td>
        <td valign="middle">&nbsp;</td>
      </tr>  
	  <tr>
        <td valign="middle">Categorii:</td>
        <td valign="middle"><?php
    	$categorii = new Categorii("inner join categorii_gestiuni using(categorie_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
   		echo $categorii -> select("xajax_load(xajax.getFormValues('frmFiltre'));");
    ?></td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td valign="middle">Tip Articol:</td>
        <td valign="middle"><?php
    	$tip = new TipuriProduse("where 1 order by descriere asc");
   		 echo $tip -> select("xajax_load(xajax.getFormValues('frmFiltre'));");
   		 ?>    
		 </td>
        <td valign="middle"></td>
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
