<?php 
class Inventare extends Model {
    var $tbl = "inventare";
    
    var $_relations = array("gestiune"=>array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value"=>"denumire"), "utilizator"=>array("type"=>"one", "model"=>"Utilizatori", "key"=>"utilizator_id", "value"=>"nume"), );
    
    function lista($click = "", $dblClick = "", $selected = 0) {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Numar_doc");
        $dg->addHeadColumn("Gestiune");
        $dg->addHeadColumn("Utilizator");
        $dg->addHeadColumn("Data");
		$dg->addHeadColumn("Inchis");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++) {
            $this->fromDataSource($i);
            $dg->addColumn($this->numar_doc);
            $dg->addColumn($this->gestiune->denumire);
            $dg->addColumn($this->utilizator->nume);
            $dg->addColumn(c_data($this->data_inventar));
			if($this->inchis ==1)
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
	
	function sterge() {
		$this -> anulareScaderi();
		$this -> anulareIntrari();
		$sql = "delete form inventar_continut where inventar_id = '". $this -> id ."'";
		$this -> db -> query($sql);
		$this -> delete();
	}
	
	function anulareScaderi() {
		$continut = new InventarContinut("where inventar_id = '". $this -> id ."'");
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$iesiri = new InventarContinutIesiri("where comp_id = '".$cnt -> id."'");
			foreach($iesiri as $iesire) {
				//refac lotul din care s-a efectuat scaderea
				$lot = new Loturi($iesire -> lot_id);
				if(count($lot)) {
					$lot -> cantitate_ramasa += $iesire -> cantitate;
					$lot -> save();
				}
				//sterg iesire
				$iesire -> delete();
			}
		}
	}
	
	function anulareIntrari() {
		$sql = "
		delete from loturi where doc_tip = 'inventar' and doc_id = '". $this -> id ."'
		and gestiune_id = '". $this -> gestiune_id ."'
		";
		$this -> db -> query($sql);
	}
}
?>
