<?php 
class Produse extends Model
{
    var $tbl = "produse";
    var $_relations = array("categorie"=>array("type"=>"one", "model"=>"Categorii", "key"=>"categorie_id", "value"=>"denumire"), "unitate_masura"=>array("type"=>"one", "model"=>"UnitatiMasura", "key"=>"unitate_masura_id", "value"=>"denumire"), "cota_tva"=>array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value"=>"valoare", "conditions"=>"where 1 order by `cod_tva` asc"), 
	"tip" => array("type"=>"one", "model"=>"TipuriProduse", "key"=>"tip_produs", "model_key" => "tip"),

	);
    var $_defaultForm = array("produs_id"=>array("type"=>"hidden"), "tip_produs"=>array("type"=>"select", "label"=>"Tip produs", "options"=>"SELECT tip as `value`, `descriere` FROM `tipuri_produse`", "attributes"=>array("tabindex"=>7)), "denumire"=>array("type"=>"text", "label"=>"Denumire", "attributes"=>array("style"=>"width:400px;")), "cod_produs"=>array("type"=>"text", "label"=>"Cod", "attributes"=>array("style"=>"width:400px;")), "cod_bare"=>array("type"=>"text", "label"=>"Cod Bare", "attributes"=>array("style"=>"width:400px;")), "nc8"=>array("type"=>"text", "label"=>"Cod intrastat", "attributes"=>array("style"=>"width:400px;", "class"=>"nc8")), "categorie"=>array("label"=>"Categorie", "attributes"=>array("style"=>"width:300px;")), "unitate_masura"=>array("label"=>"Unitate Masura", "attributes"=>array()), "cota_tva"=>array("label"=>"Cota Tva", "attributes"=>array("style"=>"width:300px;")), "pret_val"=>array("type"=>"text", "label"=>"Pret vanzare euro", "attributes"=>array()), "pret_ron"=>array("type"=>"text", "label"=>"Pret vanzare lei", "attributes"=>array()), "ambalare"=>array("type"=>"text", "label"=>"Ambalare", "attributes"=>array("style"=>"width:400px;")), "gest"=>'<div id="div_frm_gest">Gestiune</div>', );
    
    var $_validator = array(
		"pret_val"=>array(array("numeric", "Pretul trebuie sa fie numeric")), 
		"denumire"=>array(array("required", "Introduceti denumire"), 
		array("unique", "Denumire existenta")), 
	);
    
    function lista($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Denumire");
        $dg->addHeadColumn("Categorie");
        $dg->addHeadColumn("Pret EUR");
        $dg->addHeadColumn("Pret LEI");
        $dg->addHeadColumn("NC8");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $dg->addColumn($this->denumire);
            $dg->addColumn($this->categorie->denumire);
            $dg->addColumn($this->pret_val);
            $dg->addColumn($this->pret_ron);
            $dg->addColumn($this->btnInfoNC8());
            
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
    
    function lista_gestiuni($click = "", $dblClick = "", $selected = 0)
    {
        $dg = new DataGrid(array("style"=>"width:98%;margin:0px auto;", "border"=>"0", "id"=>"tbl_".$this->tbl."", "class"=>"tablesorter"));
        $dg->addHeadColumn("Denumire");
        $dg->addHeadColumn("Gestiune");
        $dg->addHeadColumn("Pret EUR");
        $dg->addHeadColumn("Pret LEI");
        $dg->setHeadAttributes(array());
        $nr_r = count($this);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $dg->addColumn($this->denumire);
            $dg->addColumn($this->gestiune);
            $dg->addColumn($this->pret_val);
            $dg->addColumn($this->pret_ron);
            
            if ($this->id == $selected)
                $class = "rowclick";
            else
                $class = "";
            $ck = $this->stringReplace($click);
            $dck = $this->stringReplace($dblClick);
            $dg->setRowOptions(array("class"=>$class, "onMouseOver"=>"$(this).addClass('rowhover')", "onMouseOut"=>"$(this).removeClass('rowhover')", "onClick"=>"".$ck."$('#selected_produs_gestiune_id').val('".$this->produs_gestiune_id."');$('#tbl_".$this->tbl." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');", "onDblClick"=>"$dck"));
            $dg->index();
        }
        $out = '<input type="hidden" id="selected_produs_gestiune_id" name="selected_produs_gestiune_id" value="'.$selected.'">';
        $out .= $dg->getDataGrid();
        return $out;
    }

    
    function btnInfoNC8()
    {
        if ($this->nc8)
            return '<input type="button" value="'.$this->nc8.'" onClick="xajax_infoNC8(\''.$this->nc8.'\')">';
        else
            return 'NA';
    }
    
    function select($size = 35, $id = "")
    {
        if (!$id)
            $id = 'sel_'.$this->tbl.'';
        $nr_r = count($this);
        $out = '<select  size="'.$size.'" id="sel_'.$this->tbl.'" style="width:100%;" onKeyPress="if(event.keyCode==13) xajax_selectProdus(this.options[this.selectedIndex].value);" onDblClick="xajax_selectProdus(this.options[this.selectedIndex].value);">';
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
	
	function selectTransformari($size = 35, $id = "")
    {
        if (!$id)
            $id = 'sel_'.$this->tbl.'';
        $nr_r = count($this);
        $out = '<select  size="'.$size.'" id="sel_'.$this->tbl.'" style="width:100%;" onKeyPress="if(event.keyCode==13) xajax_selectProdus(this.options[this.selectedIndex].value, $(\'#trans_pf_id\').val());" onDblClick="xajax_selectProdus(this.options[this.selectedIndex].value, $(\'#trans_pf_id\').val());">';
        for ($i = 0; $i < $nr_r; $i++)
        {
            $this->fromDataSource($i);
            $out .= '<option value="'.$this->id.'">'.$this->denumire.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    /**
     * disociaza produsele cu gestiunile care nu sunt in array
     * @param array $gestiuni vector id-uri gestiuni array($id1, $id2...)
     */
     
    function disociazaGestiuni($gestiuni = array())
    {
        $gestiuni_asociate = $this->getGestiuniAsociate();
        foreach ($gestiuni_asociate as $gest_id)
        {
            if (!in_array($gest_id, $gestiuni))
            {
                $cg = new ProduseGestiuni(" where `gestiune_id` = '$gest_id' and produs_id = '".$this->id."'");
                $cg->delete();
            }
        }
    }
    
    /**
     * asociaza produsele cu gestiunile din array ...
     * @param array $gestiuni vector id-uri gestiuni($id1, $id2, ...)
     * @param array $params parametrii aditionali
     */
    function asociazaCuGestiuni($gestiuni = array(), $params = array())
    {
        $gestiuni_asociate = $this->getGestiuniAsociate();
        foreach ($gestiuni as $gest_id)
        {
            if (!in_array($gest_id, $gestiuni_asociate))
            {
                $cg = new ProduseGestiuni($params);
                $cg->gestiune_id = $gest_id;
                $cg->produs_id = $this->id;
				$cg -> modificat = 1;
                $cg->save();
            }
        }
    }
    
    /**
     * returneaza gestiunile asociate
     * @return array
     */
    function getGestiuniAsociate()
    {
        $rows = $this->db->getRowsNum("select gestiune_id from produse_gestiuni where produs_id = '".$this->id."'");
        $out = array();
        foreach ($rows as $row)
        {
            $out[] = $row[0];
        }
        return $out;
    }
    
	function getLoturiFifo($cant, $gestiune_id) {
		$loturi = new Loturi();
        $loturi->getLoturiActive($this->id, $gestiune_id);
        $cantitate = $cant;
       	$nr_loturi = count($loturi);
		$loturi_out = array(); 
		if ($nr_loturi)
		{
		    $i = 0;
		    while ($cantitate > 0 && $i < $nr_loturi)
		    {
		        $lot = $loturi[$i];
		        if ($cantitate >= $lot->cantitate_ramasa)
		        {
		            $cantitate -= $lot->cantitate_ramasa;
		            $cantitate_iesire = $lot->cantitate_ramasa;         
					$loturi_out[] = array($lot, $cantitate_iesire);
					$i++;        
		        }
		        else
		        {
		            $lot_id = $lot->id;
		            $cantitate_iesire = $cantitate;    
		            $cantitate = 0;
		           	$loturi_out[] = array($lot, $cantitate_iesire);  
					$i++;  
		        }
		                 
		    }
		            
		    if($cantitate > 0)
		    {
		        $lot = new Loturi();
		        $lot->ultimulLot($this->id, $gestiune_id);
		        $lot_id = $lot->id;
				$cantitate_iesire = $cantitate;
		        $loturi_out[] = array($lot, $cantitate);       
		    }
			
			
		} else {
			 if ($cantitate > 0)
             {
   		 		$lot = new Loturi();
    			$lot->ultimulLot($this->id, $gestiune_id);
    			if (count($lot)) {
       				 $loturi_out[] = array($lot, $cantitate);
    			}
			}
		}
		return $loturi_out;
		
	}


    /**
     * scade stocul
     * @param float $cant cantitate
     * @param int $gestiune_id id-ul gestiunii pe care fac scaderea
     * @param int $comp_id id-ul componentetei documentului de pe care fac iesirea
     * @param string $model tipul iesirii ("FacturiIesiri", "TransferuriIesiri", "InventarIesiri" ...)
     */	
    function scadStoc($cant, $gestiune_id, $comp_id, $model)
    {
        switch ($this->tip_produs)
        {
        	case "pf": {
        		
        	}
            case "mp":
                {
                }
            case "marfa":
                {
                    $loturi = new Loturi();
                    $loturi->getLoturiActive($this->id, $gestiune_id);
                    $cantitate = $cant;
                    $nr_loturi = count($loturi);
                    if ($nr_loturi)
                    {
                        $i = 0;
                        while ($cantitate > 0 && $i < $nr_loturi)
                        {
                            $lot = $loturi[$i];
                            if ($cantitate >= $lot->cantitate_ramasa)
                            {
                                $cantitate -= $lot->cantitate_ramasa;
                                $lot_id = $lot->id;
                                $cantitate_iesire = $lot->cantitate_ramasa;
                                
                                $lot->cantitate_ramasa = '0.00';
                                $lot->save();
                                $i++;
                            }
                            else
                            {
                                $lot_id = $lot->id;
                                $cantitate_iesire = $cantitate;
                                
                                $lot->cantitate_ramasa -= $cantitate;
                                $lot->save();
                                $cantitate = 0;
                                $i++;
                                
                            }

                            
                            $iesiri_data = array("comp_id"=>$comp_id, "lot_id"=>$lot_id, "produs_id"=>$this->id, "gestiune_id"=>$gestiune_id, "cantitate"=>$cantitate_iesire);
                            $iesire = new Iesiri($iesiri_data, $model);
                            $iesire->save();
                            
                        }
                        
                        if ($cantitate > 0)
                        {
                            $lot = new Loturi();
                            $lot->ultimulLot($this->id, $gestiune_id);
                            $lot_id = $lot -> id;
							
                            $iesiri_data = array("comp_id"=>$comp_id, "lot_id"=>$lot_id, "produs_id"=>$this->id, "gestiune_id"=>$gestiune_id, "cantitate"=>$cantitate);
                            $iesire = new Iesiri($iesiri_data, $model);
                            $iesire->save();
                            
                            $lot->cantitate_ramasa -= $cantitate;
                            $lot->save();
                        }
                    } else {      
                        if ($cantitate > 0)
                        {
                            $lot = new Loturi();
                            $lot->ultimulLot($this->id, $gestiune_id);
							if(count($lot)) {
                            	$lot_id = $lot -> id;
								
                           	 	$iesiri_data = array("comp_id"=>$comp_id, "lot_id"=>$lot_id, "produs_id"=>$this->id, "gestiune_id"=>$gestiune_id, "cantitate"=>$cantitate);
                            	$iesire = new Iesiri($iesiri_data, $model);
                            	$iesire->save();
                            
                            	$lot->cantitate_ramasa -= $cantitate;
                            	$lot->save();
							} else {
								$lot = new Loturi();
								$lot -> gestiune_id = $gestiune_id;
								$gest = new Gestiuni($gestiune_id);
								$lot -> societate_id = $gest -> societate_id;
								$lot -> produs_id = $this -> id;
								$lot -> doc_id = -1;
								$lot -> doc_comp_id = -1;
								$lot -> doc_tip = 'init';
								$lot -> cantitate_init = '0.00';
								$lot -> cantitate_ramasa = '0.00';
								$lot -> data_intrare = data();
								$lot -> tip = 'init';
								$lot -> save();
								$lot_id = $lot -> id;
								
								$iesiri_data = array("comp_id"=>$comp_id, "lot_id"=>$lot_id, "produs_id"=>$this->id, "gestiune_id"=>$gestiune_id, "cantitate"=>$cantitate);
                            	$iesire = new Iesiri($iesiri_data, $model);
                            	$iesire->save();
                            
                            	$lot->cantitate_ramasa -= $cantitate;
                            	$lot->save();
								
							}
                        }                	
                    }
                }
                break;
            case "reteta":
                {
                    $retetar = new Retetar("where produs_id = '".$this->id."'");
                    foreach ($retetar as $comp)
                    {
                        $comp->componenta->scadStoc($cant * $comp->cantitate, $gestiune_id, $comp_id, $model);
                    }
                }
                break;
            case "serviciu":
                {
                }
                break;
        }
    }
    
    /**
     * returneaza stocul produsului la data
     * @param date $data data afisare
     * @param mixed $gestiune_id gestiunile pe care calculez stocul 0 - toate, gestiune_id sau array($id, $id)
     * @return float stoc
     */
    function stocLaData($data, $gestiune_id = 0)
    {
		$sql = "
		select stoc_la_data(produs_id, '".$gestiune_id."', '$data') as stoc
		from produse 
		where produs_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return $row['stoc'];
    }
	
	function stocLaDataValoric($data, $gestiune_id = 0) {
		$sql = "
		select stoc_la_data_valoric(produs_id, '".$gestiune_id."', '$data') as stoc
		from produse 
		where produs_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return $row['stoc'];
	}

	/**
	 * total iesiri la data pentru produs pe gestiune_id
	 * @param object $data
	 * @param object $gestiune_id
	 * @return 
	 */
	function iesiriLaData($data, $gestiune_id) {
		$sql = "
		select sum(cantitate) as iesiri from (
				select 
					facturi.data_factura as data,
					sum(facturi_iesiri.cantitate) as cantitate, 
					'factura' as doc_tip, 'iesire' as iesire
				from 
					facturi_iesiri
				 	inner join facturi_continut 
						on facturi_continut.continut_id = facturi_iesiri.comp_id
					inner join facturi 
						using(factura_id)
				where 
					facturi_iesiri.produs_id = '". $this -> id ."' 
					and data_factura between '1900-01-01' and '$data'
					and `facturi_iesiri`.`gestiune_id`  = '$gestiune_id'
				group by 
					facturi.data_factura
				
				union all
				
				select 
					inventare.data_inventar as data,
					sum(inventar_continut_iesiri.cantitate) as cantitate, 
					'inventar' as doc_tip, 'iesire' as iesire
				from 
					inventar_continut_iesiri
				 	inner join inventar_continut 
						on inventar_continut.inventar_continut_id = inventar_continut_iesiri.comp_id
					inner join inventare 
						using(inventar_id)
				where 
					inventar_continut_iesiri.produs_id = '". $this -> id ."' 
					and inventare.data_inventar between '1900-01-01' and '$data'
					and `inventare`.`gestiune_id`  = '$gestiune_id'
				group by 
					inventare.data_inventar
					
				union all
				
				select 
					deprecieri.data_doc as data,
					sum(deprecieri_iesiri.cantitate) as cantitate, 
					'depreciere' as doc_tip, 'iesire' as iesire
				from 
					deprecieri_iesiri
				 	inner join deprecieri_continut 
						on deprecieri_continut.depreciere_continut_id = deprecieri_iesiri.comp_id
					inner join deprecieri 
						using(depreciere_id)
				where 
					deprecieri_iesiri.produs_id = '". $this -> id ."' 
					and deprecieri.data_doc between '1900-01-01' and '$data'
					and `deprecieri`.`gestiune_id`  = '$gestiune_id'
				group by 
					deprecieri.data_doc 
				
				union all	
				select 
					bonuri_consum.data_doc as data,
					sum(bonuri_consum_iesiri.cantitate) as cantitate, 
					'bon_consum' as doc_tip, 'iesire' as iesire
				from 
					bonuri_consum_iesiri
				 	inner join bonuri_consum_continut 
						on bonuri_consum_continut.continut_id = bonuri_consum_iesiri.comp_id
					inner join bonuri_consum 
						using(bon_consum_id)
				where 
					bonuri_consum_iesiri.produs_id = '". $this -> id ."' 
					and bonuri_consum.data_doc between '1900-01-01' and '$data'
					and `bonuri_consum`.`gestiune_id`  = '$gestiune_id'
				group by 
					bonuri_consum.data_doc 		
					
				union all
				
				select 
					avize.data_doc as data,
					sum(avize_iesiri.cantitate) as cantitate, 
					'aviz' as doc_tip, 'iesire' as iesire
				from 
					avize_iesiri
				 	inner join avize_continut 
						on avize_continut.continut_id = avize_iesiri.comp_id
					inner join avize 
						using(aviz_id)
				where 
					avize_iesiri.produs_id = '". $this -> id ."' 
					and avize.data_doc between '1900-01-01' and '$data'
					and `avize`.`gestiune_id`  = '$gestiune_id'
				group by 
					avize.data_doc
				union all
				select 
					vanzari_pos.data_economica as data,
					sum(vanzari_pos_continut_iesiri.cantitate) as cantitate, 
					'vanzari' as doc_tip, 'iesire' as iesire
				from 
					vanzari_pos_continut_iesiri
				 	inner join vanzari_pos_continut 
						on vanzari_pos_continut.continut_id = vanzari_pos_continut_iesiri.comp_id
					inner join vanzari_pos 
						using(vp_id)
					inner join posuri
						using(pos_id)	
				where 
					vanzari_pos_continut_iesiri.produs_id = '". $this -> id ."'  
					and vanzari_pos.data_economica between '1900-01-01' and '$data'
					and `posuri`.`gestiune_id` = '$gestiune_id'
				group by 
					vanzari_pos.data_economica
		) as tbl	
		";		
	
		$row = $this -> db -> getRow($sql);
		return $row['iesiri'];
	}

	/**
	 * returneaza array materii prime array(produs_id, cantitate)
	 * @param object $cantitate [optional]
	 * @return 
	 */
    function getMateriiPrime($cantitate=1)
    {
        if ($this->tip_produs == 'mp' || $this -> tip_produs == 'marfa')
            return array(
				array("produs_id"=>$this->id,"cantitate"=>$cantitate)
				);
            
        $mp = array();
        $reteta_componente = new Retetar("WHERE produs_id=".$this->id);
        foreach ($reteta_componente as $comp)
        {
            if ($comp->componenta->tip_produs == 'reteta')
            {
                $produs = new Produse("WHERE produs_id='".$comp->componenta_id."'");
                $mp_temp = $produs->getMateriiPrime($cantitate*$comp -> cantitate);
				foreach($mp_temp as $mp_temp_elem)
					$mp[] = $mp_temp_elem;
            }
            else
            {
                $mp[] = array("produs_id"=>$comp->componenta_id, "cantitate"=>$cantitate*$comp->cantitate);
            }
        }
        
        return $mp;
    }
	
	/**
	 * returneaza pret mediu ach petru reteta
	 * @param int $gestiune_id
	 * @return float pret_mediu ach
	 */
	function getPretAchReteta($gestiune_id) {
		//incarc materiile prime
		$mps = $this -> getMateriiPrime(1);
		$pret_ach = 0;
		foreach($mps as $mp) {
			$prod = new Produse($mp['produs_id']);
			$pret_ach += $mp['cantitate']*$prod -> getPretMediuAchizitie($gestiune_id);
		}
		return $pret_ach;
	}
	
	/**
	 * returneaza pretul mediu de achzitie
	 * @param int $gestiune_id
	 * @return float pret_mediu
	 */
	function getPretMediuAchizitie($gestiune_id) {
		switch($this -> tip_produs) {
			//daca e marfa, materie prima calculez pret mediu pe loturile disponibile
			case "pf":{}
			case "mp": {}
			case "marfa": {
				$stoc = new Stocuri("where produs_id = '". $this -> id ."' and gestiune_id = '$gestiune_id'");
				$pret_mediu = 0;
				if(count($stoc)) {
					$pret_mediu = $stoc -> valoare_stoc_ron / $stoc -> stoc;
				}
				return $pret_mediu;
			}break;
			
			//daca e reteta	
			case "reteta": {
				return $this -> getPretAchReteta($gestiune_id);
			}break; 
		}
	}
    
	/**
	 * incarca produsele asociate unei gesiuni
	 * @param int $gestiune_id id-ul gestiunii
	 * @param string $conditions conditii sql
	 */
	function getByGestiuneId($gestiune_id, $conditions="") {
		$sql = " inner join produse_gestiuni using(produs_id) where gestiune_id = '$gestiune_id' ".$conditions;
		$this -> fromString($sql);
	}
	
	/**
	 * copiaza produsele primite ca parametru in gestiunea primita ca parametru
	 * 
	 * @param object $gestiune_id gestiunea in care sa se copieze categoriile
	 * @param object $categorii_ids id-urile categoriile ce se vor copia
	 * @return 
	 */
	function copiazaInGestiuneNoua($gestiune_sursa_id,$gestiune_id, $produse_ids)
	{
		$sql = "INSERT INTO produse_gestiuni (produs_id,gestiune_id,pret_ron,pret_val)
				SELECT pg.produs_id, $gestiune_id, pg.pret_ron,pg.pret_val
				FROM produse_gestiuni pg
				WHERE pg.produs_id in $produse_ids
					and pg.gestiune_id = $gestiune_sursa_id
				";
		$this->db->query($sql);
	}
}
?>
