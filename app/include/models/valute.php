<?php
class Valute extends Model
{
	var $tbl="valute";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"valuta_id" => array("type" => "hidden"),
		"descriere" => array("type"=>"text", "label"=>"Denumire", "attributes" => array( "style" => "width:400px;")),
		"default" => array("type"=>"text", "label"=>"Reg com", "attributes" => array( "style" => "width:400px;")),
		);
		
	 function select($onChange = "", $name = "", $selected = "") {
        if ($name == "") {
            $name = "valuta";
        }
        
        $nr_r = count($this);
        $out = '<select name="'.$name.'" id="'.$name.'" style="width:130px" onChange="'.$onChange.'">';
        //$out .= '<option value="0">Selectare</option>';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
			if($this -> descriere == $selected) {
				$sel = "selected";
			} else {
				$sel = "";
			}
            $out .= '<option value="'.$this->descriere.'" '. $sel .'>'.$this->descriere.'</option>';
        }
        $out .= '</select>';
        return $out;
    }	
		
}
?>