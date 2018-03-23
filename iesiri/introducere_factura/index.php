<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript" >

var is_saved = true;	

function closeIt()
{	
  if (!is_saved) {
  	return "Documentul nu a fost salvat! Parasiti aceasta pagina?\n" +
		   "Apasati Ok pentru a parasi aceasta pagina. \n" +
		   "Apasati Cancel pentru a ramane in aceasta pagina si a continua editarea documentului!"	;
  }	 
}
window.onbeforeunload = closeIt;

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


function calculator(valoare, mod) {
	var cota_tva = parseFloat($('#cota_tva_id :selected').text()).toFixed(2);

	if(cota_tva == 'NaN') cota_tva = 0;
	var curs_valutar = $('#curs_valutar').val();
	valoare = parseFloat(valoare);
	switch(mod) {
		case 'pret_vanzare_val': {
		
			var cantitate = parseFloat($('#cantitate').val()).toFixed(2);
			
			var pret_vanzare_val = parseFloat(valoare).toFixed(2);
			var pret_vanzare_ron = parseFloat(pret_vanzare_val * curs_valutar).toFixed(2);
			
			
			var val_vanzare_val = parseFloat(cantitate*pret_vanzare_val).toFixed(2);
			var val_vanzare_ron = parseFloat(cantitate*pret_vanzare_ron).toFixed(2);
			
			var val_tva_val = parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2);
			var val_tva_ron = parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2);
			
			var val_val_cu_tva = parseFloat(parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_val).toFixed(2)*1).toFixed(2);
			var val_ron_cu_tva = parseFloat(parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_ron).toFixed(2)*1).toFixed(2);
			
			var pret_val_cu_tva = parseFloat(parseFloat(pret_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_val).toFixed(2)*1).toFixed(2);
			var pret_ron_cu_tva = parseFloat(parseFloat(pret_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_ron).toFixed(2)*1).toFixed(2);
		}break;
		
		case 'cantitate': {
		
			var cantitate = valoare;
			
			var pret_vanzare_val = parseFloat($('#pret_vanzare_val').val()).toFixed(2);
			var pret_vanzare_ron = parseFloat($('#pret_vanzare_ron').val()).toFixed(2);
						
			var val_vanzare_val = parseFloat(cantitate*pret_vanzare_val).toFixed(2);
			var val_vanzare_ron = parseFloat(cantitate*pret_vanzare_ron).toFixed(2);
			
			var val_tva_val = parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2);
			var val_tva_ron = parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2);
			
			var val_val_cu_tva = parseFloat(parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_val).toFixed(2)*1).toFixed(2);
			var val_ron_cu_tva = parseFloat(parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_ron).toFixed(2)*1).toFixed(2);
			
			var pret_val_cu_tva = parseFloat(parseFloat(pret_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_val).toFixed(2)*1).toFixed(2);
			var pret_ron_cu_tva = parseFloat(parseFloat(pret_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_ron).toFixed(2)*1).toFixed(2);
		}break;
		
		case 'pret_vanzare_ron': {
		
			var cantitate = parseFloat($('#cantitate').val()).toFixed(2);
			
			
			var pret_vanzare_ron = parseFloat(valoare).toFixed(2);
			var pret_vanzare_val = parseFloat(pret_vanzare_ron / curs_valutar).toFixed(2);
						
			var val_vanzare_val = parseFloat(cantitate*pret_vanzare_val).toFixed(2);
			var val_vanzare_ron = parseFloat(cantitate*pret_vanzare_ron).toFixed(2);
			
			var val_tva_val = parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2);
			var val_tva_ron = parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2);
			
			var val_val_cu_tva = parseFloat(parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_val).toFixed(2)*1).toFixed(2);
			var val_ron_cu_tva = parseFloat(parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_ron).toFixed(2)*1).toFixed(2);
			
			var pret_val_cu_tva = parseFloat(parseFloat(pret_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_val).toFixed(2)*1).toFixed(2);
			var pret_ron_cu_tva = parseFloat(parseFloat(pret_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_ron).toFixed(2)*1).toFixed(2);
		}break;
		
		case 'pret_ron_cu_tva': {
		
			var cantitate = parseFloat($('#cantitate').val()).toFixed(2);
			
			var pret_ron_cu_tva = parseFloat(valoare).toFixed(2); 
			
			var pret_vanzare_ron = parseFloat((parseFloat(pret_ron_cu_tva * 100).toFixed(2)*1) / (parseFloat(cota_tva*1 + 100*1).toFixed(2)*1)).toFixed(2);
			var pret_vanzare_val = parseFloat(pret_vanzare_ron / curs_valutar).toFixed(2);
			
			
			var val_vanzare_val = parseFloat(cantitate*pret_vanzare_val).toFixed(2);
			var val_vanzare_ron = parseFloat(cantitate*pret_vanzare_ron).toFixed(2);
			
			var val_tva_val = parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2);
			var val_tva_ron = parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2);
			
			var val_val_cu_tva = parseFloat(parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_val).toFixed(2)*1).toFixed(2);
			var val_ron_cu_tva = parseFloat(parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_ron).toFixed(2)*1).toFixed(2);
			
			var pret_val_cu_tva = parseFloat(parseFloat(pret_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_val).toFixed(2)*1).toFixed(2);
			
		}break;

		case 'pret_val_cu_tva': {
		
			var cantitate = parseFloat($('#cantitate').val()).toFixed(2);
			
			var pret_val_cu_tva = parseFloat(valoare).toFixed(2); 
			
			var pret_vanzare_val = parseFloat((parseFloat(pret_val_cu_tva * 100).toFixed(2)*1) / (parseFloat(cota_tva*1 + 100*1).toFixed(2)*1)).toFixed(2);
			var pret_vanzare_ron = parseFloat(pret_vanzare_val * curs_valutar).toFixed(2);
			
			
			var val_vanzare_val = parseFloat(cantitate*pret_vanzare_val).toFixed(2);
			var val_vanzare_ron = parseFloat(cantitate*pret_vanzare_ron).toFixed(2);
			
			var val_tva_val = parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2);
			var val_tva_ron = parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2);
			
			var val_val_cu_tva = parseFloat(parseFloat(val_vanzare_val * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_val).toFixed(2)*1).toFixed(2);
			var val_ron_cu_tva = parseFloat(parseFloat(val_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(val_vanzare_ron).toFixed(2)*1).toFixed(2);
			
			var pret_ron_cu_tva = parseFloat(parseFloat(pret_vanzare_ron * cota_tva / 100).toFixed(2)*1 + parseFloat(pret_vanzare_ron).toFixed(2)*1).toFixed(2);
		}break;
		
	}
	$('#pret_vanzare_val').val(pret_vanzare_val);
	$('#pret_vanzare_ron').val(pret_vanzare_ron);
	$('#pret_val_cu_tva').val(pret_val_cu_tva);
	$('#pret_ron_cu_tva').val(pret_ron_cu_tva);
	$('#val_vanzare_val').val(val_vanzare_val);
	$('#val_vanzare_ron').val(val_vanzare_ron);
	$('#val_tva_val').val(val_tva_val);
	$('#val_tva_ron').val(val_tva_ron);
	$('#val_val_cu_tva').val(val_val_cu_tva);
	$('#val_ron_cu_tva').val(val_ron_cu_tva);
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
		
	}
);

</script>

<title>Intocmire Factura</title></head>
<body>

<div id="left">
<div id="left-content">
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#antet">Antet factura</a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
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
		$produse = new ViewProduseGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
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
           	  <td width="50%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><input type="button" name="btnDiscount" id="btnDiscount" value="Discount" style="width:100%;" onClick="xajax_discount();"/></td>
                  <td><input type="button" name="btnStornare" id="btnStornare" value="Stornare" onClick="xajax_stornare(xajax.getFormValues('frm_facturi_continut'), $('#factura_id').val())" style="width:100%;"/></td>
                </tr>
              </table>
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
		 <input type="button" name="btnSalveazaFactura" id="btnSalveazaFactura" value="Inchide Factura" onClick="xajax_inchideFactura(xajax.getFormValues('frm_facturi'));">
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