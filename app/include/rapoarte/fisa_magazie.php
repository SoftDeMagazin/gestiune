<?php
class FisaMagazie {
	var $html;
	
	function __construct($produs_id, $filtre) {
		$this -> genereazaRaport($produs_id, $filtre);
	}
	
	function genereazaRaport($produs_id, $filtre) {
		global $db;
		if(is_array($filtre['gestiune_id'])) {
			$sql_gest = " in (". implode(',', $filtre['gestiune_id']) .") ";
		}
		else if(is_numeric($filtre['gestiune_id'])) {
			$sql_gest = "  = '". $filtre['gestiune_id'] ."'";
		}
		
		$sql = "
		select * from (
			select 
				data_intrare as data,
				doc_id,
				sum(cantitate_init) as cantitate,
				doc_tip,
				'intrare' as tip_operatie,
				sum(cantitate_init*loturi.pret_intrare_ron)  as suma 
			from loturi 
			where 
				loturi.produs_id = '$produs_id' 
				and data_intrare between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `gestiune_id` ". $sql_gest ."
			group by 
				data,
				doc_id,
				doc_tip	
			
			union all
			
			select 
				facturi.data_factura as data,
				facturi.factura_id, 
				sum(facturi_iesiri.cantitate) as cantitate, 
				'factura' as doc_tip, 'iesire' as iesire,
				sum(facturi_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				facturi_iesiri
			 	inner join facturi_continut 
					on facturi_continut.continut_id = facturi_iesiri.comp_id
				inner join facturi 
					using(factura_id)
				inner join loturi
					using(lot_id)	
			where 
				facturi_iesiri.produs_id = '$produs_id' 
				and data_factura between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `facturi_iesiri`.`gestiune_id` ". $sql_gest ."
			group by 
				facturi.data_factura, 
				facturi.factura_id
			
			union all
			
			select 
				inventare.data_inventar as data,
				inventare.inventar_id, 
				sum(inventar_continut_iesiri.cantitate) as cantitate, 
				'inventar' as doc_tip, 'iesire' as iesire,
				sum(inventar_continut_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				inventar_continut_iesiri
			 	inner join inventar_continut 
					on inventar_continut.inventar_continut_id = inventar_continut_iesiri.comp_id
				inner join inventare 
					using(inventar_id)
				inner join loturi
					using(lot_id)	
			where 
				inventar_continut_iesiri.produs_id = '$produs_id' 
				and inventare.data_inventar between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `inventare`.`gestiune_id` ". $sql_gest ."
			group by 
				inventare.data_inventar, 
				inventare.inventar_id	
				
			union all
			
			select 
				deprecieri.data_doc as data,
				deprecieri.depreciere_id, 
				sum(deprecieri_iesiri.cantitate) as cantitate, 
				'depreciere' as doc_tip, 'iesire' as iesire,
				sum(deprecieri_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				deprecieri_iesiri
			 	inner join deprecieri_continut 
					on deprecieri_continut.depreciere_continut_id = deprecieri_iesiri.comp_id
				inner join deprecieri 
					using(depreciere_id)
					inner join loturi
					using(lot_id)
			where 
				deprecieri_iesiri.produs_id = '$produs_id' 
				and deprecieri.data_doc between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `deprecieri`.`gestiune_id` ". $sql_gest ."
			group by 
				deprecieri.data_doc, 
				deprecieri.depreciere_id
				
			union all
			
			select 
				avize.data_doc as data,
				avize.aviz_id, 
				sum(avize_iesiri.cantitate) as cantitate, 
				'aviz' as doc_tip, 'iesire' as iesire,
				sum(avize_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				avize_iesiri
			 	inner join avize_continut 
					on avize_continut.continut_id = avize_iesiri.comp_id
				inner join avize 
					using(aviz_id)
					inner join loturi
					using(lot_id)
			where 
				avize_iesiri.produs_id = '$produs_id' 
				and avize.data_doc between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `avize`.`gestiune_id` ". $sql_gest ."
			group by 
				avize.data_doc, 
				avize.aviz_id	
					
			union all
			
			select 
				vanzari_pos.data_economica as data,
				vanzari_pos.vp_id, 
				sum(vanzari_pos_continut_iesiri.cantitate) as cantitate, 
				'vanzari' as doc_tip, 'iesire' as iesire,
				sum(vanzari_pos_continut_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				vanzari_pos_continut_iesiri
			 	inner join vanzari_pos_continut 
					on vanzari_pos_continut.continut_id = vanzari_pos_continut_iesiri.comp_id
				inner join vanzari_pos 
					using(vp_id)
				inner join posuri
					using(pos_id)	
					inner join loturi
					using(lot_id)
			where 
				vanzari_pos_continut_iesiri.produs_id = '$produs_id' 
				and vanzari_pos.data_economica between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `posuri`.`gestiune_id` ". $sql_gest ."
			group by 
				vanzari_pos.data_economica, 
				vanzari_pos.vp_id	
			
			union all
				
			select 
				bonuri_consum.data_doc as data,
				bonuri_consum.bon_consum_id, 
				sum(bonuri_consum_iesiri.cantitate) as cantitate, 
				'bon_consum' as doc_tip, 'iesire' as iesire,
				sum(bonuri_consum_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				bonuri_consum_iesiri
			 	inner join bonuri_consum_continut 
					on bonuri_consum_continut.continut_id = bonuri_consum_iesiri.comp_id
				inner join bonuri_consum 
					using(bon_consum_id)
					inner join loturi
					using(lot_id)
			where 
				bonuri_consum_iesiri.produs_id = '$produs_id' 
				and bonuri_consum.data_doc between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `bonuri_consum`.`gestiune_id` ". $sql_gest ."
			group by 
				bonuri_consum.data_doc, 
				bonuri_consum.bon_consum_id		
				
			union all
				
			select 
				transferuri.data_doc as data,
				transferuri.transfer_id, 
				sum(transferuri_iesiri.cantitate) as cantitate, 
				'transfer' as doc_tip, 'iesire' as iesire,
				sum(transferuri_iesiri.cantitate*loturi.pret_intrare_ron)  as suma 
			from 
				transferuri_iesiri
			 	inner join transferuri_continut 
					on transferuri_continut.continut_id = transferuri_iesiri.comp_id
				inner join transferuri 
					using(transfer_id)
				inner join loturi
					using(lot_id)	
			where 
				transferuri_iesiri.produs_id = '$produs_id' 
				and transferuri.data_doc between '". data_c($filtre['from']) ."' and '". data_c($filtre['end']) ."'
				and `transferuri`.`gestiune_id` ". $sql_gest ."
			group by 
				transferuri.data_doc, 
				transferuri.transfer_id			
		) as tbl
		order by data asc, tip_operatie desc	
		
		
		";
		
		$produs = new Produse($produs_id);
		$ieri = strtotime ( '-1 day' , strtotime ( data_c($filtre['from']) ) ) ;
		$ieri = date ( 'Y-m-d' , $ieri );
		
		$stoc = $produs -> stocLaData($ieri, $filtre['gestiune_id']);
		$sold = $produs -> stocLaDataValoric($ieri, $filtre['gestiune_id']);
		
		$gestiune = new Gestiuni($filtre['gestiune_id']);
		$out = "";
		$out .= '<h2 align="center">FISA MAGAZIE</h2>';
		$out .= '<div align="center">'. $filtre['from'] .' - '. $filtre['end'] .'</div>';
		$out .= '<strong>GESTIUNE: </strong>'.$gestiune -> denumire;
		$out .= '<h3>ARTICOL: '. $produs -> denumire .'</h3>';
		$out .= '<strong>CATEGORIE:</strong> '. $produs -> categorie -> denumire .'<br/>';
		$out .= '<strong>UM:</strong> '. $produs -> unitate_masura -> denumire .'<br/>';
		
		$out .= '<strong>Stoc Initial:</strong> '. treizecimale($stoc);
		$out .= '<br><strong>Sold Initial:</strong> '. treizecimale($sold);
		$data = $db -> getRows($sql);
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "fisa_mag_cont", "class" => ""));
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Document");
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Intrare");
		$dg -> addHeadColumn("Iesire");
		$dg -> addHeadColumn("Valoare");
		$dg -> addHeadColumn("Stoc");
		$dg -> addHeadColumn("Sold");
		$dg -> addHeadColumn("Partener");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		
		foreach($data as $row) {
			$dg -> addColumn(c_data($row['data']));
			$partener = "";
			switch($row['doc_tip']) {
				case "nir": {
					$doc = new Niruri($row['doc_id']);
					$partener = $doc -> tert;
					$transfer = new Transferuri();
					$transfer -> getByNirId($doc -> id);
					if(count($transfer)) {
						$partener = $transfer -> gestiune;
					}
				}break;
				
				case "bon_consum": {
					$doc = new BonuriConsum($row['doc_id']);
					$partener = $doc -> gestiune;
				}break;
				
				case "factura_retur": {
				}
				case "factura": {
					$doc = new Facturi($row['doc_id']);
				
					$partener = $doc -> tert -> denumire .' - '.$doc -> tert -> cod_fiscal;
					if($doc -> transfer_id > 0) {
						$partener .= ' ('.$doc -> transfer -> gestiune_destinatie -> denumire.')';
					}
				}break;
				case "depreciere": {
					$doc = new Deprecieri($row['doc_id']);
					$partener = $doc -> gestiune;					
				}break;
				case "inventar": {
					$doc = new Inventare($row['doc_id']);
					$partener = $doc -> gestiune;
				}break;
				case "aviz": {
					$doc = new Avize($row['doc_id']);
					switch($doc -> tip_aviz) {
						case "la_transfer": {
							$partener = $doc -> transfer -> gestiune_destinatie;
						}break;
						case "doc_pv": {
							$partener = $doc -> tert;
						}break;
						case "doc_pa": {
							$partener = $doc -> tert;
						}break;
					}
				}break;
				case "transfer": {
					$doc = new Transferuri($row['doc_id']);
					$partener = $doc -> gestiune_destinatie;
				}break;
				case "vanzari": {
					$doc = new VanzariPos($row['doc_id']);
					$partener = "Vanzari POS: ".$doc -> pos -> cod;
				}break;
			}
			$dg -> addColumn(strtoupper($row['doc_tip']));
			$dg -> addColumn($doc -> numar_doc, array("style" => "text-align:center;"));
			
			if($row['tip_operatie'] == 'intrare') {
				$dg -> addColumn(treizecimale($row['cantitate']), array("align" => "right"));
				$dg -> addColumn("&nbsp;");
				$stoc += $row['cantitate'];
				$sold += $row['suma'];
				$ttl_intrari += $row['cantitate'];
			} else {
				$dg -> addColumn("&nbsp;");
				$dg -> addColumn(treizecimale($row['cantitate']), array("align" => "right"));
				$stoc -= $row['cantitate'];
				$sold -= $row['suma'];
				$ttl_iesiri += $row['cantitate'];
			}
			$dg -> addColumn(douazecimale($row['suma']), array("align" => "right"));
			$dg -> addColumn(treizecimale($stoc), array("align" => "right"));
			$dg -> addColumn(douazecimale($sold), array("align" => "right"));
			
			if(get_class($partener) == "Terti") {
				$part_str = '<strong>'.$partener -> denumire .' - '. $partener -> cod_fiscal.'</strong>';
			}
			
			if(get_class($partener) == "Gestiuni") {
				$part_str = '<strong>GESTIUNE: '.$partener -> denumire .'</strong>';
			}
			
			if(is_string($partener)) {
				$part_str = '<strong>'. $partener .'</strong>';
			}
			$dg -> addColumn($part_str, array("style" => "text-align:left;padding-left:5px;"));
			$dg -> index();
		}
		
		$dg -> addColumn("Total", array("colspan" => "3"));
		$dg -> addColumn(treizecimale($ttl_intrari), array("align" => "right"));
		$dg -> addColumn(treizecimale($ttl_iesiri), array("align" => "right"));	
		$dg -> addColumn("&nbsp;");
		$dg -> addColumn("&nbsp;");
		
		$out .= $dg -> getDataGrid();	
		$out .= '<strong>Stoc Final:</strong> '. treizecimale($stoc);
		$out .= '<br><strong>Sold Final:</strong> '. treizecimale($sold);
		$this -> html = $out;	
	}
	
	function getHtml() {
		return $this -> html;
	}

}
?>