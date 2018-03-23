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
		$aviz_id = $_GET['aviz_id'];
		if($aviz_id) echo 'xajax_load('. $aviz_id .');';
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

<title>Document Depreciere</title></head>
<body>

<div id="left">
<div id="left-content">
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#antet">Antet</a></li>
        <li><a href="#frm">Continut</a></li>
    </ul>
<div id="antet" class="tab">
	<div id="div_tip_document">
    </div>
	<div id="div_frm_antet">
    </div>
</div>    
<div id="frm" class="tab">	
<div id="div_adaugare_continut">
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
		$produse = new ViewProduseGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'
		 and tip_produs in ('mp', 'marfa') 	
		 order by denumire asc");
		echo $produse -> select();
		?>
</div>
</fieldset>
        </td>
      <td width="70%" valign="top">
        <table width="100%" cellpadding="0"  cellspacing="0">
        	<tr>
           	  <td width="50%" valign="top">
              <fieldset>
				<legend>Adaugare Componenta</legend>
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
           	<legend>Continut Document</legend>
               <div id="div_preview_continut" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">          </div>
               <?php
			   echo iconEdit("xajax_frmComponenta($('#selected_continut_id').val(), $('#depreciere_id').val())");
			   echo iconRemove("xajax_stergeComponenta($('#selected_continut_id').val())");
			   ?>
           </fieldset>    
     
	 </td>
    </tr>
  </table>
</div>
<div id="div_continut_factura"></div>  
  	<div align="center">
		 <input type="button" name="btnSalveazaFactura" id="btnSalveazaFactura" value="Inchide" onClick="xajax_inchideDocument(xajax.getFormValues('frm_avize'));">
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