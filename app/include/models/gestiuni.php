<?php 
class Gestiuni extends Model {
    var $tbl = "gestiuni";
    var $_relations = array(
	"punct_lucru"=>array("type"=>"one", "model"=>"PuncteLucru", "key"=>"punct_lucru_id", "value"=>"denumire"),
	"societate"=>array("type"=>"one", "model"=>"Societati", "key"=>"societate_id", "value"=>"denumire"),
	"tert"=>array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value"=>"denumire"),
	"tip" => array("type"=>"one", "model"=>"TipuriGestiuni", "key"=>"tip_gestiune", "model_key" => "tip", "value" => "descriere", "conditions" => "where 1")
	
	);
    var $_defaultForm = array("gestiune_id"=>array("type"=>"hidden"), "denumire"=>array("type"=>"text", "label"=>"Denumire"), "cod"=>array("type"=>"text", "label"=>"Cod"), "societate"=>array("label"=>"Societate"),
	"punct_lucru"=>array("label"=>"Punct lucru"),
	"tert"=>array("label"=>"Tert"),
	"tip"=>array("label"=>"Tip Gestiune"),
	"conditii_vanzare" => array("type"=>"textarea", "label"=>"Conditii Generale De Vanzare", "attributes" => array( "style" => "width:400px;")),
	 );
    
    function lista($click = "", $dblClick = "", $selected = 0) {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Cod");
        $dg->addHeadColumn("Punct lucru");
        $dg->addHeadColumn("Societate");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $dg->addColumn($this->denumire);
            $dg->addColumn($this->punct_lucru->denumire);
            $dg->addColumn($this->punct_lucru->societate->denumire);
            if ($this->id == $selected)
                $class = "rowclick";
            else
                $class = "";
            $ck = $this->stringReplace($click);
            $dck = $this->stringReplace($dblClick);
            $dg->setRowOptions(array("class"=>$class, "onMouseOver"=>"$(this).addClass('rowhover')", "onMouseOut"=>"$(this).removeClass('rowhover')", "onClick"=>"".$ck."$('#selected_".$this->key."').val('".$this->id."');$('#tbl_".$this->tbl." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');", "onDblClick"=>"$dck"));
            $dg->index();
        }
        $out = '<input type="hidden" id="selected_'.$this->key.'" name="selected_'.$this->key.'" value="'.$selected.'">';
        $out .= $dg->getDataGrid();
        return $out;
    }
    
    function select($onChange = "", $name = "") {
        if ($name == "") {
            $name = "gestiune_id";
        }
        
        $nr_r = count($this);
        $out = '<select name="'.$name.'" id="'.$name.'" style="width:130px" onChange="'.$onChange.'">';
        $out .= '<option value="0">Selectare</option>';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
	
	function selectOne($selected = 0) {
        if ($name == "") {
            $name = "gestiune_id";
        }
        
        $nr_r = count($this);
        $out = '<select name="gestiune_id" id="gestiune_id" style="" >';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
			if($selected == $this -> id) {
				$sel = "selected";
			} else {
				$sel = "";
			}
            $out .= '<option value="'.$this->id.'" '. $sel .'>'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    function select_multiple($onChange = "") {
        $nr_r = count($this);
        $out = '<select multiple name="gestiune_id[]" id="gestiune_id" style="width:100px" onChange="'.$onChange.'">';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    function select_multiple_with_name($onChange = "", $name = "") {
        $nr_r = count($this);
        $sel_name = ($name == "") ? 'gestiune_id' : $name;
        $out = '<select multiple name="'.$sel_name.'[]" id="'.$sel_name.'" style="width:100px" onChange="'.$onChange.'">';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    function selectMulti($selected = array(), $onChange = "") {
        $nr_r = count($this);
        $out = '<select size="5" multiple name="gestiune_id[]" id="gestiune_id" style="width:100px" onChange="'.$onChange.'">';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            if (in_array($this->id, $selected)) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $out .= '<option value="'.$this->id.'" '.$sel.'>'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    function select_multiple_with_validation() {
        $nr_r = count($this);
        $out = '<select multiple name="gestiune_id[]" id="gestiune_id" style="width:100px" onChange="" >';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            if (in_array($this->id, $selected)) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $out .= '<option validate="required:true, minlength:2" value="'.$this->id.'" '.$sel.'>'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
	
	function getGestiuneActiva() {
		$this -> fromId($_SESSION['user'] -> gestiune_id);
	}
	
	function getGestiuniCuDrepturi($utilizator_id="") {
		if(empty($utilizator_id)) $utilizator_id = $_SESSION['user'] -> user_id;
		$this -> fromString(" inner join gestiuni_utilizatori using(gestiune_id) where utilizator_id = '". $utilizator_id ."'");
	}
}
?>
