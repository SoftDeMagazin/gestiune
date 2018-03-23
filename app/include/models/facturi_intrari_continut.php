<?php
class FacturiIntrariContinut extends Model
{
	var $tbl="facturi_intrari_continut";
	var $_relations = array(
		"unitate_masura" => array("type"=>"one", "model"=>"UnitatiMasura", "key"=>"unitate_masura_id", "value" => "denumire"),
		"produs" => array("type"=>"one", "model"=>"Produse", "key"=>"produs_id"),
		"factura" => array("type" => "one", "model" => "FacturiIntrari", "key" => "factura_intrare_id"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare", "conditions" => " where 1 order by cod_tva asc"),
		);
	var $_defaultForm = array(
		"continut_id" => array("type" => "hidden"),
		"produs_id" => array("type" => "hidden"),
		"info_furnizor" => array("type" => "fieldstart", "label" => "Info factura"),
		"pret_ach_ron" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"pret_ach_bax" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"pret_cu_tva_bax" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"pret_ron_cu_tva" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"pret_ach_val" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_ach_ron" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_ach_val" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_cu_tva_ron" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_cu_tva_val" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_tva_ron" => array("type" => "hidden", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_tva_val" => array("type" => "hidden", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_tran_ron" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"val_tran_val" => array("type" => "text", "label" => "Pret Achizitie (fara TVA)", "attributes" => array("style" => "width:100px;")),
		"cantitate" => array("type" => "text", "label" => "Cantitate", "attributes" => array("style" => "width:100px;")),
		"cantitate_bax" => array("type" => "text", "label" => "Cantitate", "attributes" => array("style" => "width:100px;")),
		"masa_neta" => array("type" => "text", "label" => "Masa Neta", "attributes" => array("style" => "width:100px;")),
		"unitate_masura" => array("label" => "Unitate masura (factura)"),
		"end_info_furnizor" => array("type" => "fieldend"),
		"info_vanzare" => array("type" => "fieldstart", "label" => "Info vanzare"),
		"pret_vanzare" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"discount_continut" => array("type" => "text", "label" => "Discount", "attributes" => array("style" => "width:100px;")),
		"tip_discount" => array("type" => "radiogroup", "options" => array("procentual" => "Procentual", "valoric" => "Valoric")),
		"end_info_vanzare" => array("type" => "fieldend"),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$tert = $this -> factura -> tert;
			$dg -> addHeadColumn("Denumire");
			if($tert -> tip == "extern_ue") $dg -> addHeadColumn("NC8");
			$dg -> addHeadColumn("UM");
			$dg -> addHeadColumn("Cant");
			
			$dg -> addHeadColumn("Pret ach LEI");
			$dg -> addHeadColumn("Val. LEI");
			$dg -> addHeadColumn("Pret Intrare");
			$dg -> addHeadColumn("Val. Intrare");
			if($tert -> tip == "extern_ue") {
				$dg -> addHeadColumn("Pret ach VALUTA");
				$dg -> addHeadColumn("Val. VALUTA");
			}
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
			{
				$this -> fromDataSource($i);
				$produs = $this -> produs;
				
				$dg -> addColumn($produs -> denumire);
				if($tert -> tip == "extern_ue") $dg -> addColumn($produs -> nc8);
				$dg -> addColumn($this -> unitate_masura -> denumire);
				$dg -> addColumn($this -> cantitate);
				$dg -> addColumn($this -> pret_ach_ron);
				$dg -> addColumn(douazecimale($this -> pret_ach_ron*$this -> cantitate));
				$dg -> addColumn(douazecimale($this -> getPretIntrareRon()));
				$dg -> addColumn(douazecimale($this -> getPretIntrareRon()*$this -> cantitate));
				if($tert -> tip == "extern_ue") {
					$dg -> addColumn($this -> pret_ach_val);
					$dg -> addColumn(douazecimale($this -> pret_ach_val*$this -> cantitate));
				}
				
				if($this -> id == $selected) $class="rowclick";
				else $class="";
				$ck = $this -> stringReplace($click);
				$dck = $this -> stringReplace($dblClick);
				$dg -> setRowOptions(array(
				"class" => $class,
				"onMouseOver"=>"$(this).addClass('rowhover')", 
				"onMouseOut"=>"$(this).removeClass('rowhover')",
				"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
				"onDblClick"=>"$dck"
				));
				$dg -> index();
			}
			$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function frmFacturaInterna() {
		$out = '';
$out .= '
		<div id="div_info_produs" style=" font-weight:bold;color:red;">&nbsp;</div>
		'. $this -> continut_id() .'
		'. $this -> produs_id() .'
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Cantitate</td>
			<td align="left" >'. $this -> cantitate() .' <input type="button" id="btnCalculeazaCantitate" value="..."></td>
		</tr>
		<tr>
			<td align="left" >TVA: '. $this -> cota_tva() .'</td>
			<td align="left" >
			 UM: <span id="div_frm_unitate_masura">'. $this -> unitate_masura() .'</span></td>
		  </tr>
		  <tr>
			<td><label id="lbl_pret_fara_tva">Pret Fara TVA</label></td>
			<td align="left" >'. $this -> pret_ach_ron() .'</td>
		  </tr>
		  <tr>
			<td><label id="lbl_val_fara_tva">Valoare Fara TVA</label></td>
			<td align="left" >'. $this -> val_ach_ron() .'</td>
		  </tr>
		  <tr>
			<td><label id="lbl_pret_cu_tva">Pret Cu TVA</label></td>
			<td align="left">'. $this -> pret_ron_cu_tva() .'</td>
		  </tr>
		  <tr>
			<td><label id="lbl_val_cu_tva">Valoare Cu TVA</label></td>
			<td align="left" >'. $this -> val_cu_tva_ron() .' '. $this -> val_tva_ron() .'</td>
		  </tr>
		  <tr>
		  	<td>Discount</td>
			<td align="left" >'. $this -> discount_continut() .'</td>
		  </tr>
		  <tr>
		  	<td>Tip Discount </td>
			<td>'. $this -> tip_discount() .'</td>
		  </tr>		  
		</table>
		<div align="right">
<input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaComponenta(xajax.getFormValues(\'frm_facturi_intrari_continut\'), $(\'#factura_intrare_id\').val())">
		<input type="button" name="btnAnuleaza" id="btnAnuleaza" value="Anuleaza" onClick="xajax_frmComponenta(0, $(\'#factura_intrare_id\').val())">		</div>
		';
		return $this -> frmInnerHtml($out);
	}
	
	/*
	 * 
	 * 
	 * $out .= '
		<div id="div_info_produs" style=" font-weight:bold;color:red;">&nbsp;</div>
		'. $this -> continut_id() .'
		'. $this -> produs_id() .'
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Cantitate</td>
			<td align="left" >'. $this -> cantitate() .' <input type="button" id="btnCalculeazaCantitate" value="..."></td>
		</tr>
		<tr>
			<td align="left" >TVA: '. $this -> cota_tva() .'</td>
			<td align="left" >
			 UM: <span id="div_frm_unitate_masura">'. $this -> unitate_masura() .'</span></td>
		  </tr>
		  <tr>
			<td><label id="lbl_pret_fara_tva">Pret Fara TVA</label></td>
			<td align="left" >'. $this -> pret_ach_ron() .'</td>
		  </tr>
		  <tr>
			<td><label id="lbl_val_fara_tva">Valoare Fara TVA</label></td>
			<td align="left" >'. $this -> val_ach_ron() .'</td>
		  </tr>
		  <tr>
			<td><label id="lbl_pret_cu_tva">Pret Cu TVA</label></td>
			<td align="left">'. $this -> pret_ron_cu_tva() .'</td>
		  </tr>
		  <tr>
			<td><label id="lbl_val_cu_tva">Valoare Cu TVA</label></td>
			<td align="left" >'. $this -> val_cu_tva_ron() .' '. $this -> val_tva_ron() .'</td>
		  </tr>
		  <tr>
		  	<td>Discount</td>
			<td align="left" >'. $this -> discount_continut() .'</td>
		  </tr>
		  <tr>
		  	<td>Tip Discount </td>
			<td>'. $this -> tip_discount() .'</td>
		  </tr>		  
		</table>
		<div align="right">
<input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaComponenta(xajax.getFormValues(\'frm_facturi_intrari_continut\'), $(\'#factura_intrare_id\').val())">
		<input type="button" name="btnAnuleaza" id="btnAnuleaza" value="Anuleaza" onClick="xajax_frmComponenta(0, $(\'#factura_intrare_id\').val())">		</div>
		';
	 */
	
	function frmFacturaExterna() {
		$out = '';
		$out .= '
		<div id="div_info_produs" style=" font-weight:bold; color:red;">&nbsp;</div>
		'. $this -> continut_id() .'
		'. $this -> produs_id() .'		  
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>Cantitate</td>
			<td align="center">'. $this -> cantitate() .'</td>
			<td align="center">UM: <span id="div_frm_unitate_masura">'. $this -> unitate_masura() .'</span></td>
		  </tr>
		  <tr>
			<th>TVA: '. $this -> cota_tva() .'</th>
			<th>Valuta</th>
			<th>RON</th>
		  </tr>
		  <tr>
			<td>Pret Unitar Fara TVA</td>
			<td align="center">'. $this -> pret_ach_val() .'</td>
			<td align="center">'. $this -> pret_ach_ron() .'</td>
		  </tr>
		  <tr>
			<td>Valoare</td>
			<td align="center">'. $this -> val_ach_val() .'</td>
			<td align="center">'. $this -> val_ach_ron() .'</td>
		  </tr>
		  
		  <tr>
			<td>Val transport</td>
			<td align="center">'. $this -> val_tran_val() .'</td>
			<td align="center">'. $this -> val_tran_ron() .'</td>
		  </tr>
		   <tr>
			<td>Masa neta</td>
			<td align="center" >'. $this -> masa_neta() .'</td>
			<td align="left" >&nbsp;</td>
		  </tr>
		</table>
		<div align="right">
<input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaComponenta(xajax.getFormValues(\'frm_facturi_intrari_continut\'), $(\'#factura_intrare_id\').val());xajax_frmComponenta(0, $(\'#factura_intrare_id\').val());">
		<input type="button" name="btnAnuleaza" id="btnAnuleaza" value="Anuleaza" onClick="xajax_frmComponenta(0, $(\'#factura_intrare_id\').val())">		</div>
		';
		return $this -> frmInnerHtml($out);
	}
	
	function scriptFactura() {
		$out = "
		$('#cantitate').change(
			function () {
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'cantitate');
				if($('#tip_doc').val() == 'bon_fiscal') $('#pret_ron_cu_tva').focus().select();
				else {
					if($('#pret_ach_val').length > 0) $('#pret_ach_val').focus();
					else $('#pret_ach_ron').focus();
				}
			}
		);
		
		$('#cantitate').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
				}
			}
		);		
		
		
$('#cantitate_bax').change(
			function () {
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'cantitate_bax');
				if($('#tip_doc').val() == 'bon_fiscal') $('#pret_ron_cu_tva').focus().select();
				else {
					if($('#pret_ach_val').length > 0) $('#pret_ach_val').focus();
					else $('#pret_ach_bax').focus();
				}
			}
		);
		
		$('#cantitate_bax').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
				}
			}
		);
		
		$('#pret_ach_ron').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#pret_ach_ron').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), $('#factura_intrare_id').val(), 'pret_ach_ron');
			}
		);
		
		$('#pret_ach_bax').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#pret_ach_bax').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), $('#factura_intrare_id').val(), 'pret_ach_bax');
			}
		);
		
		$('#frm_facturi_intrari_continut #cota_tva_id').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'pret_ach_ron');
			}
		);

		$('#pret_ron_cu_tva').focus(
			function() {
				$(this).select();
			}
		);
	
		$('#pret_ron_cu_tva').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#pret_ron_cu_tva').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'pret_ron_cu_tva');
			}
		);
		
		
		$('#val_cu_tva_ron').focus(
			function() {
				$(this).select();
			}
		);
	
		$('#val_cu_tva_ron').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#val_cu_tva_ron').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'val_cu_tva_ron');
			}
		);		
		
		$('#pret_ach_val').focus(
			function() {
				$(this).select();
			}
		);
		
		$('#pret_ach_val').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#pret_ach_val').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'pret_ach_val');
			}
		);
		
		$('#val_ach_ron').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'val_ach_ron');
			}
		);
		
		$('#val_ach_ron').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#val_ach_val').change(
			function () {
				$('#btnSalveaza').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'val_ach_val');
			}
		);
		
		$('#val_ach_val').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					event.preventDefault();
				}
			}
		);		
		
		$('#val_tran_ron').change(
			function () {
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'val_tran_ron');
			}
		);
		
		$('#val_tran_val').change(
			function () {
				xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
					$('#factura_intrare_id').val(), 
					'val_tran_val');
			}
		);
		
		$('#btnCalculeazaCantitate').click(function(){
				xajax_calculeaza_cantitate();
				}
			);		";
	
		return $out;
	}
	
	function getPretIntrareRon() {
		$pret_intrare = $this -> pret_ach_ron;
		if($this -> tip_discount == "procentual") {
			$pret_intrare = ($pret_intrare * (100 - $this -> discount_continut)) / 100;
			$pret_intrare = $pret_intrare * $this -> factura -> getMultiplicatorDiscount();
		} else {
			if($this -> factura -> tip_doc != "bon_fiscal") {
				$pret_intrare = ($pret_intrare - $this -> discount_continut / $this -> cantitate);
			} else {
				$cota_tva = $this -> factura -> cota_tva -> valoare;
				$discount = ($this -> discount_continut * 100) / (100 + $cota_tva);
				$pret_intrare = ($pret_intrare - $discount / $this -> cantitate);
			}
			$pret_intrare = $pret_intrare * $this -> factura -> getMultiplicatorDiscount();
		}
		return $pret_intrare;
	}
	
	function calculeazaTotaluriRon() {
		$cota_tva = $this -> factura -> cota_tva -> valoare;
		$pret = $this -> pret_ach_ron;
		$cantitate = $this -> cantitate;
		
		$this -> val_ach_ron = $pret*$cantitate;
		$this -> pret_ron_cu_tva = $pret*(100 + $cota_tva) / 100;
		$this -> val_cu_tva_ron = $this -> pret_ron_cu_tva * $cantitate;
		$this -> val_tva_ron = $this -> val_cu_tva_ron - $this -> val_ach_ron;
		
	}
	
	function calculeazaTotaluriVal() {
		$cota_tva = $this -> factura -> cota_tva -> valoare;
		$pret = $this -> pret_ach_val;
		$cantitate = $this -> cantitate;
		
		$this -> val_ach_val = $pret*$cantitate;
		$this -> pret_val_cu_tva = $pret*(100 + $cota_tva) / 100;
		$this -> val_cu_tva_val = $this -> pret_val_cu_tva * $cantitate;
		$this -> val_tva_val = $this -> val_cu_tva_val - $this -> val_ach_val;
		
	}
}
?>