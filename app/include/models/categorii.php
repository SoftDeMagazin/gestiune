<?php
class Categorii extends Model
{
	var $tbl="categorii";
	var $_relations = array(
		);
	var $_defaultForm = array(
			"categorie_id" => array("type"=>"hidden"),
			"gest" => '<div id="div_frm_gest">Gestiune</div>',		
			"denumire" => array("type"=>"text", "label"=>"Denumire categorie"),	
			"cod" => array("type"=>"text", "label"=>"Cod"),
			"subcategorie_pentru" => array("type"=>"select", "label" => "Este subcategorie pentru", "options"=>"SELECT categorie_id, denumire FROM categorii", "default" => "Nu este subcategorie", "default_value" => 0),
			
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Cod");
		$dg -> addHeadColumn("Denumire");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> cod);
			$dg -> addColumn($this -> denumire);
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
	
	function select_single($selected="")
	{
		$nr_r = count($this);
		$out = '<select  name="categorie_id" id="categorie_id">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
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
	
	function select($onChange="", $selected="") 
	{
		$nr_r = count($this);
		$out = '<select multiple size="1" name="categorie_id[]" id="categorie_id" style="" onChange="'. $onChange .'">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
	
	/**
	 * disociaza gestiunile care nu sunt in array
	 * @param array $gestiuni vector id-uri gestiuni array($id1, $id2...)
	 */
	 
	 function disociazaGestiuni($gestiuni=array()) {
	 	$gestiuni_asociate = $this -> getGestiuniAsociate();
	 	foreach($gestiuni_asociate as $gest_id) {
			if(!in_array($gest_id, $gestiuni)) {
				$cg = new CategoriiGestiuni(" where `gestiune_id` = '$gest_id' and categorie_id = '". $this -> id ."'");
				$cg -> delete();
			}
		}
	 } 
	
	/**
	 *  asociaza categorie cu gestiunile din array ... 
	 *	@param array $gestiuni vector id-uri gestiuni($id1, $id2, ...)
	 */  
	function asociazaCuGestiuni($gestiuni=array()) {
		$gestiuni_asociate = $this -> getGestiuniAsociate();
		foreach($gestiuni as $gest_id) {
			if(!in_array($gest_id, $gestiuni_asociate)) {
				$cg = new CategoriiGestiuni();
				$cg -> gestiune_id = $gest_id;
				$cg -> categorie_id = $this -> id;
				$cg -> save();
			}
		}
	}
	
	/**
	 * returneaza gestiunile asociate
	 * @return array
	 */
	 	
	function getGestiuniAsociate() {
		$rows = $this -> db -> getRowsNum("select `gestiune_id` from `categorii_gestiuni` where `categorie_id` = '". $this -> id ."'");
		$out = array();
		foreach($rows as $row) {
			$out[] = $row[0];
		}
		return $out;
	}
	/**
	 * incarca categoriile asociate unei gesiuni
	 * @param int $gestiune_id id-ul gestiunii
	 * @param string $conditions conditii sql
	 */
	
	function getByGestiuneId($gestiune_id, $conditions="") {
		$sql = " inner join categorii_gestiuni using(categorie_id) where gestiune_id = '$gestiune_id' ".$conditions;
		$this -> fromString($sql);
	}
	
	/**
	 * copiaza categoriile primite ca parametru in gestiunea primita ca parametru
	 * 
	 * @param object $gestiune_id gestiunea in care sa se copieze categoriile
	 * @param object $categorii_ids id-urile categoriile ce se vor copia
	 * @return 
	 */
	function copiazaInGestiuneNoua($gestiune_id, $categorii_ids)
	{
		$sql = "INSERT INTO categorii_gestiuni (categorii_gestiuni.categorie_id,categorii_gestiuni.gestiune_id)
				SELECT categorii.categorie_id, $gestiune_id
				FROM categorii
				WHERE categorii.categorie_id in $categorii_ids";
		$this->db->query($sql);
	}
}
?>