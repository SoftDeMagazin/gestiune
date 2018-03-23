<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript" >
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
		
		$('#categorie_id').multiSelect({}, function(el) {
					xajax_lista_produse(xajax.getFormValues('frm_filtre'));
				});
	}
);
</script>
<title>Nomenclator - Asociere Produse</title>

</head>
<body>
<div id="left" style="height:100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#lista">Produse - Gestiuni</a></li>
		<li><a href="#import">Import Produse</a></li>
    </ul>
<div id="lista" class="tab">
<table width="100%" border="0" cellspacing="10" cellpadding="0">
    
    <tr>
      <td width="30%" valign="top">
      	<fieldset>
        <legend>Cautare</legend>	
      	<input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
     	<div id="div_select_produse">
        <?php
		$produse = new Produse("where 1 order by denumire asc");
		echo $produse -> select(35);
		?>
        </div>  
        </fieldset>
        </td>
      <td width="70%" valign="top">
        <fieldset>
          <legend>Produs</legend>
      	  <div id="div_info_produs" style="font-weight:bold; color:red">&nbsp;</div>
          UM: <span id="info_um"></span><br>
		  Tip: <span id="info_tip"></span>
          <div id="div_componenta"></div>
         </fieldset>
          <fieldset>  
            <legend>Asociat in gestiunile: </legend>	
            <div id="div_lista_asocieri" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">              </div>
			LEI: <input type="text" name="pret" id="pret" > EUR: <input type="text" name="pret_eur" id="pret_eur" ><input type="button" value="Modifica Pret" onclick="xajax_salveazaPretGestiuni($('#produs_id').val(), $('#pret').val(), $('#pret_eur').val());">
           </fieldset>
		
		    <fieldset>  
            <legend>Asociaza in: </legend>	
			<form name="frm_asociaza" id="frm_asociaza">
				
            <div id="div_asociaza"></div>
			
			</form>
			<div align="right">
			<input type="button" value="Asociaza Produs" onclick="xajax_asociazaProdus(xajax.getFormValues('frm_asociaza'));">
           	</div>
		   </fieldset>
		</td>	
    </tr>
  </table>
</div>
<div id="import" class="tab">
	<form id="frm_filtre">
	<?php
	$gest = new Gestiuni();
	$gest -> getGestiuniCuDrepturi();
	echo ' Din gestiunea: ';
	echo $gest -> select("", "gestiune_sursa_id");
	echo ' In gestiunea: ';
	echo $gest -> select("", "gestiune_destinatie_id");
	echo '<br>Categorii: <br>';
	$categorii = new Categorii("where 1");
	echo $categorii -> select();
	?>
	</form>
				<label><input type="checkbox" onClick="$('.chk_produs').attr('checked', $(this).attr('checked'))"> Selectare Tot</label>
				<form id="frm_import">
				<?php
				echo Html::overflowDiv('', '550px', '', array("id" => "lista_produse"));
				?>
				</form>
	<input type="button" value="Importa Produse" onclick="xajax_importa_produse(xajax.getFormValues('frm_filtre'), xajax.getFormValues('frm_import'));">
           
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