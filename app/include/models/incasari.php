<?php
class Incasari extends Model
{
	var $tbl="incasari";
	var $_relations = array(
			"mod_plata" => array("type" => "one", "model" => "ModalitatiPlata", "key" => "mod_plata_id", "value" => "descriere"),
			"tert" => array("type" => "one", "model" => "Terti", "key" => "tert_id")
		);
	var $_defaultForm = array(
		"incasare_id" => array("type" => "hidden"),
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"societate_id" => array("type" => "hidden"),
		"numar_doc" => array("type" => "text", "label" => "Numar Document"),
		"mod_plata" => array("label" => "Tip Document"),
		"data_doc" => array("type" => "text", "label" => "Data Document", "attributes" => array("class" => "calendar")),
		"suma" => array("type" => "text", "label" => "Suma"),
		"explicatie" => array("type" => "textarea", "label" => "Explicatie", "attributes" => array("rows" => "3")),			
		);
		
	var $_validator = array (
		"numar_doc" => array(array("required", "Introduceti numar document")),
		"data_doc" => array(array("required", "Data document")),
		"suma" => array(array("required", "Introduceti suma"), array("numeric", "Suma trebuie sa fie un numar")),
	);	
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Tip Doc");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Explicatie");
		$dg -> addHeadColumn("Suma");
		$dg -> addHeadColumn("Asociat");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn($this -> mod_plata -> descriere);	
			$dg -> addColumn(c_data($this -> data_doc));	
			$dg -> addColumn($this -> explicatie);
			$dg -> addColumn(money($this -> suma, $this -> tert -> valuta));
			$dg -> addColumn(money($this -> sumaAsociata(), $this -> tert -> valuta));
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
		
	function sumaAsociata() {
		$sql = "
		SELECT sum(suma) as total 
		FROM incasari_asocieri
		WHERE incasare_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return $row['total'];
	}
	
	function sumaNeasociata() {
		return $this -> suma - $this -> sumaAsociata();
	}
		
}
?>