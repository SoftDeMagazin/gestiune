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
		$('#meniu').accordion('activate', <?=STOCURI?>);
		$('#tabs').tabs();
		xajax_cancel();
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('#categorie_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
		$('#tip_produs').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
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
	<li><a href="#lista" onClick="xajax_cancel();">Stocuri </a></li>
	<li><a href="#loturi">Evidenta loturi</a></li>
    <li><a href="#fisa_magazie">Fisa Magazie</a></li>
    </ul>
<div id="lista" class="tab">
	
	
<div id="filtre" class="filtre" style="width: 60%; float: left">
<form id="frmFiltre" onSubmit="return false;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="middle">Cautare:</td>
    <td valign="middle"><input
	name="denumire" type="text" size="25"
	onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}"></td>
    <td valign="middle">Cat:</td>
    <td valign="middle"><?php
$categorii = new Categorii("inner join categorii_gestiuni using(categorie_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
echo $categorii -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?></td>
  </tr>
  <tr>
    <td valign="middle">Tip:</td>
    <td valign="middle"><?php
$tip = new TipuriProduse("where cu_stoc = 1 order by descriere asc");
echo $tip -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?>
</td>
    <td valign="middle">Cu stoc: </td>
    <td valign="middle"><label>
      <input type="checkbox" name="cu_stoc" id="cu_stoc" value="DA" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));">
    </label>
     </td>
  </tr>
</table>
</form>
</div>

<div id="pager" class="pager" style="text-align: right; width: 40%; float: left">
	<?php
	include_once(DOC_ROOT."app/templates/pager.php");
	?>
</div>


<div id="grid" style="height: 500px; overflow: scroll; overflow-x: hidden; margin-top:10px; clear: both;">
&nbsp;
</div>

<div style="margin-top:10px">
<input type="button" value="Print" onclick="xajax_printDoc()">
</div>

</div>
<div id="loturi" class="tab">
	<div id="div_info_produs" style="font-weight:bold"></div>	
	<strong>Intrari</strong>
               <fieldset>
           	<legend>Filtre</legend>
                <form action="" method="post" name="frmFiltreIesiri" id="frmFiltreIesiri" onSubmit="return false;">
                  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
                    <tr>
                      <td><input type="hidden" name="produs_id" id="produs_id" class="">
                       <strong>De la</strong>
                      	<input type="text" name="from" id="from" class="calendar">
                        <strong>Pana la</strong>
                        <input type="text" name="end" id="end" class="calendar">
                        <input type="button" name="btnCauta" id="btnCauta2" value="Cautare" onClick="xajax_evidentaLoturi($('#produs_id').val(),xajax.getFormValues('frmFiltreIesiri'));">
                        </td> 
                    </tr>
                  </table>
            </form>
                </fieldset>
	<div id="grid_loturi" style="height: 300px; overflow: scroll; overflow-x: hidden; margin-top:10px; clear: both;">
&nbsp;</div>
	<strong>Iesiri</strong>
    <div id="grid_iesiri" style="height: 300px; overflow: scroll; overflow-x: hidden; margin-top:10px; clear: both;">
    &nbsp;</div>
</div>
<div id="fisa_magazie" class="tab">
               <fieldset>
           	<legend>Filtre</legend>
                <form action="" method="post" name="frmFiltreFisa" id="frmFiltreFisa" onSubmit="return false;">
                  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
                    <tr>
                      <td>
                       <strong>De la</strong>
                        <input type="text" name="from" id="from2" class="calendar" value="<?=date("01.m.Y");?>">
                        <strong>Pana la:</strong>
                        <input type="text" name="end" id="end2" class="calendar" value="<?=date("d.m.Y");?>">
                        <input type="button" name="btnCauta" id="btnCauta2" value="Afisare" onClick="xajax_fisaMagazie($('#produs_id').val(),xajax.getFormValues('frmFiltreFisa'));">
                        </td> 
                    </tr>
                  </table>
            </form>
                </fieldset>
	<div id="grid_fisa_magazie" style="height: 500px; overflow: scroll; overflow-x: hidden; margin-top:10px; clear: both;">
    
    </div>
	<div style="margin-top:10px">
	<input type="button" value="Print" onclick="CallPrintContent('grid_fisa_magazie');">
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
