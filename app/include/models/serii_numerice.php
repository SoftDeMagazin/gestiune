<?php
class SeriiNumerice extends Model
{
	var $tbl="serii_numerice";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"serie_id" => array("type" => "hidden"),
		"serie" => array("type"=>"text", "label"=>"Serie optionala (contine litere)", "attributes" => array( "style" => "width:400px;")),
		"start" => array("type"=>"text", "label"=>"Primul Numar (orice numar diferit de 0)", "attributes" => array( "style" => "width:400px;")),
	//	"stop" => array("type"=>"text", "label"=>"Numar Sfarsit", "attributes" => array( "style" => "width:400px;")),
	//	"curent" => array("type"=>"text", "label"=>"Numar Curent (Primul Numar - 1)", "attributes" => array( "style" => "width:400px;")),
		"completare_stanga" => array("type"=>"text", "label"=>"Numar Total Caractere (Ex: 00001 - 5 caractere)", "attributes" => array( "style" => "width:400px;")),
		//"completez_cu" => array("type"=>"text", "label"=>"Completare Numar Caracter Cu", "attributes" => array( "style" => "width:400px;")),
		"descriere" => array("type"=>"text", "label"=>"Descriere", "attributes" => array( "style" => "width:400px;")),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Serie");
		$dg -> addHeadColumn("Start");
		$dg -> addHeadColumn("Curent");
		$dg -> addHeadColumn("Descriere");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> serie);
			$dg -> addColumn(str_pad($this -> start, $this -> completare_stanga, "0", STR_PAD_LEFT));
			$dg -> addColumn(str_pad($this -> curent, $this -> completare_stanga, "0", STR_PAD_LEFT));
			$dg -> addColumn($this -> descriere);
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

	function increment() {
		$this -> curent += 1;
		$this -> save();
	}	
	
	function decrement() {
		$this -> curent -= 1;
		$this -> save();
	}		
}
?>