<?php
class Roluri extends Model
{
	var $tbl = "roluri";
	var $_relations = array(
	);
	var $_defaultForm = array(
		"rol_id" => array("type"=>"hidden"),		
		"rol" => array("type"=>"text", "label"=>"Rol")
	);
	
	var $_validator = array(
		"rol" => array(array("required", "Introduceti un rol"))	
	);	
	
function lista($click="", $dblClick="" , $selected=0)
{
	$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
	$dg -> addHeadColumn("Rol");
	$dg -> setHeadAttributes(array());
	$nr_r = count($this);
	for($i=0;$i<$nr_r;$i++)
	{
		$this -> fromDataSource($i);
		$dg -> addColumn($this -> rol);
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
	$out = '<input type="hidden" id="selected_rol_id" name="selected_rol_id" value="'. $selected .'">';
	$out .= $dg -> getDataGrid();
	return $out;
}


function select($onChange="") 
	{
		$nr_r = count($this);
		$out = '<select name="rol_id" id="rol_id" style="width:130px" onChange="'. $onChange .'">';
		$out .= '<option value="0">Selectare</option>';	
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> rol .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}

function amDrept($drept_id, $modul_id) {
	$rd = new RoluriDrepturi("where rol_id = ".$this -> id." and modul_id = '$modul_id' and drept_id = '$drept_id'");
	if(count($rd)) return true;
	else return false;
}
	
function afisareDrepturi($modul_id, $onClick="") {
	$drepturi = new Drepturi("where 1");
	$out = '';
	foreach($drepturi as $drept) {
		if($this -> amDrept($drept -> id, $modul_id)) {
			$chk = "checked";
		} else {
			$chk = "";
		}
		$onClick = "xajax_clickDrept('". $this -> id ."','$modul_id','". $drept -> id ."',$(this).attr('checked'));";
		$out .= '<label><input type="checkbox" value="1" name="drept_'. $drept -> id .'" '. $chk .' onClick="'. $onClick .'"/> '.$drept -> denumire.'</label><br />';
	}
	return $out;
} 	

}
?>