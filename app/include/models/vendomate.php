<?php
class Vendomate extends Model
{
	var $tbl="vendomate";
	var $_relations = array(
		"gestiune" => array("type" => "one", "model" => "Gestiuni", "key"	=>	"gestiune_id", "value" => "denumire")
		);
	var $_defaultForm = array(
		"vendomat_id" => array("type" => "hidden"),
		"gestiune" => array("label" => "Gestiune"),
		"locatie" => array("type"=>"text", "label"=>"Locatie", "attributes" => array( "style" => "width:400px;")),
		"ordine_ruta" => array("type"=>"text", "label"=>"Ordine Ruta", "attributes" => array( "style" => "width:400px;")),
		"contor_vkz" => array("type"=>"text", "label"=>"Contor VKZ", "attributes" => array( "style" => "width:400px;")),
		"contor_kiz" => array("type"=>"text", "label"=>"Contor KIZ", "attributes" => array( "style" => "width:400px;")),
		);
	
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Gestiune");
			$dg -> addHeadColumn("Locatie");
			$dg -> addHeadColumn("Ordine Ruta");
			$dg -> addHeadColumn("Contor VKZ");
			$dg -> addHeadColumn("Contor KIZ");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> gestiune -> denumire);
				$dg -> addColumn($this -> locatie);
				$dg -> addColumn($this -> ordine_ruta);
				$dg -> addColumn($this -> contor_vkz);
				$dg -> addColumn($this -> contor_kiz);
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
		
}
?>