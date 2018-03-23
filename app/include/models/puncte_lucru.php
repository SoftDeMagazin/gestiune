<?php 
class PuncteLucru extends Model {
    var $tbl = "puncte_lucru";
    var $_relations = array("societate"=>array("type"=>"one", "model"=>"Societati", "key"=>"societate_id", "value"=>"denumire"), );
    var $_defaultForm = array("punct_lucru_id"=>array("type"=>"hidden"), "denumire"=>array("type"=>"text", "label"=>"Denumire", "attributes"=>array("tabindex"=>1, "style"=>"width:400px;")), "societate"=>array("label"=>"Societate", "attributes"=>array("tabindex"=>2, "style"=>"width:300px;")), "adresa"=>array("type"=>"text", "label"=>"Adresa", "attributes"=>array("tabindex"=>1, "style"=>"width:400px;")), );
    
    function lista($click = "", $dblClick = "", $selected = 0) {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Denumire");
        $dg->addHeadColumn("Societate");
        $dg->addHeadColumn("Adresa");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $dg->addColumn($this->denumire);
            $dg->addColumn($this->societate->denumire);
            $dg->addColumn($this->adresa);
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
    
    function select_multiple($onChange = "") {
        $nr_r = count($this);
        $out = '<select multiple size="1" name="punct_lucru_id[]" id="punct_lucru_id" style="" onChange="'.$onChange.'">';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    function select_single($onChange = "") {
        $nr_r = count($this);
        $out = '<select name="punct_lucru_id" id="punct_lucru_id" style="width:130px" onChange="'.$onChange.'">';
     //   $out .= '<option value="0">Selectare</option>';
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
}
?>
