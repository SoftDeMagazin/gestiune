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



$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=INTRARI?>);
		$('#tabs').tabs();
		//$('#left').width("100%");
		//$('#right').hide();
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
						OnKeyRequestBuffer.modified('txtCautareFurnizor', 'xajax_filtruFurnizor', 100);
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

	function should_clear_discount(discount)
	{
		alert(discount.value);
		// dc a sters discountul debifam cele 2 tipuri de discount
		if(discount.value == "")
		{
			document.frm_facturi_intrari.discount_procentual.checked = false;
			document.frm_facturi_intrari.discount_valoric.checked = false;
		}	
	}
</script>

<title>Introducere factura furnizor</title>
</head>
<body >
<div id="left">
<div id="left-content">
<?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#antet">Antet NIR</a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
        <li><a href="#lista">Continut NIR</a></li>
        <li><a href="#intrastat">INTRASTAT</a></li>
    </ul>
<div id="antet" class="tab">
	<div id="div_cautare_furnizor">
    	<fieldset>
        <legend>Furnizor</legend>
        <form name="frmCautareFurnizor" method="get" action="" onSubmit="return false;">
        	<table width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%">
    <input name="txtCautareFurnizor" type="text" id="txtCautareFurnizor" size="45" value="">
    <span id="err_frm_furnizor_id" class="error">&nbsp;</span>
    <div id="div_filtru_furnizori" style="position:absolute;">
    &nbsp;
    </div>
    </td>
    <td width="30%"><div align="center">
            <input type="button" name="btnAddFurnizor" id="btnAddFurnizor" value="Adauga furnizor nou" onClick="xajax_frmFurnizor()">
          </div></td>
  </tr>
</table>
        </form>
        </fieldset>
    </div>
	<div id="div_frm_factura">
    	<div align="center"><input type="button" name="btnIesire" value="RENUNTA" onClick="xajax_location('/home/')" > </div>
    </div>
</div>    
<div id="lista" class="tab">       
    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    &nbsp;
    </div>
</div>
<div id="intrastat" class="tab">     
<form id="frm_mod_calcul">
  Mod calcul Valoare Statistica si Cota Transport:<br>

  <input type="radio" name="mod_calcul" id="mod_calcul" value="automat" checked />
    Introducere Cota transport pana la frontiera <input type="text" name="cota_transport_total" id="cota_transport_total" size="7" > LEI. Acest cost va fi impartit automat pe fiecare cod NC8 in functie de greutatea neta
  <br>
  <label>
  <input type="radio" name="mod_calcul" id="mod_calcul" value="manual" /> 
  Introducere Cota transport pana la frontiera pe fiecare pozitie in parte</label>
</form>
<form id="frm_continut_intrastat"> 
<strong>Cota transport pana la frontiera LEI:</strong> 

<label></label>
<div id="grid_intrastat" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    &nbsp;
    </div>
</form>  
  

<input type="button" name="btnSalveazaDateIntrastat" id="btnSalveazaDateIntrastat" value="Salveaza Date Intrastat" onClick="xajax_salveazaDateIntrastat($('#factura_intrare_id').val(), xajax.getFormValues('frm_continut_intrastat') , xajax.getFormValues('frm_mod_calcul'))">
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
		$produse = new ViewProduseGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'
		and tip_produs in ('mp', 'marfa', 'ambalaj')
		 order by denumire asc");
		echo $produse -> select(30);
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
				<legend>Adaugare Produs</legend>
              <input type="button" name="btnAddProdus" id="btnAddProdus" value="Adauga produs nou" onClick="xajax_frmProdus();">
             </fieldset> 
              	<fieldset>
			   	<legend>Info produs</legend>
            	<div id="div_detalii_produs" style="margin-top: 0px;">
                	&nbsp;
                </div>
               </fieldset>     
              </td>
            </tr>
        </table>
		<fieldset>
           	<legend>Continut NIR</legend>
               <div id="div_preview_factura" style="height:280px; overflow:scroll; overflow-x:hidden; clear:both;">          </div>
               <?php
			   echo iconEdit("xajax_frmComponenta($('#selected_continut_id').val(), $('#factura_intrare_id').val())");
			   echo iconRemove("xajax_stergeComponenta($('#selected_continut_id').val())");
			   ?>
  
           </fieldset>         
           
        <div id="div_totaluri">
        
        </div>
      </td>
    </tr>
  </table>
  	<div>
      
      <div align="center">
            <label></label>
            <input type="button" name="btnSalveazaFactura" id="btnSalveazaFactura" value="Inchide NIR" onClick="xajax_inchideFactura($('#factura_intrare_id').val());">
          </div>
    </div>
</div>
</div>
</div>
	
</div>    

<div id="right" >
<?php
	echo menu();
?>
</div> 
<div id="windows">
</div> 
</body>
</html>