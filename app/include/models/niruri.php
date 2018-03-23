<?php
class Niruri extends Model
{
	var $tbl="niruri";
	var $_relations = array(
		"tert" => array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value" => "denumire"),
		"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
		"continut" => array("type"=>"many", "model"=>"FacturiIntrariContinut", "key"=>"factura_intrare_id"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare"),
		"factura" => array("type"=>"one", "model"=>"FacturiIntrari", "key"=>"factura_intrare_id", "value" => "valoare"),
		);
	var $_defaultForm = array(
		);
		
	var $_validator = array(
	);	
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("User");
		$dg -> addHeadColumn("Numar NIR");
		$dg -> addHeadColumn("Document");
		$dg -> addHeadColumn("Numar Document");	
		$dg -> addHeadColumn("Data");	
		$dg -> addHeadColumn("Furnizor - Cod Fiscal");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			
			$factura = $this -> factura;
			$dg -> addColumn($this -> gestiune -> denumire);
			$dg -> addColumn($factura -> user -> nume);
			$dg -> addColumn($this -> numar_nir);
			$dg -> addColumn($factura -> tip -> descriere);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn(c_data($this -> factura -> data_factura));
			
			$dg -> addColumn($this -> tert -> denumire ." - ". $this -> tert -> cod_fiscal);
			
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
	
	function getNumarNir($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		return $serie -> curent+1;
	}
	
	function getSerie($gestiune_id) {
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($gestiune_id, 'niruri');
		return $s -> serie;
	}
	
	function incrementSerie($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$serie -> increment();
	}  
	
	function genereazaNir($factura_id) {
		$this -> factura_intrare_id = $factura_id;
		$factura = new FacturiIntrari($factura_id);
		
		$this -> gestiune_id = $factura -> gestiune_id;
		$this -> societate_id = $factura -> societate_id;
		$this -> data_factura = $factura -> data_factura;
		$this -> data_nir = $factura -> data_factura;
		
		$this -> numar_nir = $this -> getNumarNir($this -> gestiune_id);
		
		$serie = $this -> getSerie($this -> gestiune_id);
		$this -> serie_id = $serie -> id;
		
		$this -> incrementSerie($this -> gestiune_id);
		
		$this -> numar_doc = $factura -> numar_doc;
		
		$this -> tert_id = $factura -> tert_id;
		$this -> cota_tva_id = $factura -> cota_tva_id;
		
		$this -> save();
	}
	
	function genereazaLoturi() {
		$disc_factura = $this -> factura -> getMultiplicatorDiscount();
		
		$sql = "
			INSERT INTO `loturi` (
			 `gestiune_id`,
			 `societate_id`,
			 `doc_id`,
			 `doc_comp_id`,
			 `doc_tip`,
			 `produs_id`,
			 `cantitate_init`,
			 `cantitate_ramasa`,
			 `pret_intrare_ron`,
			 `pret_intrare_val`,
			 `pret_vanzare`,
			 `data_intrare`,
			 `tip_lot`
			) (
			SELECT
			 '". $this -> gestiune_id ."', 	
			 '". $this -> societate_id ."', 
			 '". $this -> id ."' AS nir_id,
			 `continut_id`,
			 'nir' AS doc_tip,
			 `produs_id`,
			 `cantitate`,
			 `cantitate`,
			 (((`pret_ach_ron`)*((100 - `discount_continut`)/(100)))*". $disc_factura ."),
			 `pret_ach_val`,
			 `pret_vanzare`,
			 '". $this -> data_nir ."' AS data_intrare,
			 'nir' AS tip_lot
			FROM
			 `facturi_intrari_continut`
			WHERE 
			  `factura_intrare_id` = '". $this -> factura_intrare_id ."'
			  and tip_discount = 'procentual'
			);		
		";
		$this -> db -> query($sql);
		
		if($this -> factura -> tip_doc != "bon_fiscal") {
			$sql = "
				INSERT INTO `loturi` (
				 `gestiune_id`,
				 `societate_id`,
				 `doc_id`,
				 `doc_comp_id`,
				 `doc_tip`,
				 `produs_id`,
				 `cantitate_init`,
				 `cantitate_ramasa`,
				 `pret_intrare_ron`,
				 `pret_intrare_val`,
				 `pret_vanzare`,
				 `data_intrare`,
				 `tip_lot`
				) (
				SELECT
				 '". $this -> gestiune_id ."', 	
				 '". $this -> societate_id ."', 
				 '". $this -> id ."' AS nir_id,
				 `continut_id`,
				 'nir' AS doc_tip,
				 `produs_id`,
				 `cantitate`,
				 `cantitate`,
				 ((`pret_ach_ron` - `discount_continut`/`cantitate`)*". $disc_factura ."),
				 `pret_ach_val`,
				 `pret_vanzare`,
				 '". $this -> data_nir ."' AS data_intrare,
				 'nir' AS tip_lot
				FROM
				 `facturi_intrari_continut`
				WHERE 
				  `factura_intrare_id` = '". $this -> factura_intrare_id ."'
				  and tip_discount = 'valoric'
				);		
			";
		} else {
			$cota_tva = $this -> factura -> cota_tva -> valoare;
			$sql = "
				INSERT INTO `loturi` (
				 `gestiune_id`,
				 `societate_id`,
				 `doc_id`,
				 `doc_comp_id`,
				 `doc_tip`,
				 `produs_id`,
				 `cantitate_init`,
				 `cantitate_ramasa`,
				 `pret_intrare_ron`,
				 `pret_intrare_val`,
				 `pret_vanzare`,
				 `data_intrare`,
				 `tip_lot`
				) (
				SELECT
				 '". $this -> gestiune_id ."', 	
				 '". $this -> societate_id ."', 
				 '". $this -> id ."' AS nir_id,
				 `continut_id`,
				 'nir' AS doc_tip,
				 `produs_id`,
				 `cantitate`,
				 `cantitate`,
				 ((`pret_ach_ron` - ((`discount_continut` * 100) / (". $cota_tva ." + 100)))*". $disc_factura ."),
				 `pret_ach_val`,
				 `pret_vanzare`,
				 '". $this -> data_nir ."' AS data_intrare,
				 'nir' AS tip_lot
				FROM
				 `facturi_intrari_continut`
				WHERE 
				  `factura_intrare_id` = '". $this -> factura_intrare_id ."'
				  and tip_discount = 'valoric'
				);		
			";
		}
		$this -> db -> query($sql);
	}
	
	function stergeLoturi()
	{
		// sterg loturile asociate cu acest nir, dc este o editare permisa		
		$sql_del = "delete from loturi where doc_id = ".$this->id." and doc_tip='nir'";
		$this -> db ->query($sql_del);
	}
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Sumar NIR: '. $this -> numar_nir .'</legend>
		<strong>Furnizor:</strong> '. $this -> tert -> denumire .'<br />
		<strong>Factura:</strong> '. $this -> numar_doc .'<br />
		<strong>Gestiune:</strong> '. $this -> gestiune -> denumire .'<br />
		<strong>Data:</strong> '. c_data($this -> data_nir) .' <br />
		<strong>Total Fara TVA:</strong> '. $this -> factura -> total_fara_tva .' <strong>Total TVA:</strong> '. $this -> factura -> total_tva .'<br />
		<strong>Total Factura:</strong> '. $this -> factura -> total_factura .'<br />
		</fieldset>
		';
		return $out;
	}
	
	function totalFaraTva() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ach_ron`) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> factura_intrare_id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalTva() {
		$totalFactura = $this -> totalFaraTva();
		$totalTva = ($totalFactura * $this -> cota_tva -> valoare) / 100;
		return douazecimale($totalTva);
	}
	
	function totalFactura() {
		$totalFactura = $this -> totalFaraTva();
		$totalFactura = $totalFactura + (($totalFactura * $this -> cota_tva -> valoare) / 100);
		return douazecimale($totalFactura);
	}
	
	function suntLoturiScazute() {
		 $loturi = new Loturi("where doc_id = '". $this -> id ."' and doc_tip = 'nir' and cantitate_init > cantitate_ramasa");
		 if(count($loturi)) {
		 	return TRUE;
		 } else {
		 	return FALSE;
		 }
	}
	
	function anulareLoturi() {
		$loturi = new Loturi("where doc_id = '". $this -> id ."' and doc_tip = 'nir'");
		foreach($loturi as $lot) {
			$lot -> delete();
		}
	}
	
	function sterge() {
		$this -> anulareLoturi();
		$factura = new FacturiIntrari($this -> factura_intrare_id);
		$factura -> sterge();
		$this -> delete();
	}
}
?>