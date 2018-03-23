<?php

require_once("common.php");
$xajax->processRequest();


function selectProdus($produs_id) {
	global $db;
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$objResponse = new xajaxResponse();
	if($produs_id) {
		$produs = new Produse($produs_id);
		$objResponse -> assign("div_info_produs", "innerHTML", ''.$produs -> denumire.''.$produs -> produs_id().'');
		$objResponse -> assign("info_um", "innerHTML", $produs -> unitate_masura -> denumire);
		$objResponse -> assign("info_tip", "innerHTML", $produs -> tip -> descriere);
	} else {
		$objResponse -> assign("div_info_produs", "innerHTML", '&nbsp;');
		$objResponse -> assign("info_um", "innerHTML", '&nbsp;');
	}
	$pg = new ProduseGestiuni("where produs_id = '$produs_id'");
	
	$neasoc = new Gestiuni("where gestiune_id not in 
	  (select gestiune_id from gestiuni 
	   where gestiune_id ". $db -> inArray($produs -> getGestiuniAsociate()) .")
	 ");
	if(count($pg)) { 
		$objResponse -> assign("div_lista_asocieri", "innerHTML", $pg -> lista("", "xajax_modificaPret('<%produs_gestiune_id%>');"));	
	} else {
		$objResponse -> assign("div_lista_asocieri", "innerHTML", "Produsul nu este asociat in nici o gestiune");
	}
	$objResponse -> assign("div_asociaza", "innerHTML", "Gestiune: <br>".$neasoc -> selectMulti());	
	
	$objResponse -> append("div_asociaza", "innerHTML", '
	<br>Pret Vanzare LEI<br>
	<input type="text" id="pret_ron" name="pret_ron">
	<br>Pret Vanzare EUR<br>
	<input type="text" id="pret_val" name="pret_val">
	');
	
	$objResponse -> append("div_asociaza", "innerHTML", ''.$produs -> produs_id().'');
	
	$objResponse -> script("$('#gestiune_id').multiSelect()");
	$curs = new Cursuri();
	$curs -> getLast();
	if(count($curs)) {
		$curs_val = $curs -> valoare;
	} else {
		$curs_val = 1;
	}
	
	$objResponse -> script("
	$('#pret_ron').change(
		function() {
			var valuta = $(this).val() / ".$curs_val.";
			$('#pret_val').val(valuta.toFixed(2));
		}
	);
	$('#pret_val').change(
		function() {
			var valuta = $(this).val() * ".$curs_val.";
			$('#pret_ron').val(valuta.toFixed(2));
		}
	);	
	");
	
	$objResponse -> script("
	$('#pret').change(
		function() {
			var valuta = $(this).val() / ".$curs_val.";
			$('#pret_eur').val(valuta.toFixed(2));
		}
	);
	$('#pret_eur').change(
		function() {
			var valuta = $(this).val() * ".$curs_val.";
			$('#pret').val(valuta.toFixed(2));
		}
	);	
	");
	
	return $objResponse;
}

function filtruProduse($filtru) {
	if($filtru) {
		$produse = new Produse("where denumire like '$filtru%' order by denumire asc");
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select(35));
		return $objResponse; 
	}
	else {
		$produse = new Produse("where 1 order by denumire asc");
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select(35));
		return $objResponse; 
	}		
}


function asociazaProdus($frm) {
	$produs = new Produse($frm['produs_id']);
	
	if(!$frm['gestiune_id']) return alert('Selectati cel putin o gestiune!');
	
	if(!$frm['pret_ron'] && ($produs -> tip_produs == "marfa" || $produs -> tip_produs == "reteta")) {
		return alert("Nu ati adaugat pret de vanzare!");	
	}
	
	$produs -> asociazaCuGestiuni($frm['gestiune_id'], array("pret_ron" => $frm['pret_ron'], "pret_val" => $frm['pret_val']));
	
	$categorie = new Categorii($produs -> categorie_id);
	$categorie -> asociazaCuGestiuni($frm['gestiune_id']);
	
	$objResponse = selectProdus($produs -> id);
	return $objResponse;
	
}

function modificaPret($id) {
	$dialog = new Dialog(800, 300, '', "win_modifica_pret");
	$pg = new ProduseGestiuni($id);
	$dialog -> title = $pg -> gestiune -> denumire.' - '. $pg -> produs -> denumire;
	$dialog -> append($pg -> frmDefault());
	
	$dialog -> addButton("Salveaza", "xajax_savePret(xajax.getFormValues('frm_produse_gestiuni'));<%close%>");
	$dialog -> addButton("Renunta");
	
		$curs = new Cursuri();
	$curs -> getLast();
	if(count($curs)) {
		$curs_val = $curs -> valoare;
	} else {
		$curs_val = 1;
	}
	$objResponse = $dialog -> open();
	$objResponse -> script("
	$('#frm_produse_gestiuni #pret_ron').change(
		function() {
			var valuta = $(this).val() / ".$curs_val.";
			$('#frm_produse_gestiuni #pret_val').val(valuta.toFixed(2));
		}
	);
	$('#frm_produse_gestiuni #pret_val').change(
		function() {
			var valuta = $(this).val() * ".$curs_val.";
			$('#frm_produse_gestiuni #pret_ron').val(valuta.toFixed(2));
		}
	);	
	");
	
	return $objResponse;
}

function salveazaPretGestiuni($produs_id, $pret_ron, $pret_val) {
	$pgs = new ProduseGestiuni("where produs_id = '$produs_id'");
	foreach($pgs as $pg) {
		$pg -> pret_ron = $pret_ron;
		$pg -> pret_val = $pret_val;
		$pg -> save();
	}
	return selectProdus($produs_id);
}

function savePret($frm) {
	$pg = new ProduseGestiuni($frm);
	$pg -> save();
	return selectProdus($pg -> produs_id);
}

function lista_produse($filtre) {
	global $db;
	$objResponse = new xajaxResponse();
	if(!$filtre['gestiune_destinatie_id']) {
		return alert("Selectati gestiunea destinatie");
	}
	
	if(!$filtre['gestiune_sursa_id']) {
		return alert("Selectati gestiunea sursa");
	}
	
	$sql = "
	where gestiune_id = '". $filtre['gestiune_sursa_id'] ."'
	";
	if($filtre['categorie_id']) {
		$sql .= " and categorie_id ". $db -> inArray($filtre['categorie_id']) ."";
	} else {
		$objResponse -> assign("lista_produse", "innerHTML", "");
		return $objResponse;
	}
	
	$sql .= "
	and produs_id not in (
		select produs_id from produse_gestiuni where gestiune_id = '". $filtre['gestiune_destinatie_id'] ."'
	)
	";
	$produse = new ViewProduseGestiuni($sql);
	
	$objResponse -> assign("lista_produse", "innerHTML", $produse -> listaImport());
	return $objResponse;
}

function importa_produse($filtre, $produse) {
	$prods = $produse['chk_produs'];
	
	if(!$filtre['gestiune_destinatie_id']) {
		return alert("Selectati gestiunea destinatie");
	}
	
	if(!$filtre['gestiune_sursa_id']) {
		return alert("Selectati gestiunea sursa");
	}
	$i = 0;
	foreach($prods as $prod) {
		$produs = new Produse($prod);

		$pret_ron = $produse['pret_ron'][$prod];
		
		$produs -> asociazaCuGestiuni(array($filtre['gestiune_destinatie_id']), array("pret_ron" => $pret_ron));
	}
	
	
	$objResponse = alert('Produsele au fost asociate!');
	copyResponse($objResponse, lista_produse($filtre));
	return $objResponse;
}
?>