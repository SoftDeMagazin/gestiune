<?php
class Societati extends Model
{
	var $tbl="societati";
	var $_relations = array(
		"conturi" => array("type"=>"many", "model"=>"SocietatiConturi", "key"=>"societate_id", "model_key" => "societate_id"),
		);
	var $_defaultForm = array(
		"societate_id" => array("type" => "hidden"),
		"denumire" => array("type"=>"text", "label"=>"Denumire", "attributes" => array( "style" => "width:400px;")),
		"reg_com" => array("type"=>"text", "label"=>"Reg com", "attributes" => array( "style" => "width:400px;")),
		"cod_fiscal" => array("type"=>"text", "label"=>"Cod fiscal", "attributes" => array( "style" => "width:400px;")),
		"sediul" => array("type"=>"textarea", "label"=>"Sediul", "attributes" => array( "style" => "width:400px;")),
		"capital_social" => array("type"=>"text", "label"=>"Capital Social", "attributes" => array( "style" => "width:400px;")),
		"tara" => array("type"=>"text", "label"=>"Tara", "attributes" => array( "style" => "width:400px;")),
		"judet" => array("type"=>"text", "label"=>"Judet", "attributes" => array( "style" => "width:400px;")),
		"iban" => array("type"=>"text", "label"=>"Iban", "attributes" => array( "style" => "width:400px;", "class"=>"iban")),
		"banca" => array("type"=>"text", "label"=>"Banca", "attributes" => array( "style" => "width:400px;")),
		"iban_valuta" => array("type"=>"text", "label"=>"Iban Valuta", "attributes" => array( "style" => "width:400px;", "class"=>"iban")),
		"banca_valuta" => array("type"=>"text", "label"=>"Banca Valuta", "attributes" => array( "style" => "width:400px;")),
		"swift" => array("type"=>"text", "label"=>"Swift", "attributes" => array( "style" => "width:400px;")),
		"telefon" => array("type"=>"text", "label"=>"Telefon", "attributes" => array( "style" => "width:400px;")),
		"fax" => array("type"=>"text", "label"=>"Fax", "attributes" => array( "style" => "width:400px;")),
		"website" => array("type"=>"text", "label"=>"Website", "attributes" => array( "style" => "width:400px;")),
		"administrator" => array("type"=>"text", "label"=>"Administrator", "attributes" => array( "style" => "width:400px;")),
		"info_factura_externa" => array("type"=>"textarea", "label"=>"Info Factura Externa", "attributes" => array( "style" => "width:400px;")),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Reg Com");
		$dg -> addHeadColumn("Cod fiscal");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn($this -> reg_com);
			$dg -> addColumn($this -> cod_fiscal);
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
		
	function select_multiple($selected=array()) 
	{
		$nr_r = count($this);
		$out = '<select multiple size="3" name="societate_id[]" id="societate_id" style="" onChange="'. $onChange .'">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if(in_array($this -> id, $selected)) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> denumire .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
	
	function select($selected=array()) 
	{
		return $this -> select_multiple($selected);
	}
}
?>