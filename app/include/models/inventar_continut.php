<?php 
class InventarContinut extends Model
{
    var $tbl = "inventar_continut";
    
    var $_relations = array("produs"=>array("type"=>"one", "model"=>"Produse", "key"=>"produs_id", "value"=>"denumire"), );
    
    function lista($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Produs");
		$dg->addHeadColumn("UM");
        $dg->addHeadColumn("Stoc scriptic");
        $dg->addHeadColumn("Stoc faptic");
		$dg->addHeadColumn("Pret Achzitie");
		$dg->addHeadColumn("Diferenta Inventar");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $dg->addColumn($this->produs->denumire);
			//um
			$um = new UnitatiMasura($this->produs->unitate_masura_id);
			$dg->addColumn($um->denumire);
			
            $dg->addColumn(treizecimale($this->stoc_scriptic), array("style" => "text-align:right"));
            $dg->addColumn($this->getStocFaptic(), array("style" => "text-align:right"));
			 $dg->addColumn($this->getPretAchizitie(), array("style" => "text-align:right"));
            $dg->addColumn('<div id="diferenta_'. $this -> id .'">'. treizecimale($this -> stoc_faptic - $this -> stoc_scriptic) .'</div>', array("style" => "text-align:right"));
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
    
    function getStocFaptic()
    {
  		return '<input type="text" style="text-align:right" value="'.treizecimale($this->stoc_faptic).'" onChange="xajax_save_content('. $this -> id .',this.value)" onFocus="this.select()">';  
    }
	
	function getPretAchizitie() {
		return '<input type="text" id="pret_'. $this -> id .'" style="text-align:right" value="'. douazecimale($this -> pret_achizitie) .'" onChange="xajax_save_pret('. $this -> id .',$(this).val())" onBlur="xajax_save_pret('. $this -> id .',$(this).val())" onFocus="this.select()">';  
		
	}
}
?>
