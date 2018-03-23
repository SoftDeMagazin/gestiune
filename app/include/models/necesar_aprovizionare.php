<?php 
class NecesarAprovizionare extends Model
{
    var $tbl = "necesar_aprovizionare";
    
    var $_relations = array("gestiune"=>array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value"=>"denumire"), "utilizator"=>array("type"=>"one", "model"=>"Utilizatori", "key"=>"utilizator_id", "value"=>"nume"), );
    
    function lista($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Numar_doc");
        $dg->addHeadColumn("Gestiune");
        $dg->addHeadColumn("Utilizator");
        $dg->addHeadColumn("Data");
        $dg->addHeadColumn("Realizat");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $dg->addColumn($this->numar_doc);
            $dg->addColumn($this->gestiune->denumire);
            $dg->addColumn($this->utilizator->nume);
            $dg->addColumn(c_data($this->data));
            if ($this->realizat == 1)
                $dg->addColumn('DA');
            else
                $dg->addColumn('NU');
                
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
