<?php
class Clienti extends Model
{
	var $tbl="clienti";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"client_id" => array("type" => "hidden"),
		"denumire" => array("type"=>"text", "label"=>"Denumire", "attributes" => array("tabindex" => 1, "style" => "width:400px;")),
		"reg_com" => array("type"=>"text", "label"=>"Reg com", "attributes" => array("tabindex" => 2, "style" => "width:400px;")),
		"cod_fiscal" => array("type"=>"text", "label"=>"Cod fiscal", "attributes" => array("tabindex" => 3, "style" => "width:400px;")),
		"sediul" => array("type"=>"textarea", "label"=>"Sediul", "attributes" => array("tabindex" => 4, "style" => "width:400px;")),
		"judet" => array("type"=>"text", "label"=>"Judet", "attributes" => array("tabindex" => 5, "style" => "width:400px;")),
		"iban" => array("type"=>"text", "label"=>"Iban", "attributes" => array("tabindex" => 6, "style" => "width:400px;", "class" => "iban")),
		"banca" => array("type"=>"text", "label"=>"Banca", "attributes" => array("tabindex" => 7, "style" => "width:400px;")),
		"telefon" => array("type"=>"text", "label"=>"Telefon", "attributes" => array("tabindex" => 8, "style" => "width:400px;")),
		"email" => array("type"=>"text", "label"=>"Email", "attributes" => array("tabindex" => 9, "style" => "width:400px;")),
		);
		
	function cautare($str) {
		$this -> fromString("WHERE `denumire` LIKE '$str%' or `cod_fiscal` LIKE '$str%'");
		return count($this);
	}
		
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

	function selectMultiple() {
		$nr_r = count($this);
		$out = '<select size="5" name="sel_furnizor" id="sel_furnizor" style="width:400px">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}	
	
	function select() 
	{
		$nr_r = count($this);
		$out = '<select name="client_id" id="client_id" style="width:400px">';
		$out .= '<option value="0">Selectare Client</option>';	
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}

}
?>