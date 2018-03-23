<?php
class IncasariAsocieri extends Model
{
	var $tbl="incasari_asocieri";
	var $_relations = array(
			"incasare" => array("type" => "one", "model" => "Incasari", "key" => "incasare_id"),
		);
	var $_defaultForm = array(
		);
		
		
		
}
?>