<?php
/**
 * genereaza factura pdf format intern
 */
class PrintFacturaInternaPdf extends PrintFactura {
	var $inaltime_pozitie = 10;
	var $inaltime_nc8 = 5;
	var $inaltime_pagina = 140;
	function __construct($factura_id) {
		parent::__construct($factura_id);
	}
	
	function logo() {
		if(is_file(DOC_ROOT.PATH_LOGO_SOCIETATI.$this -> societate -> logo)) {
			$this -> pdf -> Image(DOC_ROOT.PATH_LOGO_SOCIETATI.$this -> societate -> logo, 1, 10);
		}
	}
	
	/**
	 * informatii societate
	 * @return 
	 */
	function infoSocietate() {
		
		$left = 60;
		$this -> text("Furnizor", 1, 6);
		$this -> text($this -> societate -> denumire, $left, 6, 14);
		$this -> text($this -> societate -> reg_com, $left, 11);
		$this -> text($this -> societate -> cod_fiscal, $left, 16, 12, 'B');
		$this -> text($this -> societate -> sediul.' '.$this -> societate -> tara, $left,21, 10);
		$scont = new SocietatiConturi("where societate_id = '". $this -> societate -> id ."'
		and valuta='". $this -> factura -> valuta ."'
		");
		
		if(count($scont)) {
			$this -> text($scont -> banca, $left, 26);
			$this -> text($scont -> iban, $left, 31,12, 'B');
		} else {
			$this -> text($this -> societate -> banca, $left, 26);
			$this -> text($this -> societate -> iban, $left, 31,12, 'B');
		}
		$this -> text('Capital Social: '.$this -> societate -> capital_social, $left,36, 10);
		$this -> text('GESTIUNE: '.$this -> factura -> gestiune -> denumire, $left,41, 10);
	}
	/**
	 * informatii client
	 * @return 
	 */
	function infoClient() {
		$left = 90;
		$this -> text("Client", $left, 48);
		if($this -> factura -> transfer_id > 0) {
			$transfer = new Transferuri($this -> factura -> transfer_id);
			$gestiune = new Gestiuni($transfer -> gestiune_destinatie_id);
			$societate = $gestiune -> societate;
			$this -> text($societate -> denumire, $left, 53, 12);
			$this -> text($societate -> reg_com, $left, 57);
			$this -> text($societate -> cod_fiscal, $left, 61, 10, 'N'); 
			if(strlen($societate -> sediul) < 60) {
				$this -> text($societate -> sediul, $left,65, 10);
				$plus = 0;
			}
		
			$this -> text($societate -> banca, $left, 69+$plus);
			$this -> text($societate -> iban, $left, 73+$plus);
			$this -> text("GESTIUNE: ".$gestiune -> denumire, $left, 77+$plus);
		} else {
			$this -> text($this -> tert -> denumire, $left, 53, 12);
			$this -> text($this -> tert -> reg_com, $left, 57);
			$this -> text($this -> tert -> cod_fiscal, $left, 61, 10, 'N'); 
			if(strlen($this -> tert -> sediul) < 60) {
				$this -> text($this -> tert -> sediul, $left,65, 10);
				$plus = 0;
			} else {
				$gasit = 0;
				$sediu = $this -> tert -> sediul;
				for($i=60; $i>0 && $gasit == 0; $i--) {
					if($sediu[$i] == " ") {
						$gasit = 1;
					}
				}
				$this -> text(substr($sediu, 0, $i), $left,65, 10);
				$this -> text(substr($sediu, $i+1), $left,69, 10);
				$plus = 4;
			}
			$this -> text($this -> tert -> banca, $left, 69+$plus);
			$this -> text($this -> tert -> iban, $left, 73+$plus);
		}


	}
	/**
	 * informatii factura (numar, data, scadenta)
	 * @param int $pagina - numar curent pagina
	 * @param int $din - nuumar total pagini
	 * @return 
	 */
	function infoDoc($pagina, $din) {
		
		$this -> pdf -> Rect(1, 45, 80, 38);	
		$this -> text("FACTURA FISCALA", 16, 50, 14, 'B');
		$this -> text("Serie / Numar", 3, 55);
		$this -> text($this -> factura -> serie -> serie, 35, 55, 12, 'B');
		$this -> text(str_pad($this -> factura -> numar_doc, $this -> factura -> serie -> completare_stanga, "0", STR_PAD_LEFT), 55, 55, 12, 'B');
		$this -> text("Data", 3, 61);
		$this -> text(c_data($this -> factura -> data_factura), 55, 61, 12, 'B');
		$this -> text("Scadenta", 3, 66);
		$this -> text(c_data($this -> factura -> data_scadenta), 55, 66);
		$this -> text("Agent", 3, 70);		
		$this -> text("Pagina $pagina / $din ", 30, 81);
	}
	
	/**
	 * detalii factura
	 * @return 
	 */
	function detaliiFactura() {
		$y = 88;
		if(BILL_AFISEZ_MONEDA) $this -> text("Moneda: LEI", 5, $y);
		if(BILL_AFISEZ_CURS) $this -> text("Curs Valutar ". $this -> factura -> curs_valutar  ."", 50, $y);
		if(BILL_AFISEZ_INCOTERM) $this -> text("Incoterms: ".$this -> factura -> incoterm, 110, $y);
		if(BILL_AFISEZ_COTA_TVA) $this -> text("Cota TVA:".$this -> factura -> cota_tva -> valoare, 160, $y);
	}
	/**
	 * cap tabel continut factura
	 * @return 
	 */
	function antetContinutFactura() {
		
		$y = 90;
		$h = 10;
		
		$this -> text("NrCrt", 2, $y+4, 11, 'B');
		$this -> text("Descriere Produs", 16, $y+4, 11, 'B');
		$this -> text("UM", 116, $y+4,11,'B');
		$this -> text("Cant", 131, $y+4,11,'B');
		$this -> text("Pret", 151, $y+4,11,'B');
		$this -> text("Valoare", 171, $y+4, 11,'B');		
		$this -> text("Val. TVA", 191, $y+4,11,'B');
		
		
				
		$this -> pdf -> Rect(  1, $y, 14,10); //nrcrt
		$this -> pdf -> Rect( 15, $y, 100,10); //denumire
		$this -> pdf -> Rect(115, $y, 15,10); //um
		$this -> pdf -> Rect(130, $y, 20,10); //cantitate
		$this -> pdf -> Rect(150, $y, 20,10); //pret
		$this -> pdf -> Rect(170, $y, 20,10); //valoare
		$this -> pdf -> Rect(190, $y, 19,10); //valoare tva
	}
	
	/**
	 * tabel conti nut factura
	 * @return 
	 */
	function continutFacturaModel() {
		$y=100;
		$this -> pdf -> Rect(  1, $y, 14, $this -> inaltime_pagina); //nrcrt
		$this -> pdf -> Rect( 15, $y, 100, $this -> inaltime_pagina); //denumire
		$this -> pdf -> Rect(115, $y, 15, $this -> inaltime_pagina); //um
		$this -> pdf -> Rect(130, $y, 20, $this -> inaltime_pagina); //cantitate
		$this -> pdf -> Rect(150, $y, 20, $this -> inaltime_pagina); //pret
		$this -> pdf -> Rect(170, $y, 20, $this -> inaltime_pagina); //valoare
		$this -> pdf -> Rect(190, $y, 19, $this -> inaltime_pagina); //valoare tva
	}
	
	/**
	 * afiseaza o pozite din articol
	 * @param object $nr_crt - numar pozitie articol
	 * @param FacturiContinut $cnt 
	 * @param object $last_y - coordonata
	 * @return $last_y = $last_y + inaltime_pozitie
	 */
	function articol($nr_crt, $cnt, $y) {
		$this -> text($nr_crt, 6, $y, 9, 'N');
		
		if(BILL_AFISEZ_COD_PRODUS) {
		$this -> text("Cod Articol:", 16, $y, 9, 'N');
		$this -> text($cnt -> cod_produs, 35, $y);
		$plus = 4;
		}
		$this -> text($cnt -> denumire, 16, $y+$plus, 9, 'N');
		
		$um = new UnitatiMasura($cnt -> unitate_masura_id);
		$this -> text($um -> denumire, 116, $y);
		
		//afisez cu writeHTMLCell pentru a putea alinia la dreapta
		$this -> pdf -> writeHTMLCell(19, 13, 131, $y - 4, '<p style="text-align:right;">'. $cnt -> cantitate .'</p>');
		$this -> pdf -> writeHTMLCell(19, 13, 151, $y - 4, '<p style="text-align:right;">'. $cnt -> pret_vanzare_ron .'</p>');
		$this -> pdf -> writeHTMLCell(19, 13, 171, $y - 4, '<p style="text-align:right;">'. $cnt -> val_vanzare_ron .'</p>');
		$this -> pdf -> writeHTMLCell(18, 13, 191, $y - 4, '<p style="text-align:right;">'. $cnt -> val_tva_ron .'</p>');
		return $y + $this -> inaltime_pozitie;
	}
	
	/**
	 * afiseaza cod vama
	 * @param string $cod - cod vamal
	 * @param int $last_y - coordonata y
	 * @return 
	 */
	function nc8($cod, $y) {
		$this -> text("Cod Vamal $cod", 50, $y);
		return $y + $this -> inaltime_nc8;
		
	}
	
	/**
	 * subsol factura - total, informatii expediere, semnatura
	 * @return 
	 */
	function subsolFactura() {
		$this -> expediere();
		$this -> total();
		$this -> semnatura();
	}
	
	function expediere() {
		$y = 248;
		$h = 25;
		$this -> pdf -> Rect(1, $y, 68, $h);
		$this -> text("Expediere prin", 2, $y+4);
		if($this -> factura -> delegat_id == -2) {
			$this -> text("Curier ", 2, $y+8);
			$this -> text("Numar AWB: ", 2, $y+12);
			$this -> pdf -> writeHTMLCell(65, 10, 2, $y+12, $this -> factura -> auto_numar);
		}
		
		if($this -> factura -> delegat_id == -3) {
			$this -> text($this -> factura -> transportator, 2, $y+8);
			$this -> text("Numar auto: ", 2, $y+12);
			$this -> pdf -> writeHTMLCell(65, 10, 2, $y+12, $this -> factura -> auto_numar);
		}
		
		if($this -> factura -> delegat_id == 0 || $this -> factura -> delegat_id == -1) {
			$this -> text("Nume", 2, $y+8);
			$this -> text("Cnp", 2, $y+12);
			$this -> text("Act", 2, $y+16);
			$this -> text("Auto", 2, $y+20);
		}
	}
	
	function total() {
		$y = 248;
		$h = 25;
		$this -> pdf -> Rect(141, $y, 68, $h);
		$this -> text("Total", 142, 255, 10, 'B');
		$total_fara_tva = number_format($this -> factura -> totalFaraTva(), 2, ',', '.');
		$total_tva = number_format($this -> factura -> totalTva(), 2, ',', '.');
		$total_factura = number_format($this -> factura -> totalFactura(), 2, ',', '.');
		$this -> pdf -> writeHTMLCell(18, 13, 171, 250, '<p style="text-align:right;">'. $total_fara_tva .'</p>');
		$this -> pdf -> writeHTMLCell(18, 13, 191, 250, '<p style="text-align:right;">'. $total_tva .'</p>');
		$this -> text("Total TVA inclus", 142, 266, 10, 'B');
		$this -> pdf -> SetFontSize(14);
		$this -> pdf -> writeHTMLCell(36, 13, 174, 260, '<p style="text-align:center;">'. $total_factura .'</p>');
	}
	
	function semnatura() {
		$y = 248;
		$h = 25;
		$this -> pdf -> Rect(70, $y, 70, $h);
		if($this -> factura -> delegat_id == -2 || $this -> factura -> delegat_id == -3) {
			$this -> text("Adresa de livrare", 71, $y+4);
			if($this -> factura -> adresa_livrare)
				$this -> pdf -> writeHTMLCell(70, 30, 71, $y+4, $this -> factura -> adresa_livrare);
			else {
				$this -> pdf -> writeHTMLCell(70, 30, 71, $y+4, $this -> tert -> sediul);
			}
		} else {
			$this -> text("Sematura de primire", 71, $y+4);
		}
	}
	
	/**
	 * genereaza array continut facura
	 * @return array 
	 */
	function continutFactura() {
		
	
		$continut = new FacturiContinut();
		$continut -> fromString(" where factura_id = '". $this -> factura -> id ."' 
		group by denumire, unitate_masura_id, pret_vanzare_ron, produs_id",
			array(
			"denumire", 
			"cod_produs",
			"unitate_masura_id", 
			"sum(cantitate) as cantitate", 
			"pret_vanzare_ron", 
			"sum(val_vanzare_ron) as val_vanzare_ron",
			"sum(val_tva_ron) as val_tva_ron",
			)
		);
		
		$nc8 = -1;
		$contor = 0;
		$contor_inaltime = 0;
		$contor_pagini = 0;
		$last_y = 104;
		$contor_art_pag = 0;
		$pages = array();
		$nr_crt = 1;
		
		foreach($continut as $cnt) {
			$contor++;
			/* NU AFISEZ GRUPAT PE CODURI VAMALE
			if($cnt -> nc8 != $nc8) {
				$pages[$contor_pagini][] = array($cnt -> nc8, "nc8");
				$contor_inaltime += $this -> inaltime_nc8;
				$nc8 = $cnt -> nc8;
			}
			*/
			
			$pages[$contor_pagini][] = array($cnt, "articol", $nr_crt);
			$nr_crt++;
			$contor_inaltime += $this -> inaltime_pozitie;
			
			//daca am terminat o pagina sau am terminat articolele trec la urmatoarea pagina
			if((($this -> inaltime_pagina - $contor_inaltime) < $this -> inaltime_pozitie) || $contor == $this -> numar_pozitii) {
				$contor_inaltime = 0;
				$contor_pagini++;
			}
		}
		
		return $pages;
	}
	
	function intocmit() {
		$x = 10;
		$y = 244;
		$user = new Utilizatori($this -> factura -> utilizator_id);
		$this -> text("Document intocmit de ".$user -> nume ."       CNP: ". $user -> cnp, $x, $y, 9);
	}
	
	/**
	 * conditii de livrare
	 * @return 
	 */
	function conditii() {
		$html = '<div style="text-align:justify; font-size:18px;">'. $this -> gestiune -> conditii_vanzare .'</div>';
		$this -> pdf -> writeHTMLCell(208,20,1,275,$html,1);
	}
	
	/**
	 * sablon
	 * @param int $pagina - numar curent pagina
	 * @param int $din - numar total pagini
	 * @return 
	 */
	function sablonPagina($pagina, $din){
		$this -> pdf -> AddPage();	
		$this -> logo();
		$this -> infoSocietate();
		$this -> infoClient();
		$this -> infoDoc($pagina, $din);
		$this -> detaliiFactura();
		$this -> antetContinutFactura();
		$this -> continutFacturaModel();
		$this -> subsolFactura();
		$this -> intocmit();
		if(BILL_AFISEZ_CONDITII_VANZARE) $this -> conditii();
	}
	
	/**
	 * genereaza pdf-ul
	 * @return 
	 */
	function buildPdf() {
		$this -> pdfProperties();
		$pages = $this -> continutFactura();
		$nr_pagini = count($pages);
		$pagina = 1;
		foreach($pages as $page) {
			$last_y = 104;
			$this -> sablonPagina($pagina, $nr_pagini);
			foreach($page as $pag) {
				if($pag[1] == "articol") {
					$last_y = $this -> articol($pag[2], $pag[0], $last_y);
				} else {
					$last_y = $this -> nc8($pag[0], $last_y);
				}
			}
			$pagina++;
		}
	}
	
	function getPdf() {
		$this -> buildPdf();
		parent::getPdf('factura.pdf');
	}	
}
?>