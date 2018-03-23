<?php 
class Posuri extends Model
{
    var $tbl = "posuri";
    var $_relations = array("gestiune"=>array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value"=>"denumire"));
    var $_defaultForm = array("pos_id"=>array("type"=>"hidden"), "cod"=>array("type"=>"text", "label"=>"Cod", "attributes"=>array("tabindex"=>1, "style"=>"width:400px;")), "gestiune"=>array("label"=>"Gestiune", "attributes"=>array("tabindex"=>2, "style"=>"width:300px;")), );
    
    function lista($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Id");
		$dg->addHeadColumn("Cod");
        $dg->addHeadColumn("Gestiune");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
			$dg->addColumn($this->id);
            $dg->addColumn($this->cod);
            $dg->addColumn($this->gestiune->denumire);
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
    
    function select_multiple($selected = array())
    {
        $nr_r = count($this);
        $out = '<select multiple size="5" name="pos_id[]" id="pos_id" style="" onChange="'.$onChange.'">';
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            if (in_array($this->id, $selected))
            {
                $sel = "selected";
            }
            else
            {
                $sel = "";
            }
            $out .= '<option value="'.$this->id.'" '.$sel.'>'.$this->cod.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
}
?>
