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
		$('#meniu').accordion('activate', <?=IESIRI?>);
		$('#tabs').tabs();
		<?php
		$transformare_id = $_GET['transformare_id'];
		if($transformare_id) echo 'xajax_load('. $transformare_id .');';
		else echo 'xajax_load();';				
		?>		
		$('#cautare_produs').keyup(
			function(event) {
				switch(event.keyCode) {
					case 40: {						
						$('#sel_view_produse_gestiuni').attr('selectedIndex', 0);
						$('#sel_view_produse_gestiuni').focus();						
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

<title>Transformare</title></head>
<body>

<div id="left">
<div id="left-content">
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#antet">Antet</a></li>
        <li><a href="#frm">Continut</a></li>
    </ul>
<div id="antet" class="tab">
	<div id="div_frm_transformare">
    </div>
</div>    
<div id="frm" class="tab">
  <table width="100%" border="0" cellspacing="10" cellpadding="0">
    <tr>
      <td width="30%" valign="top">
      	<fieldset> 
        <legend>Cautare</legend>
        <div id="div_filtru">
       <input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
        </div>
        <div id="div_select_produse">
  <?php
  		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."'
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		echo $produse -> selectTransformari();
		?>
</div>
</fieldset>
        </td>
      <td width="70%" valign="top">
      	<div id="produs_finit" style="visibility:hidden; height:0px">
      	<fieldset><legend>Produs Finit</legend>
		<form id="frmProdusFinit" name="frmProdusFinit">
			<input type="hidden" id="trans_pf_id" name="trans_pf_id" >
			<input type="hidden" id="pf_produs_id" name="pf_produs_id" >
			<input type="text" id="produs_finit" readonly name="produs_finit" style="width:400px;" />
			<br />Cantitate <br/>
			<input type="text" id="cantitate_pf" name="cantitate_pf" style="width:100px;" />
			<input type="button" value="Salveaza" id="btnSalveazaPf" onclick="xajax_salveazaProdusFinit($('#transformare_id').val(), xajax.getFormValues('frmProdusFinit'));">
		</form>
		</fieldset>
		</div>
		<div id="continut_materiale" style="display:block;">
        <table width="100%" cellpadding="0"  cellspacing="0">
        	<tr>
           	  <td width="50%" valign="top">
              <fieldset>
				<legend>Adaugare Material</legend>
             	 <div id="div_frm_continut" style="margin-top: 0px;"></div>
              </fieldset>   
              </td>
              <td width="50%" valign="top">
              	<fieldset>
			   <legend>Info produs</legend>
            	<div id="div_detalii_produs" style="margin-top: 0px;">
                	&nbsp;</div>
               </fieldset>     
              </td>
            </tr>
        </table>
      	
           <fieldset>
           	<legend>Continut Materiale</legend>
               <div id="div_preview_continut" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">          </div>
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td> <?php
			   echo iconEdit("xajax_frmMateriePrima($('#selected_trans_mp_id').val(), $('#trans_pf_id').val())");
			   echo iconRemove("xajax_stergeMateriePrima($('#selected_trans_mp_id').val())");
			   ?>
        </td>
        <td align="right">
        	 <div >Total: <input type="text" id="total_materiale"></div>
        </td>
    </tr>
</table>

			  
			  
           </fieldset>    
     </div>
	 </td>
    </tr>
  </table>
  
  	<div align="center">
		 <input type="button" name="btnSalveazaFactura" id="btnSalveazaFactura" value="Inchide" onClick="xajax_inchideDocument(xajax.getFormValues('frm_deprecieri'));">
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