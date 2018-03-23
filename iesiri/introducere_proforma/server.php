<?php
require_once("common.php");
$xajax->processRequest();

function load($factura_id=NULL) {
	if($factura_id) {
		$factura = new FacturiProforme($factura_id);
		$factura -> data_factura = c_data($factura -> data_factura);
		$factura -> data_scadenta = c_data($factura -> data_scadenta);
	}
	else {
		$factura = new FacturiProforme();
	}
	$objResponse = new xajaxResponse();
	if($factura_id) {
		$objResponse -> assign("div_frm_factura", "innerHTML", $factura -> frmFactura($factura -> tert_id));
		$objResponse -> append("div_frm_factura", "innerHTML", '
	    <div>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi_proforme\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>
    </div>
	');
		$objResponse -> assign("txtCautareFurnizor", "value", $factura -> tert -> denumire);
		copyResponse($objResponse, lista($factura -> id));
	}	
	
	copyResponse($objResponse, frmComponenta(0));
	copyResponse($objResponse, initControl());
	copyResponse($objResponse, filtruProduse(''));
	copyResponse($objResponse, switchTab("antet"));
	return $objResponse;
}

function frmClient() {
	$client = new Terti();
	$objResponse = new xajaxResponse();
	$dialog = new Dialog(800, 600, '', 'win_add_client');
	$dialog -> title = "Adauga client";
	$dialog -> append($client -> frmDefault());
	$dialog -> addButton("Salveaza", "xajax_salveazaClient(xajax.getFormValues('frm_terti'))");
	$dialog -> addButton("Renunta");
	$objResponse = openDialog($dialog);
	$objResponse -> script("\$('#denumire').focus();");
	return $objResponse;
}

function salveazaClient($frmValues) {
	$model = new Terti($frmValues);
	$objResponse = new xajaxResponse();
	if(!$model -> validate($objResponse)) {
		return $objResponse;
	}
	$model -> save();
	$objResponse = selectClient($model -> id);
	$objResponse -> script("\$('#win_add_client').dialog('close');");
	return $objResponse;
}


function salveazaAntet($frmValues)
{
	$frmValues['gestiune_id'] = $_SESSION['user'] -> gestiune_id;
	$factura = new FacturiProforme($frmValues);
	$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
	$objResponse = new xajaxResponse();
	if(!$factura -> validate($objResponse)) {
		return $objResponse;
	}
	
	if(!$factura -> id) {
		$factura -> data_factura = data_c($factura -> data_factura);
		$factura -> data_scadenta = data_c($factura -> data_scadenta);
		$factura -> data_introducere = data();
		$factura -> numar_doc = $factura -> getNumar($_SESSION['user'] -> gestiune_id);
		
		$serie = $factura -> getSerie($_SESSION['user'] -> gestiune_id);
		$factura -> serie_id = $serie -> id;
		$factura -> incrementSerie($_SESSION['user'] -> gestiune_id);
		
		$factura -> utilizator_id = $_SESSION['user'] -> user_id;
		
	}
	else {
		$factura -> data_factura = data_c($factura -> data_factura);
		$factura -> data_scadenta = data_c($factura -> data_scadenta);
	}
	
	
	$factura -> societate_id = $gestiune -> societate_id;
	$factura -> save();
	
	$factura -> data_factura = c_data($factura -> data_factura);
		
	$objResponse -> assign("div_frm_factura", "innerHTML", $factura -> frmFactura($factura -> tert_id));
	$objResponse -> append("div_frm_factura", "innerHTML", '
	    <div>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi_proforme\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>
    </div>
	');
	copyResponse($objResponse, switchTab('frm'));
	copyResponse($objResponse, frmComponenta(0));
	$objResponse -> script("\$('#cautare_produs').focus().select();");
	return $objResponse;
}

function filtruProduse($filtru) {
	if($filtru) {
		$sql = "inner join produse_gestiuni using(produs_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id."' and produse.denumire like '$filtru%' order by produse.denumire asc";
		$produse = new Produse($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}
	else {
		$objResponse = new xajaxResponse();
		return $objResponse;
	}		
}


function selectProdus($produs_id) {
	$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("produs_id", "value", $produs -> id);
	$objResponse -> assign("div_info_produs", "innerHTML", $produs -> denumire);
	if($produs -> pret_val) $pret = $produs -> pret_val;
	else $pret = "0";
	
	$stoc = new Stocuri("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	if(count($stoc)) {
		$stoc_cant = $stoc -> stoc;
		$stoc_val = $stoc -> valoare_stoc_ron;
		$pmp = douazecimale($stoc_val/$stoc_cant);
	}
	else {
		$stoc_cant = 0;
		$stoc_val = 0;
		$pmp = 0;
	}
	
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and cantitate_ramasa <> 0");
	
	$objResponse -> assign("pret_vanzare_val", "value", $pret);
	$objResponse -> assign('div_detalii_produs', 'innerHTML', '
	<strong>Ambalare:</strong>'. $produs -> ambalare .'</br> 
	<strong>Stoc:</strong> <a href="#" onClick="xajax_infoLoturi(\''. $produs_id .'\'); return false;">'. $stoc_cant .'</a><br />
	'. $loturi -> infoLoturi() .'
	');
	$objResponse -> assign("div_frm_unitate_masura", "innerHTML", $produs -> unitate_masura());
	$objResponse -> script("\$('#cantitate').focus().select();");
	$objResponse -> script("xajax_calculator(xajax.getFormValues('frm_facturi_continut'), 
					$('#factura_id').val(), 
					'pret_vanzare_val');");
	return $objResponse;
}

function infoLoturi($produs_id) {
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and cantitate_ramasa <> 0");
	$produs = new Produse($produs_id);
	if(count($loturi)) {
		$dialog = new Dialog(600, 400, "", "win_info_loturi");
		$dialog -> title = "Loturi: ".$produs -> denumire;
		$dialog -> append($loturi -> lista());
		$objResponse = $dialog -> open();
		$objResponse -> script("$('#win_info_loturi .tablesorter').tablesorter()");
		return $objResponse;
	}
	else return alert('Nu sunt loturi inregistrate pentru acest produs');
}

function frmProdus($produs_id=0) {
	$produs = new Produse($produs_id);
	$dialog = new Dialog(800,600, "", "win_frm_produs");
	$dialog -> append($produs -> frmDefault());
	$dialog -> title = "Adaugare/Editare produs";
	$dialog -> addButton("Salveaza", "xajax_salveazaProdus(xajax.getFormValues('frm_produse'))");
	$dialog -> addButton("Renunta");
	$objResponse = openDialog($dialog);
	$objResponse -> script("\$('#denumire').focus().select();");
	return $objResponse;	
}

function salveazaProdus($frmValues) {
	$produs = new Produse($frmValues);
	$objResponse = new xajaxResponse();
	if(!$produs -> validate($objResponse)) {
		return $objResponse;
	}
	$produs -> save();
	$objResponse = selectProdus($produs -> id);
	$objResponse -> script("\$('#win_frm_produs').dialog('close')");
	return $objResponse;
}

function filtruClient($filtru) {
	$clienti = new Terti();
	$objResponse = new xajaxResponse();
	if($clienti -> cautare($filtru)) {
		$objResponse -> assign("div_filtru_furnizori", "innerHTML", $clienti -> selectMultiple());
	}
	$objResponse -> script("
		$('#sel_furnizor').keyup(
			function(event) {
				if(event.keyCode == 13) {
					xajax_selectClient(this.options[this.selectedIndex].value);
					$('#div_filtru_furnizori').hide();
					$('#numar_doc').focus().select();
				}
			}
		);
		$('#sel_furnizor').dblclick(
			function(event) {
					xajax_selectClient(this.options[this.selectedIndex].value);
					$('#div_filtru_furnizori').hide();
					$('#numar_doc').focus().select();
			}
		);
	");
	return $objResponse;
}

function selectClient($tert_id) {
	$client = new Terti($tert_id);
	$factura = new FacturiProforme();
	$factura -> tert_id = $tert_id;
	$factura -> data_factura = c_data(data());
	$factura -> data_scadenta = c_data(data());
	$curs = new Cursuri();
	$curs -> getLast();
	$factura -> curs_valutar = $curs -> valoare;
	$factura -> numar_doc = $factura -> getNumar();
	$objResponse = new xajaxResponse();
	if($client -> tip == "intern") {
	}
	else {
		$cota_tva = new CoteTva();
		$cota_tva -> getTvaZero();
		$factura -> cota_tva_id = $cota_tva -> id;	
	}
	
	

	

	
	$objResponse -> assign("tert_id", "value", $client -> id);
	$objResponse -> assign("txtCautareFurnizor", "value", $client -> denumire);
	$objResponse -> assign("div_frm_factura", "innerHTML", $factura -> frmFactura($tert_id));
	$objResponse -> append("div_frm_factura", "innerHTML", '
	    <div>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi_proforme\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>
    </div>
	');
	$objResponse -> assign("div_filtru_furnizori", "innerHTML", "");
	copyResponse($objResponse, initControl());
	return $objResponse;
}


function selectDelegat($delegat_id, $tert_id=0) {
	$objResponse = new xajaxResponse();
	if($delegat_id != -1) $delegat = new Delegati($delegat_id);
	else {
		$objResponse -> assign("frm_delegat", "innerHTML", '<input type="hidden" id="delegat_id" name="delegat_id" value="-1">');
		return $objResponse;
	}
	$delegat -> tert_id = $tert_id;
	
	$objResponse -> assign("frm_delegat", "innerHTML", $delegat -> frmContent());
	return $objResponse;
}

function frmComponenta($continut_id=NULL, $factura_id=NULL) {
	$continut = new FacturiProformeContinut($continut_id);
	$objResponse = new xajaxResponse();
	
	$objResponse -> assign("div_frm_continut", "innerHTML", $continut -> frmFacturaContinut());
	$objResponse -> script($continut -> scriptFactura());
	
	if(!$continut_id) $objResponse -> assign("div_info_produs", "innerHTML", "&nbsp;");
	else $objResponse -> assign("div_info_produs", "innerHTML", $continut -> produs -> denumire);
	$objResponse -> script("\$('#cautare_produs').focus().select();");
	copyResponse($objResponse, switchTab('frm'));
	return $objResponse;	
}

function salveazaComponenta($frmValues, $factura_id) {
	if(!$factura_id) {
		return alert('Introduceti si salvat antet factura!');
	}
	
	if(!$frmValues['produs_id']) {
		return alert('Selectati produsul pe care se face intrarea!');
	}
	
	$continut = new FacturiProformeContinut($frmValues);
	$continut -> factura_id = $factura_id;
	
	$produs = new ViewProduseGestiuni(" where produs_id = '". $frmValues['produs_id'] ."' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	
	$continut -> denumire = $produs -> denumire;
	$continut -> cod_produs = $produs -> cod_produs;
	$continut -> cod_bare = $produs -> cod_bare;
	$continut -> nc8 = $produs -> nc8;
	
	$continut -> save();
	
	$objResponse = lista($factura_id);
	copyResponse($objResponse, frmComponenta(0,$factura_id));
	return $objResponse;
}

function stergeComponenta($continut_id) {
	$continut = new FacturiProformeContinut($continut_id);
	$factura_id = $continut -> factura_id;
	$continut -> delete();
	$objResponse = lista($factura_id);
	copyResponse($objResponse, frmComponenta());
	return $objResponse;
}

function lista($factura_id)
{
	$factura = new FacturiProforme($factura_id);
	$objResponse = new xajaxResponse();
	$continutFactura = $factura -> continut -> lista("", "xajax_frmComponenta('<%continut_id%>');");
	$objResponse -> assign("grid", "innerHTML", $continutFactura);
	$objResponse -> assign("div_preview_factura", "innerHTML", $continutFactura);
	
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	
	$objResponse -> assign("txt_total", "value", $factura -> totalFaraTva());
	$objResponse -> assign("txt_total_tva", "value", $factura -> totalTva());
	$objResponse -> assign("txt_total_factura", "value", $factura -> totalFactura());
	
	$objResponse -> assign("txt_total_val", "value", $factura -> totalFaraTvaValuta());
	$objResponse -> assign("txt_total_tva_val", "value", $factura -> totalTvaValuta());
	$objResponse -> assign("txt_total_factura_val", "value", $factura -> totalFacturaValuta());
	return $objResponse;
}

function inchideFactura($frmValues) {
	$factura = new FacturiProforme($frmValues['factura_id']);
	$dialog = new Dialog(800, 600, "", "win_inchide_factura");
	$dialog -> title = "Salvare Factura";
	$dialog -> append($factura -> sumar());
	$dialog -> append(
	'<fieldset>
	<legend>Continut factura</legend>
	<div style="height:300px;overflow:scroll; overflow-x:hidden;">
	'. $factura -> continut -> lista() .'
	</div>
	</fieldset>
	'
	);
	$dialog -> addButton("Salveaza Proforma", "xajax_salveazaFactura(". $factura -> id .");<%close%>");
	$dialog -> addButton("Anuleaza Proforma", "xajax_anuleazaFactura(". $factura -> id .");<%close%>");
	$dialog -> addButton("Continua Editare", "<%close%>");
	$objResponse = openDialog($dialog);
	return $objResponse;
}


function salveazaFactura($factura_id) {
	$factura = new FacturiProforme($factura_id);
	$factura -> salvat = 'DA';
	$factura -> save();
	$objResponse = new xajaxResponse();
	$dialog = new Dialog(800,600, "", "win_proforma");
	$dialog -> append("Factura proforma a fost salvata");
	
	$dialog -> addButton("Tiparire Proforma", "xajax_xPrintProforma('$factura_id')");
	$dialog -> addButton("Evidenta Proforme", "xajax_location('".DOC_ROOT."iesiri/evidenta_proforme/');");
	$dialog -> addButton("Adauga Proforma", "xajax_location('".DOC_ROOT."iesiri/introducere_proforma/');");
	return $dialog -> open();
}

function anuleazaFactura($factura_id) {
	$factura = new FacturiProforme($factura_id); 
	$factura -> anuleaza();
	$objResponse = new xajaxResponse();
	$objResponse -> script("xajax_location('".DOC_ROOT."iesiri/evidenta_proforme/')");
	return $objResponse;
}

function calculator($frm, $factura_id, $mod="pret_ach_ron") {
	
	$factura = new FacturiProforme($factura_id);
	$cota_tva = $factura -> cota_tva;
	$objResponse = new xajaxResponse();
	$response = array();
	
	switch($mod) {
		case "cantitate": {
			$response['pret_vanzare_ron'] = $frm['pret_vanzare_val'] * $factura -> curs_valutar;
			$response['val_vanzare_ron']  = $frm['cantitate'] * $response['pret_vanzare_ron'];
			$response['val_vanzare_val']  = $frm['cantitate'] * $frm['pret_vanzare_val'];
			
			$response['pret_ron_cu_tva'] = ($response['pret_vanzare_ron'] * (100 + $cota_tva -> valoare)) / 100;
			$response['pret_val_cu_tva'] = ($frm['pret_vanzare_val'] * (100 + $cota_tva -> valoare)) / 100;
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
						
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
		
		case "pret_vanzare_ron": {
			$response['pret_vanzare_ron'] = $frm['pret_vanzare_ron'];
			$response['pret_vanzare_val'] = $frm['pret_vanzare_ron'] / $factura -> curs_valutar;
			
			$response['val_vanzare_ron']  = $frm['cantitate'] * $frm['pret_vanzare_ron'];
			$response['val_vanzare_val']  = $frm['cantitate'] * $response['pret_vanzare_val'];
			
			$response['pret_ron_cu_tva'] = ($response['pret_vanzare_ron'] * (100 + $cota_tva -> valoare)) / 100;
			$response['pret_val_cu_tva'] = ($response['pret_vanzare_val'] * (100 + $cota_tva -> valoare)) / 100;
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
			
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
		case "pret_vanzare_val": {
			$response['pret_vanzare_val'] = $frm['pret_vanzare_val'];
			$response['pret_vanzare_ron'] = $frm['pret_vanzare_val'] * $factura -> curs_valutar;
			
			$response['val_vanzare_ron']  = $frm['cantitate'] * $response['pret_vanzare_ron'];
			$response['val_vanzare_val']  = $frm['cantitate'] * $frm['pret_vanzare_val'];

			$response['pret_ron_cu_tva'] = ($response['pret_vanzare_ron'] * (100 + $cota_tva -> valoare)) / 100;
			$response['pret_val_cu_tva'] = ($response['pret_vanzare_val'] * (100 + $cota_tva -> valoare)) / 100;
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
			
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
		
		case "pret_ron_cu_tva": {
			$response['pret_ron_cu_tva'] = $frm['pret_ron_cu_tva'];
			$response['pret_val_cu_tva'] = $frm['pret_ron_cu_tva'] / $factura -> curs_valutar;
			
			$response['pret_vanzare_ron'] = $frm['pret_ron_cu_tva'] * 100 / ($cota_tva -> valoare + 100);
			$response['pret_vanzare_val'] = $frm['pret_vanzare_ron'] / $factura -> curs_valutar;
			
			$response['val_vanzare_ron']  = $frm['cantitate'] * $response['pret_vanzare_ron'];
			$response['val_vanzare_val']  = $frm['cantitate'] * $response['pret_vanzare_val'];
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
			
			
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
		
		case "pret_val_cu_tva": {
			$response['pret_val_cu_tva'] = $frm['pret_val_cu_tva'];
			$response['pret_ron_cu_tva'] = $frm['pret_val_cu_tva'] * $factura -> curs_valutar;
			
			$response['pret_vanzare_val'] = ($frm['pret_val_cu_tva'] * 100) / ($cota_tva -> valoare + 100);
			$response['pret_vanzare_ron'] = $response['pret_vanzare_val'] * $factura -> curs_valutar;
			
			$response['val_vanzare_ron']  = $frm['cantitate'] * $response['pret_vanzare_ron'];
			$response['val_vanzare_val']  = $frm['cantitate'] * $response['pret_vanzare_val'];
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
			
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
		
		case "val_vanzare_ron": {
			$response['val_vanzare_ron']  = $frm['val_vanzare_ron'];
			$response['val_vanzare_val']  = $frm['val_vanzare_ron'] / $factura -> curs_valutar;
			
			$response['pret_vanzare_ron'] = $frm['val_vanzare_ron'] / $frm['cantitate'];
			$response['pret_vanzare_val'] = $response['val_vanzare_val'] / $frm['cantitate'];
			
			$response['pret_ron_cu_tva'] = ($response['pret_vanzare_ron'] * (100 + $cota_tva -> valoare)) / 100;
			$response['pret_val_cu_tva'] = ($response['pret_vanzare_val'] * (100 + $cota_tva -> valoare)) / 100;
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
			
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
		case "val_vanzare_val": {
			$response['val_vanzare_val']  = $frm['val_vanzare_val'];
			$response['val_vanzare_ron']  = $frm['val_vanzare_val'] * $factura -> curs_valutar;
			
			$response['pret_vanzare_ron'] = $response['val_vanzare_ron'] / $frm['cantitate'];
			$response['pret_vanzare_val'] = $frm['val_vanzare_val'] / $frm['cantitate'];
			
			$response['pret_ron_cu_tva'] = ($response['pret_vanzare_ron'] * (100 + $cota_tva -> valoare)) / 100;
			$response['pret_val_cu_tva'] = ($response['pret_vanzare_val'] * (100 + $cota_tva -> valoare)) / 100;
			
			$response['val_ron_cu_tva']  = $frm['cantitate'] * $response['pret_ron_cu_tva'];
			$response['val_val_cu_tva']  = $frm['cantitate'] * $response['pret_val_cu_tva'];
			
			$response['val_tva_ron'] = ($response['val_vanzare_ron'] * $cota_tva -> valoare) / 100;
			$response['val_tva_val'] = ($response['val_vanzare_val'] * $cota_tva -> valoare) / 100;
		}break;
	}
	foreach($response as $key => $value) {
		$objResponse -> assign($key, "value", douazecimale($value));
	}
	return $objResponse;
}
?>