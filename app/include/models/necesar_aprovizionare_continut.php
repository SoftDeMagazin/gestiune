<?php 
class NecesarAprovizionareContinut extends Model
{
    var $tbl = "necesar_aprovizionare_continut";
    
    var $_relations = array("produs"=>array("type"=>"one", "model"=>"Produse", "key"=>"produs_id", "value"=>"denumire"), );
    
    function lista($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Produs");
        $dg->addHeadColumn("UM");
        $dg->addHeadColumn("Cantitate dorita");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $dg->addColumn($this->produs->denumire);
            $dg->addColumn($this->produs->unitate_masura->denumire);
            $dg->addColumn($this->cantitate_dorita);
            
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
