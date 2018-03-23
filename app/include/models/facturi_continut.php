<?php
class FacturiContinut extends Model
{
	var $tbl="facturi_continut";
	var $_relations = array(
		"unitate_masura" => array("type"=>"one", "model"=>"UnitatiMasura", "key"=>"unitate_masura_id", "value" => "denumire"),
		"produs" => array("type"=>"one", "model"=>"Produse", "key"=>"produs_id"),
		"factura" => array("type"=>"one", "model"=>"Facturi", "key"=>"factura_id"),
		);
	var $_defaultForm = array(
		"continut_id" => array("type" => "hidden"),
		"produs_id" => array("type" => "hidden"),
		"info_furnizor" => array("type" => "fieldstart", "label" => "Info factura"),
		"pret_vanzare_ron" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"pret_vanzare_val" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"pret_ron_cu_tva" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"pret_val_cu_tva" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"val_vanzare_ron" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"val_vanzare_val" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"val_ron_cu_tva" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"val_val_cu_tva" => array("type" => "text", "label" => "Pret Vanzare", "attributes" => array("style" => "width:100px;")),
		"val_tva_ron" => array("type" => "hidden"),
		"val_tva_val" => array("type" => "hidden"),
		"cantitate" => array("type" => "text", "label" => "Cantitate", "attributes" => array("style" => "width:100px;")),
		"unitate_masura" => array("label" => "Unitate masura (factura)"),
		"end_info_furnizor" => array("type" => "fieldend"),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$tert = $this -> factura -> tert;
			$dg -> addHeadColumn("Denumire");
			$dg -> addHeadColumn("UM");
			$dg -> addHeadColumn("Cant");
			$dg -> addHeadColumn("Pret LEI");
			$dg -> addHeadColumn("Valoare LEI");
			if($tert -> tip == "intern") $dg -> addHeadColumn("Val TVA LEI");
			if($tert -> tip == "extern_ue") {
				$dg -> addHeadColumn("Pret ".$tert -> valuta);
				$dg -> addHeadColumn("Valoare ".$tert -> valuta);
			}
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire);
				$dg -> addColumn($this -> unitate_masura -> denumire);
				$dg -> addColumn($this -> cantitate);
				$dg -> addColumn($this -> pret_vanzare_ron);
				$dg -> addColumn(douazecimale($this -> pret_vanzare_ron*$this -> cantitate));
				if($tert -> tip == "intern") $dg -> addColumn(douazecimale($this -> val_tva_ron));
				if($tert -> tip == "extern_ue") {
					$dg -> addColumn($this -> pret_vanzare_val);
					$dg -> addColumn(douazecimale($this -> pret_vanzare_val*$this -> cantitate));
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
	
	function listaStornare($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$tert = $this -> factura -> tert;
			$dg -> addHeadColumn("Numar Factura");
			$dg -> addHeadColumn("Data Factura");
			$dg -> addHeadColumn("Cant Facturata");
			$dg -> addHeadColumn("Cant Stornata");
			$dg -> addHeadColumn("Cant Ramasa");
			$dg -> addHeadColumn("Cant Stornare");
			$dg -> addHeadColumn("Valoare LEI");
			if($tert -> tip == "extern_ue") {
				$dg -> addHeadColumn("Valoare ".$tert -> valuta);
			}
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$factura = $this -> factura;
				$dg -> addColumn($factura -> numar_doc);
				$dg -> addColumn(c_data($factura -> data_factura));
				$dg -> addColumn($this -> cantitate);
				$dg -> addColumn($this -> cantitate_stornata);
				$dg -> addColumn($this -> cantitate - $this -> cantitate_stornata);
				$dg -> addColumn('<input type="hidden" id="continut_id_'. $this -> id .'" name="continut_id[]" value="'. $this -> id .'" /><input type="text" id="cantitate_storno" name="cantitate_storno[]" value="0.00" onFocus="this.select()" />');
				$dg -> addColumn(douazecimale($this -> pret_vanzare_ron*$this -> cantitate));
				if($tert -> tip == "extern_ue") {
					$dg -> addColumn(douazecimale($this -> pret_vanzare_val*$this -> cantitate));
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
	
	function frmFacturaContinut() {
		$out = '';
		if(FACTURA_ADAUGARE_VALUTA) {
		$out .= '
		<div id="div_info_produs" style="font-weight:bold; color:red">&nbsp;</div>
		'. $this -> continut_id() .'
				
		Cantitate: '. $this -> produs_id() .'
		'. $this -> cantitate() .' UM: <span id="div_frm_unitate_masura">'. $this -> unitate_masura() .'</span>
		<br />
		<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
		  <tr>
			<th>&nbsp;</th>
			<th>Valuta</th>
			<th>RON</th>
		  </tr>
		  <tr>
			<td><strong>PRET FARA TVA</strong></td>
			<td align="center">'. $this -> pret_vanzare_val() .'</td>
			<td align="center">'. $this -> pret_vanzare_ron() .'</td>
		  </tr>
		  		  <tr>
			<td><strong>PRET CU TVA</strong></td>
			<td align="center">'. $this -> pret_val_cu_tva() .'</td>
			<td align="center">'. $this -> pret_ron_cu_tva() .'</td>
		  </tr>

		  <tr>
		  <td colspan="3"><hr></td>
		  </tr>
		  <tr>
			<td><strong>VAL. FARA TVA</strong></td>
			<td align="center">'. $this -> val_vanzare_val() .'</td>
			<td align="center">'. $this -> val_vanzare_ron() .'</td>
		  </tr>
		  <tr>
			<td><strong>VAL. CU TVA</strong></td>
			<td align="center">'. $this -> val_val_cu_tva() .' '. $this -> val_tva_val() .'</td>
			<td align="center">'. $this -> val_ron_cu_tva() .' '. $this -> val_tva_ron() .'</td>
		  </tr>
		</table>
		<div align="right">
                            <input type="button" name="btnSalveazaComponenta" id="btnSalveazaComponenta" value="Salveaza" onClick="">
                            </div>
		';
		$out .= '';
		} else {
					$out .= '
		<div id="div_info_produs" style="font-weight:bold; color:red">&nbsp;</div>
		'. $this -> continut_id() .'
				
		Cantitate: '. $this -> produs_id() .'
		'. $this -> cantitate() .' UM: <span id="div_frm_unitate_masura">'. $this -> unitate_masura() .'</span>
		<br />
		<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
		  <tr>
			<th>&nbsp;</th>
			<th>RON</th>
		  </tr>
		  <tr>
			<td><strong>PRET FARA TVA</strong></td>
			<td align="center">'. $this -> pret_vanzare_ron() .' '. $this -> pret_vanzare_val(array("type" => "hidden")) .'</td>
		  </tr>
		  		  <tr>
			<td><strong>PRET CU TVA</strong></td>
			<td align="center">'. $this -> pret_ron_cu_tva() .' '. $this -> pret_val_cu_tva(array("type" => "hidden")) .'</td>
		  </tr>

		  <tr>
		  <td colspan="3"><hr></td>
		  </tr>
		  <tr>
			<td><strong>VAL. FARA TVA</strong></td>
			<td align="center">'. $this -> val_vanzare_ron() .' '. $this -> val_vanzare_val(array("type" => "hidden")) .'</td>
		  </tr>
		  <tr>
			<td><strong>VAL. CU TVA</strong></td>
			<td align="center">'. $this -> val_ron_cu_tva() .' '. $this -> val_tva_ron() .' '. $this -> val_val_cu_tva(array("type" => "hidden")) .' '. $this -> val_tva_val(array("type" => "hidden")) .'</td>
		  </tr>
		</table>
		<div align="right">
                            <input type="button" name="btnSalveazaComponenta" id="btnSalveazaComponenta" value="Salveaza" onClick="">
                            </div>
		';
		$out .= '';
		}
		return $this -> frmInnerHtml($out);
	}
	
	function recalculareTotaluriRon() {
		$cantitate = $this -> cantitate;
		$pret = $this -> pret_vanzare_ron;
		$cota_tva = $this -> factura -> cota_tva -> valoare;
		$this -> val_vanzare_ron = $cantitate*$pret;
		$this -> pret_ron_cu_tva = $pret * (100 + $cota_tva) / 100;
		$this -> val_ron_cu_tva = $this -> pret_ron_cu_tva * $cantitate;
		$this -> val_tva_ron = $this -> val_ron_cu_tva - $this -> val_vanzare_ron;
	}
	
	function scriptFactura() {
		$out = "
		$('#cantitate').change(
			function () {
				calculator($(this).val(), 'cantitate');
				$('#btnSalveazaComponenta').focus();
					
			}
		);
		
		$('#btnSalveazaComponenta').click(
			function () {
				
				xajax_salveazaComponenta(xajax.getFormValues('frm_facturi_continut'), $('#factura_id').val());		
			}
		);
		
		$('#pret_vanzare_ron').change(
			function () {
				$('#btnSalveazaComponenta').focus();
				calculator($(this).val(), 'pret_vanzare_ron')
			}
		);
		
		$('#pret_vanzare_val').change(
			function () {
				$('#btnSalveazaComponenta').focus();
				calculator($(this).val(), 'pret_vanzare_val');			
			}
		);
		
		$('#pret_vanzare_val').focus(
			function() {
				$(this).select();
			}
		);
		
		$('#pret_ron_cu_tva').change(
			function () {
				$('#btnSalveazaComponenta').focus();
				calculator($(this).val(), 'pret_ron_cu_tva')
			}
		);
		
		$('#pret_val_cu_tva').change(
			function () {
				$('#btnSalveazaComponenta').focus();
				calculator($(this).val(), 'pret_val_cu_tva')
			}
		);

		
		$('#val_vanzare_ron').change(
			function () {
				$('#btnSalveazaComponenta').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_continut'), 
					$('#factura_id').val(), 
					'val_vanzare_ron');
			}
		);
		
		$('#val_vanzare_val').change(
			function () {
				$('#btnSalveazaComponenta').focus();
				xajax_calculator(xajax.getFormValues('frm_facturi_continut'), 
					$('#factura_id').val(), 
					'val_vanzare_val');
			}
		);
		
		$('#cantitate').keypress(
			function(event) {
				if(event.keyCode == 13) {
					xajax_calculator(xajax.getFormValues('frm_facturi_continut'), 
						$('#factura_id').val(), 
						'cantitate');
						event.preventDefault();
						$('#btnSalveazaComponenta').focus();
				}
			}
		);
		
		$('#pret_vanzare_val').keypress(
			function(event) {
				if(event.keyCode == 13) {
						$(this).change();
						event.preventDefault();
				}
			}
		);
		
		$('#pret_vanzare_ron').keypress(
			function(event) {
				if(event.keyCode == 13) {
						$(this).change();
						event.preventDefault();
				}
			}
		);
		";
	
		return $out;
	}
	
}
?>