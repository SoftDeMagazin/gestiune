<?php
class SocietatiConturi extends Model
{
	var $tbl="societati_conturi";
	var $_relations = array(
		"societate" => array("type"=>"one", "model"=>"Societati", "key"=>"societate_id", "model_key" => "societate_id", "value" => "denumire"),
		);
	var $_defaultForm = array(
		"cont_id" => array("type" => "hidden"),
		"societate_id" => array("type" => "hidden"),
		"banca" => array("type"=>"text", "label"=>"IBAN", "attributes" => array("style" => "width:400px;", "class" => "iban")),
		"iban" => array("type"=>"text", "label"=>"Banca", "attributes" => array("style" => "width:400px;")),
		"swift" => array("type"=>"text", "label"=>"Swift", "attributes" => array("style" => "width:400px;")),
		"valuta" => array("type" => "select", "options" => "SELECT `descriere`, `descriere` FROM valute", "label" => "Valuta"),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Iban");
		$dg -> addHeadColumn("Banca");
		$dg -> addHeadColumn("Valuta");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> iban);
			$dg -> addColumn($this -> banca);
			$dg -> addColumn($this -> valuta);
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
	
	function form() {
	}

		
}
?>