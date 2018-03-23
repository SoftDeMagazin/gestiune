<?php
class Transferuri extends Model
{
	var $tbl="transferuri";
	var $_relations = array(
		"continut" => array("type" => "many", "model" => "TransferuriContinut", "key" => "transfer_id"),
		"gestiune" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_id"),
		"gestiune_destinatie" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_destinatie_id", "model_key" => "gestiune_id", "value" => "denumire"),
		"utilizator" => array("type" => "one", "model" => "Utilizatori", "key" => "utilizator_id"),
		"serie" => array("type" => "one", "model" => "SeriiNumerice", "key" => "serie_id"),
		);
	var $_defaultForm = array(
		"transfer_id" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"gestiune_destinatie" => array("label" => "Gestiune Destinatie"),
		"data_doc" => array("type"=>"text", "label"=>"Data", "attributes" => array("class" => "calendar")),
		"numar_doc" => array("type"=>"text", "label"=>"Numar Doc", "attributes" => array("readonly")),
		);	
	
	/**
	 * returneaza numarul curent al documentului
	 * @param int $gestiune_id id-ul gestiunii
	 * @return int
	 */
	function getNumar($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		return $serie -> curent+1;
	}
	
	function getSerie($gestiune_id) {
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($gestiune_id, 'transferuri');
		if(count($s))
		return $s -> serie;
	}
	
	function incrementSerie($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$serie -> increment();
	}  
		
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Numar Doc");
			$dg -> addHeadColumn("Data Doc");
			$dg -> addHeadColumn("Gestiune Sursa");
			$dg -> addHeadColumn("Gestiune Destinatie");
			$dg -> addHeadColumn("Intocmit de");
			$dg -> addHeadColumn("Validat");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				$dg -> addColumn(c_data($this -> data_doc));
				$dg -> addColumn($this -> gestiune -> denumire);
				$dg -> addColumn($this -> gestiune_destinatie -> denumire);
				$dg -> addColumn($this -> utilizator -> nume);
				$dg -> addColumn($this -> salvat);
				if($this -> id == $selected) $class="rowclick";
				else $class="";
				$ck = $this -> stringReplace($click);
				$dck = $this -> stringReplace($dblClick);
				$dg -> setRowOptions(array(
				"class" => $class,
				"onMouseOver"=>"$(this).addClass('rowhover')", 
				"onMouseOut"=>"$(this).removeClass('rowhover')",
				"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
				"onDblClick"=>"$dck"
				));
				$dg -> index();
				}
			$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}			
	/**
	 * anuleaza scaderile efectue
	 * @return bool;
	 */
	function anulareScaderi() {
		$continut = $this -> continut;
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$iesiri = new TransferuriIesiri("where comp_id = '".$cnt -> id."'");
			foreach($iesiri as $iesire) {
				//refac lotul din care s-a efectuat scaderea
				$lot = new Loturi($iesire -> lot_id);
				$lot -> cantitate_ramasa += $iesire -> cantitate;
				$lot -> save();
				//sterg iesire
				$iesire -> delete();
			}
		}
	}
	
	function emitereFactura() {
		$factura = new Facturi();
		$factura -> tert_id = $gest_dest -> tert_id;
		$factura -> gestiune_id = $this -> gestiune_id;
		$factura -> societate_id = $gest_sursa -> societate_id;
		$cota_tva = new CoteTva("where valoare = '19.00'");
		$factura -> cota_tva_id = $cota_tva -> id;
	 	$factura -> utilizator_id = $this -> utilizator_id;
		$factura -> valuta = 'LEI';
		$factura -> numar_doc = $factura -> getNumar($gest_sursa -> id);
	
		$serie = $factura -> getSerie($gest_sursa -> id);
		$factura -> serie_id = $serie -> id;
			
		$serie -> increment();
			
		$factura -> data_factura = $this -> data_doc;
		$factura -> data_scadenta = $this -> data_doc;
		$factura -> data_introducere = $this -> data_inregistrare;
		$factura -> salvat = 'DA';
		$factura -> tip_factura = 'interna';
		$factura -> transfer_id = $this -> id;
		$factura -> save();
		$continut = $this -> continut;
			
		foreach($continut as $cnt) {
			$produs = new Produse($cnt -> produs_id);
			$loturi = $produs -> getLoturiFifo($cnt -> cantitate, $gest_sursa -> id);
			foreach($loturi as $lot) {
				$factura -> adaugaContinut($cnt -> produs_id, $lot[1], $lot[0] -> pret_intrare_ron);
			}								
		}
		$factura -> scadStoc();
		
		$this -> tip_doc = "factura";
		$this -> doc_id = $factura -> id;
		$this -> save();
		
		return $factura;
	}
	
	function emitereAviz($scad_stoc=true) {
		$aviz = new Avize();
		$aviz -> gestiune_id = $this -> gestiune_id;
		$aviz -> data_doc = $this -> data_doc;
		$aviz -> tip_aviz = "la_transfer";
		
		$aviz -> numar_doc = $aviz -> getNumar($this -> gestiune_id);
		
		$aviz -> incrementSerie($this -> gestiune_id);
		
		$serie = $aviz -> getSerie($this -> gestiune_id);
		
		$aviz -> serie_id = $serie -> id;
			
		$aviz -> data_inregistrare = $this -> data_inregistrare;
		$aviz -> utilizator_id = $this -> utilizator_id;
		$aviz -> transfer_id = $this -> id;
		$aviz -> salvat = "DA";
		$aviz -> save();
			
		if(AVIZ_PRET_VANZARE) {
			$continut = $this -> continut;
			foreach($continut as $cnt) {
				$produs = new ViewProduseGestiuni();
				$produs -> getByGestiuneAndProdus($this -> gestiune_id, $cnt -> produs_id);	
				$aviz -> adaugaContinut($cnt -> produs_id, $cnt -> cantitate, $produs -> pret_ron);
			}
		} else {
			$continut = $this -> continut;
			foreach($continut as $cnt) {
				$aviz -> adaugaContinut($cnt -> produs_id, $cnt -> cantitate);
			}
		}	
		
		if($scad_stoc) {
			$aviz -> scadStoc();
			$this -> tip_doc = "aviz";	
			$this -> doc_id = $aviz -> id;
			$this -> save();
		}
		return $aviz;
	}

	function genereazaNirFactura() {
		$factura = new Facturi("where transfer_id = '". $this -> id ."'");
			
		$intrare = new FacturiIntrari();
		$intrare -> tip_doc = "factura";
		$intrare -> cota_tva_id = $factura -> cota_tva_id;
		$intrare -> tert_id = $gest_sursa -> tert_id;
		$intrare -> gestiune_id = $gest_dest -> id;
		$intrare -> societate_id = $gest_dest -> societate_id;
		$intrare -> utilizator_id = $this -> utilizator_id;
		$intrare -> numar_doc = $factura -> numar_doc;
		$intrare -> data_factura = $factura -> data_factura;
		$intrare -> data_scadenta = $factura -> data_scadenta;
		$intrare -> data_introducere = $factura -> data_introducere;
		$intrare -> salvat = 'DA';
		$intrare -> save();
			
		$continut = new FacturiContinut();
		$continut -> fromString(" 
				where factura_id = '". $factura -> id ."' 
				group by produs_id, denumire, unitate_masura_id, pret_vanzare_ron, produs_id",
				array(
				"produs_id",	
				"denumire", 
				"unitate_masura_id", 
				"sum(cantitate) as cantitate", 
				"pret_vanzare_ron"
				)
		);
		foreach($continut as $cnt) {
			$intrare -> adaugaContinut($cnt -> produs_id, $cnt -> cantitate, $cnt -> pret_vanzare_ron);
		}
		$intrare -> salveazaTotaluri();
			
		$nir = new Niruri();
		$nir -> genereazaNir($intrare -> id);
		$nir -> genereazaLoturi();
		
		return $nir;
	}	
	
	function genereazaNirAviz() {
		$aviz = new Avize("where transfer_id = '". $this -> id ."'");
		
		$gest_sursa = $this -> gestiune;
		$gest_dest = $this -> gestiune_destinatie;
			
		$cota_tva = new CoteTva();
		$cota_tva -> getTvaZero();
		
		$intrare = new FacturiIntrari();
		$intrare -> tip_doc = "aviz";
		$intrare -> cota_tva_id = $cota_tva -> id;
		$intrare -> tert_id = $gest_sursa -> tert_id;
		$intrare -> gestiune_id = $gest_dest -> id;
		$intrare -> societate_id = $gest_dest -> societate_id;
		$intrare -> utilizator_id = $this -> utilizator_id;
		$intrare -> numar_doc = $aviz -> numar_doc;
		$intrare -> data_factura = $aviz -> data_doc;
		$intrare -> data_scadenta = $aviz -> data_doc;
		$intrare -> data_introducere = $aviz -> data_inregistrare;
		$intrare -> salvat = 'DA';
		$intrare -> save();
		if(AVIZ_PRET_VANZARE) {
			$continut = $aviz -> continut;
			foreach($continut as $cnt) {
				$intrare -> adaugaContinut($cnt -> produs_id, $cnt -> cantitate, $cnt -> pret_vanzare_ron);
			} 
		} else {
			$continut = $this -> db -> getRows("
				select
				avize_iesiri.produs_id,
				sum(avize_iesiri.cantitate) as cantitate,
				loturi.pret_intrare_ron as pret_intrare_ron
				from avize_iesiri
				inner join avize_continut on avize_continut.continut_id = avize_iesiri.comp_id
				inner join loturi using(lot_id)
				where avize_continut.aviz_id = '". $aviz -> id ."'
				group by avize_iesiri.produs_id, loturi.pret_intrare_ron
				"
			);
			foreach($continut as $cnt) {
				$intrare -> adaugaContinut($cnt['produs_id'], $cnt['cantitate'], $cnt['pret_intrare_ron']);
			}
		}
		$intrare -> salveazaTotaluri();
		
		$nir = new Niruri();
		$nir -> genereazaNir($intrare -> id);
		$nir -> genereazaLoturi();
		
		return $nir;
	}
	
	function genereazaNirTransfer() {
		$gest_dest = $this -> gestiune_destinatie;
		$gest_sursa = $this -> gestiune;
		$cota_tva = new CoteTva();
		$cota_tva -> getTvaZero();
		
		$intrare = new FacturiIntrari();
		$intrare -> tip_doc = "transfer";
		$intrare -> cota_tva_id = $cota_tva -> id;
		$intrare -> tert_id = $gest_sursa -> tert_id;
		$intrare -> gestiune_id = $gest_dest -> id;
		$intrare -> societate_id = $gest_dest -> societate_id;
		$intrare -> utilizator_id = $this -> utilizator_id;
		$intrare -> numar_doc = $this -> numar_doc;
		$intrare -> data_factura = $this -> data_doc;
		$intrare -> data_scadenta = $this -> data_doc;
		$intrare -> data_introducere = $this -> data_inregistrare;
		$intrare -> salvat = 'DA';
		$intrare -> save();

		$continut = $this -> db -> getRows("
			select
			transferuri_iesiri.produs_id,
			sum(transferuri_iesiri.cantitate) as cantitate,
			loturi.pret_intrare_ron as pret_intrare_ron
			from transferuri_iesiri
			inner join transferuri_continut on transferuri_continut.continut_id = transferuri_iesiri.comp_id
			inner join loturi using(lot_id)
			where transferuri_continut.transfer_id = '". $this -> id ."'
			group by transferuri_iesiri.produs_id, loturi.pret_intrare_ron
			"
		);
		foreach($continut as $cnt) {
			$intrare -> adaugaContinut($cnt['produs_id'], $cnt['cantitate'], $cnt['pret_intrare_ron']);
		}
		
		$intrare -> salveazaTotaluri();
		
		$nir = new Niruri();
		$nir -> genereazaNir($intrare -> id);
		$nir -> genereazaLoturi();
		
		return $nir;
	}

	function genereazaNir() {
		$gest_sursa = new Gestiuni($this -> gestiune_id);
		$gest_dest = new Gestiuni($this -> gestiune_destinatie_id);
		
		if($gest_sursa -> societate_id != $gest_dest -> societate_id) {
			$nir = $this -> genereazaNirFactura();
		} else {
			if(TRANSFER_EMIT_AVIZ) {
				$nir = $this -> genereazaNirAviz();
			} else {
				$nir = $this -> genereazaNirTransfer();
			}
		}
		$this -> nir_id = $nir -> id;
		$this -> save();
	}
	
	function validareDocument() {
		$gest_sursa = new Gestiuni($this -> gestiune_id);
		$gest_dest = new Gestiuni($this -> gestiune_destinatie_id);
		
		if($gest_sursa -> societate_id != $gest_dest -> societate_id) {
			//daca sunt pe societati diferite emit factura
			$this -> emitereFactura();
		} else {
			if(TRANSFER_EMIT_AVIZ) {
				$this -> emitereAviz();
			} else {
				$this -> scadStoc();
				$this -> tip_doc = "nota";
				$this -> doc_id = '-1';
				$this -> save();
			}
		}
		$this -> genereazaNir();
	}	
	
	/**
	 * scade stocul fifo
	 * @return bool
	 */	
	function scadStoc() {
		$continut = $this -> continut;
		if(!count($continut)) return false;	
		foreach($continut as $cnt) {
			$cnt -> produs -> scadStoc($cnt -> cantitate, $this -> gestiune_id, $cnt -> id, 'TransferuriIesiri');			
		}
		return TRUE;
	}	
	
	/**
	 * sterge documentul
	 * @return 
	 */
	function sterge() {
		global $db;
		if($gest_sursa -> societate_id != $gest_dest -> societate_id) {
			$factura = new Facturi("where transfer_id = '". $this -> id ."'");
			if(count($factura)) $factura -> sterge();
		} else {
			$aviz = new Avize("where transfer_id = '". $this -> id ."'");
			if(count($aviz)) $aviz -> sterge();
		}
		
		$nir = new Niruri($this -> nir_id);
		if(count($nir)) $nir -> sterge();
		
		$this -> anulareScaderi();
		
		//$this -> stergeContinut();
		//$this -> delete();
		$this -> anulat = 'DA';
		$this -> save();
	}
	
	function stergeContinut() {
		global $db;
		$sql = "DELETE FROM transferuri_continut WHERE transfer_id = '". $this -> id ."'";
		$db -> query($sql);
	}
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Nota Transfer Nr: '. $this -> numar_doc .'</legend>	
		Data: '. c_data($this -> data_doc) .'<br>
		Intocmit: '. $this -> utilizator -> nume .'<br>
		Transfer din gestiunea: '. $this -> gestiune -> denumire .' in gestiunea: '. $this -> gestiune_destinatie -> denumire .'
		</fieldset>
		';
		return $out;
	}
	
	function getByNirId($nir_id) {
		$this -> fromString("where `nir_id` = '$nir_id'");
	}
}
?>