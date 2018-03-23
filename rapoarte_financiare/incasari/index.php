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
//segventa urmatoare de cod se activeaza la incarcarea pagini si are rolul de a genera meniul
//a initializa arajarea in pagina 
//si initializarea componentelor (calendar, multiSelect, tab)
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
		$('#societate_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadSocietate(xajax.getFormValues('frmFiltre'));
				});				
		$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadGestiune(xajax.getFormValues('frmFiltre'));
				});			
		$('#tert_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					
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
	<li><a href="#incasari" onClick="">Raport Incasari</a></li>
	</ul>
<div id="incasari" class="tab">
    <div id="filtre" class="filtre" style="width:100%; float: left">
    <form id="frmFiltre" action="<?php echo DOC_ROOT;?>print/raport.php" method="post" onSubmit="return popup('', this.target)" target="print">
    <input type="hidden" name="rpt_name" value="RptIncasari">
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
		$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
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
        $gest = new Gestiuni();
		$gest -> getGestiuniCuDrepturi();
		$selected = array($_SESSION['user'] -> gestiune_id);
		echo $gest -> selectMulti($selected);
		?>
        </div>        </td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td valign="middle">Client</td>
        <td valign="middle">
        <?php
		$terti = new Terti();
		$terti -> getClienti();
		echo $terti -> selectMulti();
		?>        </td>
        <td valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" valign="middle">Grupare dupa: 
          <label>
          <input name="grupare_dupa" type="radio" id="grupare_dupa" value="client" checked>
          Client 
          </label>
          <label>
          <input type="radio" name="grupare_dupa" id="grupare_dupa" value="moduri_plata"> 
          Moduri plata
  		  </label>
          <label>
          <input type="radio" name="grupare_dupa" id="grupare_dupa" value="documente"> 
          Documente</label></td>
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
