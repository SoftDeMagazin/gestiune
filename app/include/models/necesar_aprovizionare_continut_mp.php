<?php 
class NecesarAprovizionareContinutMp extends Model
{
    var $tbl = "necesar_aprovizionare_continut_mp";
    
    var $_relations = array("produs"=>array("type"=>"one", "model"=>"Produse", "key"=>"produs_id", "value"=>"denumire"), );
    
    /*function lista($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Produs");
        $dg->addHeadColumn("UM");
        $dg->addHeadColumn("Stoc");
        $dg->addHeadColumn("Cantitate necesara");
        $dg->addHeadColumn("Diferenta");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $dg->addColumn($this->produs->denumire);
            $dg->addColumn($this->produs->unitate_masura->denumire);
            $dg->addColumn($this->stoc);
            $dg->addColumn($this->cantitate_necesara);
			$dg->addColumn($this->stoc-$this->cantitate_necesara);
            
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
        }*/
        
        function lista($necesar_id)
        {
        	global $db;
	        $na = new NecesarAprovizionare($necesar_id);
	        $sql = "select p.produs_id as produs_id,
       					p.denumire as produs,
       					um.denumire as um,
       					s.stoc as stoc,
       					ifnull(SUM(cantitate_necesara),0) as cantitate_necesara
					from necesar_aprovizionare_continut_mp nacmp
					inner join produse p on p.produs_id = nacmp.produs_id
					inner join unitati_masura um on um.unitate_masura_id = p.unitate_masura_id
					left join stocuri s on s.produs_id = p.produs_id
					where nacmp.doc_id = $necesar_id AND s.gestiune_id = '". $na -> gestiune_id ."'
					group by produs_id
					";
						     
	        $data = $db->getRows($sql);
	        $dg = new DataGrid(array("style"=>"width:98%;margin:10px auto;", "border"=>"0", "id"=>"fisa_mag_cont", "class"=>"tablesorter"));
	        $dg->addHeadColumn("Produs");
	        $dg->addHeadColumn("UM");
	        $dg->addHeadColumn("Stoc");
	        $dg->addHeadColumn("Cantitate necesara");
	        $dg->addHeadColumn("Necesar Achzitie");
	        
	        $dg->setHeadAttributes(array());
	        $nr_r = count($this);
	        
	        foreach ($data as $row)
	        {
	            $dg->addColumn($row['produs']);
	            $dg->addColumn($row['um']);
	            $dg->addColumn(treizecimale($row['stoc']), array("style" => "text-align:right;"));
	            $dg->addColumn($row['cantitate_necesara'], array("style" => "text-align:right;"));
				if($row['cantitate_necesara'] > $row['stoc'])
	            	$dg->addColumn(douazecimale($row['cantitate_necesara'] - $row['stoc']), array("style" => "text-align:right;"));
				else {
					$dg->addColumn(douazecimale(0), array("style" => "text-align:right;"));
				}
	            $dg->index();
	        }
	        $out .= $dg->getDataGrid();
	        
	        return $out;
	        }
    }
?>
