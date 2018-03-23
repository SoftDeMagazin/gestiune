<?php
class FacturiIntrari extends Model
{
	var $tbl="facturi_intrari";
	var $_relations = array(
		"tert" => array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value" => "denumire"),
		"tip" => array("type"=>"one", "model"=>"TipuriDocumente", "key"=>"tip_doc", "value" => "descriere", "conditions" => "where 1 order by tip_doc_id asc"),
		"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
		"continut" => array("type"=>"many", "model"=>"FacturiIntrariContinut", "key"=>"factura_intrare_id"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare", "conditions" => " where 1 order by cod_tva asc"),
		"plati" => array("type"=>"many", "model"=>"Plati", "key"=>"factura_intrare_id"),
		"user" => array("type"=>"one", "model"=>"Utilizatori", "key"=>"utilizator_id"),
		);
	var $_defaultForm = array(
		"factura_intrare_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;")),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;")),
		"gestiune" => array("label" => "Gestiune"),
		"cota_tva" => array("label" => "Cota Tva"),
		"curs_valutar" => array("type" => "text", "label" => "Curs Valutar", "attributes" => array("style" => "width:200px;")),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"i_total_fara_tva" => array("type" => "text", "label" => "Total Fara Tva", "attributes" => array("style" => "width:400px;")),
		"i_total_tva" => array("type" => "text", "label" => "Total Tva", "attributes" => array("style" => "width:400px;")),
		"masa_totala_bruta" => array("type" => "text", "label" => "Masa Totala Bruta (Kg)", "attributes" => array("style" => "width:400px;")),
		"masa_totala_neta" => array("type" => "text", "label" => "Masa Totala Neta (Kg)", "attributes" => array("style" => "width:400px;")),
		"info_doc_end" => array("type" => "fieldend"),
		);	
		
	var $_validator = array(
		"numar_doc" => array(array("required", "Introduceti numar factura")),
		"tert_id" => array(array("required", "Selectati un furnizor")),
		"data_factura" => array(array("required", "Introduceti data facturii")),
		"data_scadenta" => array(array("required", "Introduceti data scadenta")),	
	);	
	
	
	var $facturaExterna = array(
		"factura_intrare_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Document", "attributes" => array("style" => "width:400px;")),
		"tip" => array("label" => "Tip Document", "value" => "factura"),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva_id" => array("type" => "hidden"),
		"valuta" => array("type" => "select", "options" => "SELECT `descriere`, `descriere` FROM valute", "label" => "Valuta"),
		"curs_valutar" => array("type" => "text", "label" => "Curs Valutar", "attributes" => array("style" => "width:200px;")),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"scadenta" => array("type" => "text", "label" => "Scadenta Numar Zile"),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"masa_totala_bruta" => array("type" => "text", "label" => "Masa Totala Bruta (Kg)", "attributes" => array("style" => "width:400px;")),
		"masa_totala_neta" => array("type" => "text", "label" => "Masa Totala Neta (Kg)", "attributes" => array("style" => "width:400px;")),
		"info_doc_end" => array("type" => "fieldend"),
		"info_intrastat" => array("type" => "fieldstart", "label" => "Info INTRASTAT"),
		"natura_tranzactie_a" => array("type" => "select", "label" => "Natura Tranzactiei A", "options" => "SELECT `cod`, `descriere` FROM `natura_tranzactie` WHERE parent_code = 0"),
		"natura_tranzactie_b" => array("type" => "select", "label" => "Natura Tranzactiei B", "options" => "SELECT `cod`, `descriere` FROM `natura_tranzactie` WHERE parent_code = 1"),
		"incoterm" => array("type" => "select", "label" => "Termeni Livrare", "options" => "SELECT `cod`, `cod` FROM `incoterms`"),
		"transport" => array("type" => "select", "label" => "Mod Transport", "options" => "SELECT `cod`, `descriere` FROM `transport`"),
		"info_intrastat_end" => array("type" => "fieldend"),
		);	
		
	var $facturaInterna = array(
		"factura_intrare_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Document", "attributes" => array("style" => "width:400px;")),
		"tip" => array("label" => "Tip Document", "value" => "factura"),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva" => array("label" => "Cota Tva"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"scadenta" => array("type" => "text", "label" => "Scadenta Numar Zile"),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"info_disc" => array("type" => "fieldstart", "label" => "Reduceri"),
		"discount" => array("type" => "text", "label" => "Discount", "attributes" => array("style" => "width:200px;")),
		"tip_discount" => array("type" => "radiogroup", "label"=>"Tip discount", "options" => array("procentual" => "Procentual", "valoric" => "Valoric")) ,
		"info_disc_end" => array("type" => "fieldend"),
		"info_total" => array("type" => "fieldstart", "label" => "Total factura"),
		"i_total_fara_tva" => array("type" => "text", "label" => "Total Fara Tva", "attributes" => array("style" => "width:100px;")),
		"i_total_tva" => array("type" => "text", "label" => "Total Tva", "attributes" => array("style" => "width:100px;")),
		"info_total_end" => array("type" => "fieldend"),
		"info_doc_end" => array("type" => "fieldend"),
		);	
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Furnizor");
		$dg -> addHeadColumn("Numar");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Data scadenta");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> tert -> denumire);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn(c_data($this -> data_factura));
			$dg -> addColumn(c_data($this -> data_scadenta));
			
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
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function listaPlati($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Factura");
		$dg -> addHeadColumn("Total");
		$dg -> addHeadColumn("Platit");
		$dg -> addHeadColumn("Sold");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				switch($this -> tert -> tip) {
					case "intern": {
						$dg -> addColumn(money($this -> totalFactura(), $this -> tert -> valuta), array("style" => "color:blue;"));
						$dg -> addColumn(money($this -> totalPlati(), $this -> tert -> valuta), array("style" => "color:green"));
						$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));			

					}break;
					case "extern_ue": {
						$dg -> addColumn(money($this -> totalFacturaValuta(), $this -> tert -> valuta), array("style" => "color:blue;"));
						$dg -> addColumn(money($this -> totalPlati(), $this -> tert -> valuta), array("style" => "color:green"));
						$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));			
					}break;
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
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Sumar factura: '. $this -> numar_doc .'</legend>
		<strong>Furnizor:</strong> '. $this -> tert -> denumire .'<br />
		<strong>Gestiune:</strong> '. $this -> gestiune -> denumire .'<br />
		<strong>Data:</strong> '. c_data($this -> data_factura) .' <strong>Data scadenta:</strong> '. c_data($this -> data_scadenta) .'<br />		
		<fieldset><legend>LEI</legend>
		<strong>Total Fara TVA:</strong> '. $this -> total_fara_tva .' <strong>Total TVA:</strong> '. $this -> total_tva .'<br />
		<strong>Total Factura:</strong> '. $this -> total_factura .'<br />
		</fieldset>';
		
		if($this -> tert -> tip == "extern_ue") {
			$out .= '<fieldset><legend>CURS VALUTAR</legend>'. $this -> curs_valutar .'</fieldset>';
			$out .= '<fieldset><legend>VALUTA</legend>
			<strong>Total Fara TVA:</strong> '. $this -> totalFaraTvaValuta() .' <strong>Total TVA:</strong> '. $this -> totalTvaValuta() .'<br />
			<strong>Total Factura:</strong> '. $this -> totalFacturaValuta() .'<br />
			</fieldset>';
		}
				
		$out .= '
		</fieldset>
		';
		return $out;
	}
	
	function totalFaraTva() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ach_ron`) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function getMultiplicatorDiscount() {
		if($this -> tip_discount == "procentual") {
			$disc_factura = douazecimale((100 - $this -> discount)/100);
		} else {
			$procentual = ($this -> discount / $this -> totalCuDiscountComponente()) * 100;
			$disc_factura = (100 - $procentual)/100;
		}
		return $disc_factura;
	}
	
	function totalTva() {
		$sql = "
		SELECT sum(`val_tva_ron`) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."' and tip_discount = 'procentual';
		";
		$totalProcentual = $this -> db -> getRow($sql);
		return douazecimale($totalProcentual['total']);
	}
	
	function totalFactura() {
		$totalFactura = $this -> totalFaraTva();
		$totalFactura = $totalFactura + $this -> totalTva();
		return douazecimale($totalFactura);
	}
	
	function totalDiscountComponente() {
		$sql = "
		select sum(cantitate*( (pret_ach_ron * discount_continut) / 100)) as total
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."' and tip_discount = 'procentual';
		";
		$totalProcentual = $this -> db -> getRow($sql);
		
		if($this -> tip_doc != "bon_fiscal") {
			$sql = "
			select sum(discount_continut) as total
			FROM `facturi_intrari_continut` 
			WHERE `factura_intrare_id` = '". $this -> id ."' and tip_discount = 'valoric';
			";
		} else {
			$cota_tva = $this -> cota_tva -> valoare;
			$sql = "
			select sum(((discount_continut * 100) / (100 + get_valoare_tva(cota_tva_id)))) as total
			FROM `facturi_intrari_continut` 
			WHERE `factura_intrare_id` = '". $this -> id ."' and tip_discount = 'valoric';
			";
		}
		$totalValoric = $this -> db -> getRow($sql);
		return douazecimale($totalProcentual['total'] +  $totalValoric['total']);
	}
	
	function totalCuDiscountComponente() {
		return ($this -> totalFaraTva() - $this -> totalDiscountComponente());
	}
	
	function totalCuDiscount() {
		return ($this -> totalCuDiscountComponente() - $this -> totalDiscountFactura());
	}
	
	function totalTvaCuDiscount() {
		$sql = "
		SELECT sum(`val_tva_ron` - (`val_tva_ron` * `discount_continut`) / 100) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."' and tip_discount = 'procentual';
		";
		$totalProcentual = $this -> db -> getRow($sql);
		$sql = "
		SELECT sum(`val_tva_ron` - (`discount_continut` * get_valoare_tva(cota_tva_id)) / 100) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."' and tip_discount = 'valoric';
		";
		$totalValoric = $this -> db -> getRow($sql);
		$componente = douazecimale($totalProcentual['total'] + $totalValoric['total']);
		
		if($this -> tip_discount == "procentual") {
			$tva = $componente - $componente * $this -> discount / 100;
		} else {
			$tva = $componente - $this -> discount * $this -> cota_tva -> valoare / 100;
		}
		
		return douazecimale($tva);
	}
	
	function totalFacturaCuDiscount() {
		return $this -> totalCuDiscount() + $this -> totalTvaCuDiscount();
	}
	
	function totalDiscountFactura() {
		if($this -> tip_discount == "procentual") {
			return douazecimale($this -> totalCuDiscountComponente() * $this -> discount / 100);
		} else {
			if($this -> tip_doc != 'bon_fiscal') {
				return douazecimale($this -> discount);
			} else {
				$cota_tva = $this -> cota_tva -> valoare;
				return douazecimale(($this -> discount * 100 / $cota_tva + 100));
			}
		}
	}
	
	function totalTvaDiscount() {
		$totalFactura = $this -> totalCuDiscount();
		$totalTva = $this -> totalTva();
		return douazecimale($totalTva);
	}
	
	function totalFaraTvaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ach_val`) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	
	function totalTvaValuta() {
		$totalFactura = $this -> totalFaraTvaValuta();
		$totalTva = ($totalFactura * $this -> cota_tva -> valoare) / 100;
		return douazecimale($totalTva);
	}
	
	function totalFacturaValuta() {
		$totalFactura = $this -> totalFaraTvaValuta();
		$totalFactura = $totalFactura + (($totalFactura * $this -> cota_tva -> valoare) / 100);
		return douazecimale($totalFactura);
	}
	
	function totalPlati() {
		$sql = "
		SELECT sum(`suma`) as total_plati
		FROM plati_asocieri
		WHERE factura_intrare_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return douazecimale($row['total_plati']);
	}
		
	function sold() {
		if($this -> tert -> tip == "intern") {
			$totalFactura = $this -> totalFacturaCuDiscount();
		}
		else {
			$totalFactura = $this -> totalFacturaValuta();
		}
		$totalPlati = $this -> totalPlati();
		$sold = douazecimale($totalFactura - $totalPlati);
		return $sold;
	}
	
	function areSold() {
		if($this -> sold() != 0) return true;
		return false;
	}
	
	function sterge() {
		$this -> db -> query("delete from facturi_intrari_continut where factura_intrare_id = '". $this -> id ."'");
		$this -> delete();
	}
	
	function frmFactura($tert_id=0) {
		if(!$this -> id) {
			$tert = new Terti($tert_id);
		}
		else {
			$tert = $this -> tert;
		}

		switch($tert -> tip) {
			case "intern": {
				return $this -> frmInnerHtml($this -> frmContent($this -> facturaInterna));
			}break;
			case "extern_ue": {
				return $this -> frmInnerHtml($this -> frmContent($this -> facturaExterna));
			}break;		
				
		}
	}
	
	function frmContinutFactura($continut_id=0) {
		$continut = new FacturiIntrariContinut($continut_id);
		if(!$continut_id) $continut -> cota_tva_id = $this -> cota_tva_id;
		switch($this -> tert -> tip) {
			case "intern": {
				return $continut -> frmFacturaInterna();
			}break;
			case "extern_ue": {
				return $continut -> frmFacturaExterna();
			}break;
		}
	}
	
	function salveazaTotaluri() {
		$this -> total_fara_tva = $this -> totalCuDiscount();
		$this -> total_tva = $this -> totalTvaCuDiscount();
		$this -> total_factura = $this -> totalFacturaCuDiscount();
		
		
		$this -> total_fara_tva_val = $this -> totalFaraTvaValuta();
		$this -> total_tva_val = $this -> totalTvaValuta();
		$this -> total_factura_val = $this -> totalFacturaValuta();
		$this -> save();
	}
	
	function updatePreturiLei() {
		$sql = "
		update facturi_intrari_continut 
		set 
		pret_ach_ron = pret_ach_val * ". $this -> curs_valutar .",
		val_ach_ron = val_ach_val * ". $this -> curs_valutar .",
		val_tran_ron = val_tran_val * ". $this -> curs_valutar ."
		where factura_intrare_id = ". $this -> id ."
		";
		$this -> db -> query($sql);
	}
	
	
	
	function scriptFactura() {
		$continut = new FacturiIntrariContinut();
		return $continut -> scriptFactura();
	}
	
	function totaluriFacturaInterna() {
		$out = '
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <th scope="col">&nbsp;</th>
          <th scope="col">TOTAL</th>
          </tr>
        <tr>
          <td scope="col"><strong>Total Fara TVA</strong></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_val" id="txt_total_val" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          </tr>
        <tr>
          <td scope="col"><strong>Total TVA</strong></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_tva_val" id="txt_total_tva_val" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          </tr>
        <tr>
          <td scope="col"><strong>Total Factura</strong></td>
          <td scope="col"><div align="center">
            <input type="text" name="txt_total_factura_val" id="txt_total_factura_val" style="width:150px; height:20px; border:1px solid #000000; text-align:center; font-weight:bold; font-size:18px; line-height: 20px; color:#FF0000;" value="0.00">
          </div></td>
          </tr>
      </table>		';
	  return $out;
	}
	
	function totaluriFacturaExterna() {
		$out = '<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <th scope="col">&nbsp;</th>
          <th scope="col">LEI</th>
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
      </table>';
	  
	  return $out;
	}
	
	
	function totaluriFactura() {
		switch($this -> tert -> tip) {
			case "intern": {
				return $this -> totaluriFacturaInterna();
			}break;
			case "extern_ue": {
				return $this -> totaluriFacturaExterna();
			}break;
		}
	}
	
	function adaugaContinut($produs_id, $cantitate, $pret_ach) {
		$continut = new FacturiIntrariContinut();
		$continut -> factura_intrare_id = $this -> id;
		
		$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $this -> gestiune_id ."'");
		
		if(!count($produs)) {
			$produs = new Produse($produs_id);
			$produs -> asociazaCuGestiuni(array($this -> gestiune_id));
		}
		
		$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $this -> gestiune_id ."'");
		
		$continut -> produs_id = $produs_id;
		$continut -> unitate_masura_id = $produs -> unitate_masura_id;
		$continut -> pret_vanzare = $produs -> pret_ron;
		$continut -> tip_produs = $produs -> tip_produs;
		$continut -> cota_tva_id = $this -> cota_tva_id;
		
		
		$continut -> cantitate = $cantitate;
		$continut -> pret_ach_ron = $pret_ach;
		
		$continut -> calculeazaTotaluriRon();
		
		$continut -> save();
	}
	
	function calculeazaMasaAmbalaje() {
		$continut = new FacturiIntrariContinut("where factura_intrare_id = '". $this -> id ."' and tip_produs = 'ambalaj'");
		$total = 0;
		foreach($continut as $cnt) {
			$total += $cnt -> cantitate;
		}
		return $total;
	}
	
	function valideazaMasaAmbalaje() {
		$ambalaje = $this -> masa_totala_bruta - $this -> masa_totala_neta;
		$calculat = $this -> calculeazaMasaAmbalaje();
		return ($ambalaje == $calculat);
	}
}
?>