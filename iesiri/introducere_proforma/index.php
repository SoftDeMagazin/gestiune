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
		$factura_id = $_GET['factura_id'];
		if($factura_id) echo 'xajax_load('. $factura_id .');';
		else echo 'xajax_load();';				
		?>		
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
		
		$('#txtCautareFurnizor').keyup(
			function(event) {
				switch(event.keyCode) {
					case 40: {
						$('#sel_furnizor').attr('selectedIndex', 0);					
						$('#sel_furnizor').focus()						
					}break;
					default: {
						OnKeyRequestBuffer.modified('txtCautareFurnizor', 'xajax_filtruClient', 100);
					}break;
				}
			}
		);
		
		var position = $('#txtCautareFurnizor').offset();	
		$('#div_filtru_furnizori').hide();	
		$('#div_filtru_furnizori').css("top", (position.top + 23)+"px");
		$('#div_filtru_furnizori').css("left", (position.left)+"px");
		$('#txtCautareFurnizor').focus(
			function() {
				$('#div_filtru_furnizori').show();
			}
		);
		
		$('#txtCautareFurnizor').focus();
		
	}
);

</script>

<title>Intocmire Factura</title></head>
<body>

<div id="left">
<div id="left-content">
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#antet">Antet factura proforma</a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
        <li><a href="#lista">Continut factura</a></li>
    </ul>
<div id="antet" class="tab">
	<div id="div_cautare_furnizor">
    	<fieldset>
        <legend>Client</legend>
        <form name="frmCautareFurnizor" method="get" action="" onSubmit="return false;">
        	<table width="95%" border="0" cellspacing="0" cellpadding="0">
  	<tr>
    <td width="50%">
    <input name="txtCautareFurnizor" type="text" id="txtCautareFurnizor" size="45">
    <span id="err_frm_tert_id" class="error">&nbsp;</span>
    <div id="div_filtru_furnizori" style="position:absolute;">
    &nbsp;
    </div>
    </td>
    <td width="30%"><div align="center">
            <input type="button" name="btnAddFurnizor" id="btnAddFurnizor" value="Adauga client nou" onClick="xajax_frmClient()">
          </div></td>
  </tr>
</table>
        </form>
        </fieldset>
    </div>
	<div id="div_frm_factura">
    </div>
</div>    
<div id="lista" class="tab">       
    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    &nbsp;
    </div>
    <input type="button" id="btnAddPlata" value="Adauga" onClick="xajax_frmComponenta(0);">
	<input type="button" id="btnDelPlata" value="Editeaza" onClick="xajax_frmComponenta($('#selected_continut_id').val());">	
    <input type="button" id="btnDelPlata" value="Sterge" onClick="xajax_stergeComponenta($('#selected_continut_id').val());">	
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
		$produse = new Produse("inner join produse_gestiuni using(produs_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
		echo $produse -> select();
		?>
</div>
</fieldset>
        </td>
      <td width="70%" valign="top">
      <!--
      	<fieldset>
        <legend>Produs</legend>
      	
        <div align="right" style="width:30%; float:left">
        	<label>
        	<input type="button" name="btnAddProdus" id="btnAddProdus" value="Adauga produs nou" onClick="xajax_frmProdus();">
        	</label>
        </div>
        </fieldset>
      -->  
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
           	<legend>Continut Factura</legend>
               <div id="div_preview_factura" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">          </div>
               <?php
			   echo iconEdit("xajax_frmComponenta($('#selected_continut_id').val(), $('#factura_id').val())");
			   echo iconRemove("xajax_stergeComponenta($('#selected_continut_id').val())");
			   ?>
           </fieldset>    
          <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <th scope="col">&nbsp;</th>
          <th scope="col">RON</th>
          <th scope="col">VALUTA</th>
          </tr>
        <tr>
          <td scope="col"><strong>Total Fara TVA</strong></td>
          <td scope="col"><div align="center">
            <label>
            <input type="text" name="txt_total" id="txt_total" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
            </label>
          </div></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_val" id="txt_total_val" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          </tr>
        <tr>
          <td scope="col"><strong>Total TVA</strong></td>
          <td scope="col"><div align="center">
            <label></label>
            <input type="text" name="txt_total_tva" id="txt_total_tva" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_tva_val" id="txt_total_tva_val" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          </tr>
        <tr>
          <td scope="col"><strong>Total Factura</strong></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_factura" id="txt_total_factura" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_factura_val" id="txt_total_factura_val" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          </tr>
      </table>       </td>
    </tr>
  </table>
  
  	<div align="center">
		 <input type="button" name="btnSalveazaFactura" id="btnSalveazaFactura" value="Inchide Factura" onClick="xajax_inchideFactura(xajax.getFormValues('frm_facturi_proforme'));">
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