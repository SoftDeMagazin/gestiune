<?php
class Cursuri extends Model
{
	var $tbl="cursuri";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
		
	function getLast($valuta="EUR") {
		$this -> fromString("where `gestiune_id` = '". $_SESSION['user'] -> gestiune_id ."'
		and valuta = '$valuta'
		order by `curs_valutar_id` desc limit 0,1");
	}	
		
}
?>