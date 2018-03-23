<?php 
class VanzariPosContinutIesiri extends Model {
    var $tbl = "vanzari_pos_continut_iesiri";
    var $_relations = array(
		"comp"=>array("type"=>"one", "model"=>"VanzariPosContinut", "key"=>"comp_id", "model_key"=>"continut_id"), 
		"produs"=>array("type"=>"one", "model"=>"Produse", "key"=>"produs_id"),
		"lot" => array("type"=>"one", "model" => "Loturi", "key" => "lot_id"),
		);
    var $_defaultForm = array();
    
    function evidentaIesiri($click = "", $dblClick = "", $selected = 0) {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Pos");
        $dg->addHeadColumn("Data economica");
        $dg->addHeadColumn("Cantitate");
        $dg->addHeadColumn("Pret iesire LEI");
        $dg->addHeadColumn("Pret iesire VAL");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        $vpid_id = 0;
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $comp = $this->comp;
            if ($vp_id != $comp->vp_id) {
                $date_pos = $this->comp->pos;
                $vp_id = $comp->vp_id;
            }
            $dg->addColumn($date_pos->denumire);
            $dg->addColumn(c_data($date_pos->data_economica));
            $dg->addColumn($this->cantitate);
            $dg->addColumn($comp->pret_vanzare);
            $dg->addColumn("");
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
}
?>
