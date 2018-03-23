<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>

<style type="text/css">
.ui-tabs-disabled { 
    display: none; /* disabled tabs don't show up */ 
} 
</style>
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
		$('#meniu').accordion('activate', <?=NOMENCLATOR?>);
		$('#tabs').tabs();
		$('#tabs').tabs('disable', 1);
		$('#tabs').tabs('disable', 2);
		$('#tabs').tabs('disable', 3);
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		$('#categorie_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
		$('#tip_produs').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
		
		$('#cautare_produs').keyup(
			function(event) {
				switch(event.keyCode) {
					case 40: {						
						$('#sel_produse').attr('selectedIndex', 0);
						$('#sel_produse').focus();						
					}break;
					default: {
						OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filtruProduse', 100);
					}break;
				}
			}
		);
		
		$('#cautare_produs').focus(
			function() {
				this.select();
			}
		);
		
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
	<li><a href="#lista" onClick="xajax_cancel()">Lista </a></li>
	<li><a href="#frm">Adaugare/Editare</a></li>
    <li><a href="#componente">Componente</a></li>
    <li><a href="#comisioane">Comisioane Agenti</a></li>
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
$tip = new TipuriProduse("where 1 order by descriere asc");
echo $tip -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?>    </td>
    <td valign="middle">&nbsp;</td>
    <td valign="middle">&nbsp;</td>
  </tr>
</table>
<br>
</form>
</div>

<div id="pager" class="pager" style="text-align: right; width: 40%; float: left"><?php
	include_once(DOC_ROOT."app/templates/pager.php");
	?></div>

	<?php
	echo toolbar('produs_id',$_SESSION['user']->permissions['1']);
	?>

<div id="grid" style="height: 500px; overflow: scroll; overflow-x: hidden; clear: both;">
&nbsp;</div>
</div>
<div id="frm" class="tab">

	<div><strong>Stoc curent:</strong> 0.00</div>
</div>

<div id="componente" class="tab">
<table width="100%" border="0" cellspacing="10" cellpadding="0">
    
    <tr>
      <td width="30%" valign="top">
      	<fieldset>
        <legend>Cautare</legend>	
      	<input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
     	<div id="div_select_produse">
        <?php
		$produse = new Produse(" inner join produse_gestiuni using(produs_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
		echo $produse -> select(22);
		?>
        </div>  
        </fieldset>
        </td>
      <td width="70%" valign="top">
        <fieldset>
          <legend>Produs</legend>
      	  <div id="div_info_produs" style="font-weight:bold; color:red">&nbsp;</div>
          UM: <span id="info_um"></span>
          <div id="div_componenta"></div>
          <div align="center"><input type="button" id="btnSalveazaComponenta" value="Salveaza Componenta" onClick="xajax_salveazaComponenta(xajax.getFormValues('frm_retetar'));"></div>
          </fieldset>
          <fieldset>  
            <legend>Componente</legend>	
            <div id="div_lista_componente" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">              </div>
           </fieldset>          </td>
    </tr>
  </table>
  <fieldset>
  	<legend>Pret achizitie</legend>
   <div id="pret_achizitie_reteta"></div>
  </fieldset>	
</div>
	<div id="comisioane" class="tab">

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
