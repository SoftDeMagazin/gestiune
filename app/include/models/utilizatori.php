<?php
class Utilizatori extends Model
{
	var $tbl="utilizatori";
	var $_relations = array(
		"rol" => array("type"=>"one", "model"=>"Roluri", "key"=>"rol_id", "value" => "rol"),
	);
	var $_defaultForm = array(
		"utilizator_id" => array("type" => "hidden"),
		"user_name" => array("type"=>"text", "label"=>"User Name", "attributes" => array("tabindex" => 1, "style" => "width:400px;")),
		"parola" => array("type"=>"text", "label"=>"Parola", "attributes" => array("tabindex" => 2, "style" => "width:400px;")),
		"rol" => array("label"=>"Rol", "attributes" => array("tabindex" => 3, "style" => "width:300px;")),
		"nume" => array("type"=>"text", "label"=>"Nume utilizator", "attributes" => array("tabindex" => 4, "style" => "width:400px;")),
		"cnp" => array("type"=>"text", "label"=>"Cnp", "attributes" => array("tabindex" => 5, "style" => "width:400px;")),
	);

	var $_validator = array(
		"user_name" => array(array("required", "Introduceti nume acces"), array("unique", "Nume acces exista deja")),
		"parola" => array(array("required", "Introduceti o parola")),	
		//"rol" => array(array("required","Selectati un rol")),
	);

	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("User Name");
		$dg -> addHeadColumn("Nume utilizator");
		$dg -> addHeadColumn("Rol");
		$dg -> addHeadColumn("Cnp");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> user_name);
			$dg -> addColumn($this -> nume);
			$dg -> addColumn($this -> rol -> rol);
			$dg -> addColumn($this -> cnp);
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

function select($onChange="") 
	{
		$nr_r = count($this);
		$out = '<select name="utilizator_id" id="utilizator_id" style="width:150px" onChange="'. $onChange .'">';
		$out .= '<option value="0">Selectare</option>';	
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> nume .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}

function select_multiple($onChange="") 
	{
		$nr_r = count($this);
		$out = '<select multiple name="utilizator_id[]" id="utilizator_id" style="width:100px" onChange="'. $onChange .'">';	
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> nume .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}


function gestiuniCuDrept() {
		$gestiuniUtilizatori = new GestiuniUtilizatori(" where utilizator_id = '". $this -> id ."'");
		$out = array();
		foreach($gestiuniUtilizatori as $gest) {
			$out[] = $gest -> gestiune_id;
		}
		return $out;
	}

function afisareGestiuni() {
	$gestiuni = new Gestiuni("where 1");
	$out = '';
	$gestiuni_drepturi = $this -> gestiuniCuDrept(); 
	foreach($gestiuni as $gestiune) {
		if(in_array($gestiune -> id, $gestiuni_drepturi)) {
			$chk = "checked";
		} else {
			$chk = "";
		}
		$onClick = "xajax_clickGestiune('". $this -> id ."','". $gestiune -> id ."',$(this).attr('checked'));";
		$out .= '<label><input type="checkbox" value="1" name="gestiune_'. $gestiune -> id .'" '. $chk .' onClick="'. $onClick .'"/> '.$gestiune -> denumire.' - '. $gestiune -> punct_lucru -> societate -> denumire .'</label><br />';
	}
	return $out;
}

}
?>