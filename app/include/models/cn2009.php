<?php
class Cn2009 extends Model
{
	var $tbl="cn_2009";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
	public function __construct($id=NULL) {
		global $db;
		
		if(is_object($db)) {
			$this -> db = $db;	
		}
		
		$tblInfo = $this -> db -> tableInfo($this -> tbl);
		$this -> _tblColumns = $this -> db -> tableColumns($tblInfo);
		$this -> key = $this -> db -> tableKey($tblInfo);
		if($id) {
			$this -> getByCode($id);
		}
		else {
			$this -> setEmpty();
		}
	}	
	function getByCode($code) {
		$this -> fromString(" where Code = '$code' ");
	}
	
	function getDescription() {
		$parent = new Cn2009();
		$parent -> getByCode($this -> parentCode);
		$out = $this -> NameRo;
		$out = $parent -> NameRo.' ><br>'.$out;
		while($parent -> parentCode) {
			$parent -> getByCode($parent -> parentCode);
			$out = $parent -> NameRo." ><br>".$out;
		}
		
		return $out;
	}
		
}
?>