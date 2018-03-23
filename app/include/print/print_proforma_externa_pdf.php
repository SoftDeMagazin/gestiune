<?php
define("PDF_FONT", 'helvetica');
/**
 * genereaza factura pdf format extern
 */
class PrintFacturaExternaPdf extends PrintFactura {
	var $inaltime_pozitie = 10;
	var $inaltime_nc8 = 5;
	var $inaltime_pagina = 140;
	function __construct($factura_id) {
		parent::__construct($factura_id);
	}

	function logo() {
		$this -> pdf -> Image(DOC_ROOT.PATH_LOGO_SOCIETATI.$this -> societate -> logo, 1, 10);
	}
	
	/**
	 * informatii societate
	 * @return 
	 */
	function infoSocietate() {
		
		$left = 60;
		$this -> text("Furnizor / Fornitore", 1, 6);
		$this -> text($this -> societate -> denumire, $left, 6, 14);
		$this -> text($this -> societate -> reg_com, $left, 11);
		$this -> text($this -> societate -> cod_fiscal, $left, 16, 12, 'B');
		$this -> text($this -> societate -> sediul.' '.$this -> societate -> tara, $left,21, 10);
		$this -> text($this -> societate -> banca_valuta, $left, 26);
		$this -> text($this -> societate -> iban_valuta, $left, 31,12, 'B');
		$this -> text('Capital Social: '.$this -> societate -> capital_social, $left,36, 10);
		$this -> text($this -> societate -> website, $left,41, 10);
	}
	/**
	 * informatii client
	 * @return 
	 */
	function infoClient() {
		$left = 90;
		$this -> text("Client / Cliente", $left, 48);
		$this -> text($this -> tert -> denumire, $left, 53, 12);
		$this -> text($this -> tert -> reg_com, $left, 57);
		$this -> text($this -> tert -> cod_fiscal, $left, 61, 10, 'N'); 
		$this -> text($this -> tert -> sediul.' '.$this -> tert -> cod_tara, $left,65, 10);
		$this -> text($this -> tert -> banca, $left, 69);
		$this -> text($this -> tert -> iban, $left, 73);

	}
	/**
	 * informatii factura (numar, data, scadenta)
	 * @param int $pagina - numar curent pagina
	 * @param int $din - nuumar total pagini
	 * @return 
	 */
	function infoDoc($pagina, $din) {
		
		$this -> pdf -> Rect(1, 45, 80, 38);	
		$this -> text("FACTURA /", 16, 50, 14, 'B');
		$this -> text("FATTURA", 44, 50, 14, 'I');	
		$this -> text("Numar /", 3, 55);
		$this -> text("Numero", 16, 55, 10,'I');
		$this -> text(str_pad($this -> factura -> numar_doc, 9, "0", STR_PAD_LEFT), 55, 55, 12, 'B');
		$this -> text("Data / Data", 3, 61);
		$this -> text(c_data($this -> factura -> data_factura), 55, 61, 12, 'B');
		$this -> text("Scadenta / Scadenza", 3, 66);
		$this -> text(c_data($this -> factura -> data_scadenta), 55, 66);
		$this -> text("Agent / Agente", 3, 70);
		
		$this -> text("Pagina $pagina / $din ", 30, 81);
	}
	
	/**
	 * detalii factura
	 * @return 
	 */
	function detaliiFactura() {
		$y = 88;
		$this -> text("Moneda / Divisa : ". $this -> factura -> valuta, 5, $y);
		$this -> text("Swift: ". $this -> societate -> swift, 70, $y);
		$this -> text("Incoterms: ".$this -> factura -> incoterm, 150, $y);
	}
	/**
	 * cap tabel continut factura
	 * @return 
	 */
	function antetContinutFactura() {
		
		$y = 90;
		$this -> text("NrCrt", 2, $y+4, 10, 'B');
		$this -> text("Pos", 7, $y+9, 10, 'I');
		
		$this -> text("Descriere Produs", 16, $y+4, 10, 'B');
		$this -> text("Descrizione Materiale", 76, $y+9, 10, 'I');
		
		$this -> text("UM", 116, $y+4,10,'B');
		$this -> text("UM", 122, $y+9,10,'I');
		
		$this -> text("Cant", 131, $y+4,10,'B');
		$this -> text("Qta", 142, $y+9,10,'I');
		
		$this -> text("Pret", 151, $y+4,10,'B');
		$this -> text("Prezzo", 167, $y+9,10,'I');
		
		$this -> text("Valoare", 181, $y+4,10,'B');
		$this -> text("Importo", 195, $y+9,10,'I');
				
		$this -> pdf -> Rect(  1, $y, 14,10); //nrcrt
		$this -> pdf -> Rect( 15, $y, 100,10); //denumire
		$this -> pdf -> Rect(115, $y, 15,10); //um
		$this -> pdf -> Rect(130, $y, 20,10); //cantitate
		$this -> pdf -> Rect(150, $y, 30,10); //pret
		$this -> pdf -> Rect(180, $y, 29,10); //valoare
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
		$this -> pdf -> Rect(150, $y, 30, $this -> inaltime_pagina); //pret
		$this -> pdf -> Rect(180, $y, 29, $this -> inaltime_pagina); //valoare
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
		
		$this -> text("Cod Articol:", 16, $y, 9, 'B');
		$this -> text($cnt -> cod_produs, 35, $y);
		$this -> text($cnt -> denumire, 16, $y+4, 9, 'N');
		
		$um = new UnitatiMasura($cnt -> unitate_masura_id);
		$this -> text($um -> denumire, 116, $y);
		
		//afisez cu writeHTMLCell pentru a putea alinia la dreapta
		$this -> pdf -> writeHTMLCell(19, 13, 131, $y - 4, '<p style="text-align:right;">'. $cnt -> cantitate .'</p>');
		$this -> pdf -> writeHTMLCell(29, 13, 151, $y - 4, '<p style="text-align:right;">'. $cnt -> pret_vanzare_val .'</p>');
		$this -> pdf -> writeHTMLCell(28, 13, 181, $y - 4, '<p style="text-align:right;">'. $cnt -> val_vanzare_val .'</p>');
		return $y + $this -> inaltime_pozitie;
	}
	
	/**
	 * afiseaza cod vamal
	 * @param string $cod - cod vamal
	 * @param int $last_y - coordonata y
	 * @return 
	 */
	function nc8($cod, $y) {
		$this -> text("Cod Vamal / Tariffa doganale: $cod", 35, $y, 9, 'NU');
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
		$this -> text("Expediere prin / ", 2, $y+4, 10, 'N');
		$this -> text("Spedizione a cura di", 28, $y+4, 10, 'I');
		if($this -> factura -> delegat_id == -2) {
			$this -> text("Curier ", 2, $y+8);
			$this -> text("Numar AWB: ", 2, $y+12);
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
		$total_factura = number_format($this -> factura -> totalFacturaValuta(), 2, ',', '.');
		$this -> text("Total / Totale", 142, 266, 10, 'B');
		$this -> pdf -> SetFontSize(14);
		$this -> pdf -> writeHTMLCell(36, 13, 174, 260, '<p style="text-align:center;">'. $total_factura .'</p>');
	}
	
	function semnatura() {
		$y = 248;
		$h = 25;
		$this -> pdf -> Rect(70, $y, 70, $h);
	}
	
	/**
	 * genereaza array continut facura
	 * @return array 
	 */
	function continutFactura() {
		
	
		$continut = new FacturiContinut(" where factura_id = ". $this -> factura -> id ." order by nc8");
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
			if($cnt -> nc8 != $nc8) {
				$pages[$contor_pagini][] = array($cnt -> nc8, "nc8");
				$contor_inaltime += $this -> inaltime_nc8;
				$nc8 = $cnt -> nc8;
			}
			
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
		$this -> text("Scutit cu drept de deducere in conformitate cu prevederile Codului Fiscal / Exempted with right of deduction according to Fiscal Code", 
						10, 244, 9);
	}
	
	/**
	 * conditii de livrare
	 * @return 
	 */
	function conditii() {
		$html = '<div style="text-align:justify; font-size:18px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
		Pellentesque id lobortis lorem. Nulla magna eros, aliquet at venenatis ac,
		auctor quis nisl. Maecenas lacinia, sem nec convallis condimentum, 
		ante odio pharetra magna, vel fringilla urna orci vel justo. 
		In consectetur lobortis nisi non interdum. Morbi eleifend, lorem nec pharetra ultricies,
		libero tortor imperdiet sapien,
		sit amet volutpat dui mi quis magna. Integer sed metus arcu. Vestibulum ante ipsum primis 
		in faucibus orci luctus et ultrices posuere cubilia Curae; Donec magna libero, 
		blandit in laoreet in, condimentum eget lectus. 
		Nulla gravida, nisi eu ultrices tincidunt,
		urna tortor blandit turpis, et vulputate massa purus ac purus. 
		Ut quam est, elementum ut malesuada nec, laoreet eu nisi. 
		Pellentesque habitant morbi tristique senectus et netus et 
		malesuada fames ac turpis egestas. Donec tellus mauris, 
		molestie ut molestie ac, eleifend at felis. 
		Sed interdum vulputate odio quis convallis. 
		Duis convallis libero ac risus ullamcorper lobortis. Donec placerat lectus sed sem sodales eu fringilla augue imperdiet. 
		Vivamus ac sapien sed mi sagittis consectetur</div>';
		
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
		$this -> conditii();
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
	
	/**
	 * salveaza pdf ca fiser pe server
	 * @param string $file - nume fiser
	 * @return 
	 */	
	function savePdf($file) {
		$this -> buildPdf();
		$this -> pdf -> Output($file, 'F');
	}
	
	
}
?>