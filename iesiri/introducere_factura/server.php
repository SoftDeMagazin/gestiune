<?php
require_once("common.php");
$xajax->processRequest();

function load($factura_id=NULL) {
	if($factura_id) {
		$factura = new Facturi($factura_id);
		$factura -> data_factura = c_data($factura -> data_factura);
		$factura -> data_scadenta = c_data($factura -> data_scadenta);
		$factura -> anulareScaderi();
	}
	else {
		$factura = new Facturi();
	}
	
	$serie = $factura -> getSerie($_SESSION['user'] -> gestiune_id);
	
	if(!count($serie)) {
		$dialog = new Dialog(800, 600, "", "win_alert_serie");
		$dialog -> modal = true;
		$dialog -> close = FALSE;
		$dialog -> append("Nu ati configurat o serie numerica pentru facturi!");
		$dialog -> addButton("Serii Numerice", "xajax_location('".DOC_ROOT."configurari/serii_numerice/');");
		return $dialog -> open();
	}
	
	$objResponse = new xajaxResponse();
	if($factura_id) {
		$objResponse -> assign("txtCautareFurnizor", "value", $factura -> tert -> denumire);
		$agenti = new Agenti("inner join agenti_terti using(agent_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and tert_id = '". $factura -> tert_id ."';");
		if(!count($agenti)) {
			$agenti -> fromString("where 1");
		}
		
		$objResponse -> assign("div_frm_factura", "innerHTML", $factura -> frmFactura($factura -> tert_id));
		$objResponse -> append("div_frm_agent", "innerHTML", $agenti -> select($factura -> agent_id));
		
		$delegati = new Delegati("where tert_id = '". $factura -> tert_id ."'");
		$objResponse -> append("div_frm_delegat", "innerHTML", $delegati -> select($factura -> delegat_id, "xajax_selectDelegat(this.value, '". $factura -> tert_id ."')"));
		
		if($factura -> delegat_id > 0) {
			$objResponse -> append("div_frm_delegat", "innerHTML", '<div id="frm_delegat">'.$delegat -> frmContent().'</div>');
		}
		else {
			
			if($factura -> delegat_id == '-3') {
				$objResponse -> append("div_frm_delegat", "innerHTML", "<br>Transportator<br>".$factura -> transportator());
				
			}
			$objResponse -> append("div_frm_delegat", "innerHTML", '<div id="frm_delegat"><input type="hidden" id="delegat_id" name="delegat_id" value="'. $factura -> delegat_id .'"></div>');
		}	
		
		
		
		$adr = new TertiAdrese("where tert_id = '". $factura -> tert_id ."'");
		$objResponse -> append("div_frm_adresa", "innerHTML", $adr -> select($factura -> adresa_id));

		copyResponse($objResponse, lista($factura -> id));
	}	
	
	copyResponse($objResponse, frmComponenta(0, $factura_id));
	copyResponse($objResponse, initControl());
	copyResponse($objResponse, filtruProduse(''));
	copyResponse($objResponse, switchTab("antet"));
	return $objResponse;

}

function changeNaturaTranzactieiA($cod) {
	$copii = new NaturaTranzactiei("where parent_code = '$cod'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_frm_natura_tranzactie_b", "innerHTML", $copii -> select_copil());
	return $objResponse;
}

function frmClient() {
	$client = new ViewTertiGestiuni();
    $objResponse = new xajaxResponse();
    $dialog = new Dialog(800, 600, '', 'win_add_client');
    $dialog->title = "Adauga client";
    $dialog->append($client->frmDefault());
    $dialog->addButton("Salveaza", "xajax_salveazaClient(xajax.getFormValues('frm_view_terti_gestiuni'));<%close%>");
    $dialog->addButton("Renunta");
    $objResponse = openDialog($dialog);
    $gest = new Gestiuni("where 1");
    if ($id)
    {
        $selected = $model->getGestiuniAsociate();
    }
    else
    {
        $selected = array($_SESSION['user']->gestiune_id);
    }
    $objResponse->assign("div_frm_gest", "innerHTML", "Gestiune<br />
".$gest->selectMulti($selected));

	$objResponse -> script("\$('#denumire').focus().select();");
	$objResponse -> script("
		$('#tip').change(
			function() {
				switch($(this).val()) {
					case 'intern': {
						$('#cod_tara').attr('value', 'RO');
						$('#valuta').val('LEI');
						$('#err_frm_cod_tara').html('');
					}break;
					case 'extern_ue': {
						$('#cod_tara').val(0);
						$('#valuta').val('EUR');
					}break;
					case 'extern_nonue': {
						$('#cod_tara').val(0);
						$('#valuta').val('USD');
					}break;
				}
			}
		);
	");
    $objResponse->script("$('#div_frm_gest #gestiune_id').multiSelect()");
    return $objResponse;
}

function salveazaClient($frmValues) {
	$model = new Terti($frmValues);
    $objResponse = new xajaxResponse();
    if (!$model->validate($objResponse))
    {
        return $objResponse;
    }
    $model->save();
    $model->disociazaGestiuni($frmValues['gestiune_id']);
    $model->asociazaCuGestiuni($frmValues['gestiune_id']);
    $objResponse = selectClient($model->id);
    $objResponse->script("\$('#win_add_furnizor').dialog('close');");
    return $objResponse;
}


function salveazaAntet($frmValues)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}	
	$frmValues['gestiune_id'] = $_SESSION['user'] -> gestiune_id;
	$factura = new Facturi($frmValues);
	$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
	$objResponse = new xajaxResponse();
	
	if(!$frmValues['tert_id']) {
		return alert('Nu ati selectat client!');
	}
	
	if(!$factura -> validate($objResponse)) {
		return $objResponse;
	}
	
	if(!$factura -> id) {
		$factura -> data_factura = data_c($factura -> data_factura);
		$factura -> data_scadenta = data_c($factura -> data_scadenta);
		$factura -> utilizator_id = $_SESSION['user'] -> user_id;
		

		
		if($frmValues['tip_factura'] != "agent") {
			
					$facturi_posterioare = new Facturi("where 
					data_factura > '". $factura -> data_factura ."'
					and salvat = 'DA';
					");
					if(count($facturi_posterioare)) {
						return alert('S-a emis deja o factura cu data mai mare decat data selectata!');
					}
			
			$factura -> numar_doc = $factura -> getNumar($_SESSION['user'] -> gestiune_id);
			$serie = $factura -> getSerie($_SESSION['user'] -> gestiune_id);
			$factura -> serie_id = $serie -> id;
			$serie -> increment();
		}
		$factura -> data_introducere = data();
	}
	else {
		$factura -> data_factura = data_c($factura -> data_factura);
	}
	
	if($frmValues['delegat_id'] >= 0) {
	
		if(!$frmValues['nume']) return alert('Introduceti nume delegat');
		$delegat = new Delegati($frmValues);
		$delegat -> save();
		$factura -> delegat_id = $delegat -> id;
	}
	elseif($frmValues['delegat_id'] < 0){
		$factura -> delegat_id = $frmValues['delegat_id'];
	}
	
	if($frmValues['adresa_id']) {
		$adresa = new TertiAdrese($frmValues['adresa_id']);
		$factura -> adresa_livrare = $adresa -> adresa;
		$factura -> tara_livrare = $adresa -> cod_tara;
	}
	
	
	$factura -> societate_id = $gestiune -> punct_lucru -> societate_id;
	$factura -> save();
	
	$factura -> data_factura = c_data($factura -> data_factura);
	
	$agenti = new Agenti("inner join agenti_terti using(agent_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and tert_id = '". $factura -> tert_id ."';");
	if(!count($agenti)) {
		$agenti -> fromString("where 1");
	}
	

	
	$objResponse -> assign("div_frm_factura", "innerHTML", $factura -> frmFactura($factura -> tert_id));
	
	
	$objResponse -> append("div_frm_agent", "innerHTML", $agenti -> select($factura -> agent_id));
	
	$delegati = new Delegati("where tert_id = '". $factura -> tert_id ."'");
	$objResponse -> append("div_frm_delegat", "innerHTML", $delegati -> select($factura -> delegat_id, "xajax_selectDelegat(this.value, '". $factura -> tert_id ."')"));
	
	if($factura -> delegat_id > 0) {
		$objResponse -> append("div_frm_delegat", "innerHTML", '<div id="frm_delegat">'.$delegat -> frmContent().'</div>');
	}
	else {
			if($factura -> delegat_id == '-3') {
				$objResponse -> append("div_frm_delegat", "innerHTML", "<br>Transportator<br>".$factura -> transportator());
			}
		$objResponse -> append("div_frm_delegat", "innerHTML", '<div id="frm_delegat"><input type="hidden" id="delegat_id" name="delegat_id" value="'. $factura -> delegat_id .'"></div>');
	}
	
	$adr = new TertiAdrese("where tert_id = '". $factura -> tert_id ."'");
	$objResponse -> append("div_frm_adresa", "innerHTML", $adr -> select($factura -> adresa_id));

	
	
	
	
	
	copyResponse($objResponse, switchTab('frm'));
	copyResponse($objResponse, frmComponenta(0));
	$objResponse -> script("\$('#cautare_produs').focus().select();");
	
	$objResponse -> script("is_saved = false;");
	return $objResponse;
}

function filtruProduse($filtru) {
	if($filtru) {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."' and denumire like '$filtru%' order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}
	else {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."' order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}		
}


function selectProdus($produs_id, $add_pret=true) {
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("produs_id", "value", $produs -> id);
	$objResponse -> assign("div_info_produs", "innerHTML", $produs -> denumire);
	
	if($add_pret) {
		if($produs -> pret_referinta == 'EUR') {
			if($produs -> pret_val) $pret = $produs -> pret_val;
			else $pret = "0";	
		} else {
			if($produs -> pret_ron) $pret = $produs -> pret_ron;
			else $pret = "0";	
		}
	}

	
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
	if($add_pret) {
		if($produs -> pret_referinta == 'EUR') {
			$objResponse -> assign("pret_vanzare_val", "value", $pret);
		} else {
			$objResponse -> assign("pret_ron_cu_tva", "value", $pret);
		}
		
	}
	$objResponse -> assign('div_detalii_produs', 'innerHTML', '
	<strong>Ambalare:</strong>'. $produs -> ambalare .'</br> 
	<strong>Stoc:</strong> <a href="#" onClick="xajax_infoLoturi(\''. $produs_id .'\'); return false;">'. $stoc_cant .'</a><br />
	');
	
	$objResponse -> append("div_detalii_produs", "innerHTML", Html::overflowDiv($loturi -> infoLoturi(), "130px"));
	$objResponse -> assign("div_frm_unitate_masura", "innerHTML", $produs -> unitate_masura());
	$objResponse -> script("\$('#cantitate').focus().select();");
	if($add_pret) {
		if($produs -> pret_referinta == 'EUR') {
			$objResponse -> script("xajax_calculator(xajax.getFormValues('frm_facturi_continut'), 
							$('#factura_id').val(), 
							'pret_vanzare_val');");
		} else {
			$objResponse -> script("xajax_calculator(xajax.getFormValues('frm_facturi_continut'), 
							$('#factura_id').val(), 
							'pret_ron_cu_tva');");
		}
	}
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
	$client = new ViewTertiGestiuni();
	$gestiune_id = $_SESSION['user'] -> gestiune_id;
	
	$client -> getByIds($tert_id, $_SESSION['user'] -> gestiune_id);
	
	$tert = new Terti($tert_id);
	
//	return alert($tert -> getLimitaDeCredit($gestiune_id));
	
	if($tert -> esteBlocat($_SESSION['user'] -> gestiune_id)) {
		$objResponse = alert('Clientul este blocat. Nu se pot emite facturi!<br> 
		Limita credit: '.$tert -> getLimitaDeCredit($gestiune_id));
		$objResponse -> assign("tert_id", "value", 0);
		return $objResponse;
	}
	
	if($tert -> depasesteLimitaCredit($gestiune_id)) {
		$tert -> blocheazaClient($gestiune_id);
		$objResponse = alert('Clientul este blocat. Nu se pot emite facturi!<br> 
		Limita credit: '.$tert -> getLimitaDeCredit($gestiune_id));
		$objResponse -> assign("tert_id", "value", 0);
		return $objResponse;
	}
	
	$factura = new Facturi();
	$factura -> tert_id = $tert_id;
	$factura -> data_factura = c_data(data());
	$factura -> data_scadenta = c_data(data());
	
	if($client -> scadenta_default) {
		$scadenta = strtotime ( '+'. $client -> scadenta_default .' day' , strtotime ( data_c($factura -> data_factura) ) ) ;
		$scadenta = date ( 'Y-m-d' , $scadenta );
		$factura -> data_scadenta = c_data($scadenta);
		$factura -> scadenta = $client -> scadenta_default;
	}
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
	
	
	$agenti = new Agenti("inner join agenti_terti using(agent_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and tert_id = '". $tert_id ."';");
	if(!count($agenti)) {
		$agenti -> fromString("inner join agenti_gestiuni using(agent_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	}
	
	$delegati = new Delegati("where tert_id = '$tert_id'");
	
	$delegat = new Delegati();
	$delegat -> tert_id = $client -> id;
	
	$objResponse -> assign("tert_id", "value", $client -> id);
	$objResponse -> assign("txtCautareFurnizor", "value", $client -> denumire);
	$objResponse -> assign("div_frm_factura", "innerHTML", $factura -> frmFactura($tert_id));
	$objResponse -> append("div_frm_agent", "innerHTML", $agenti -> select());
	$objResponse -> append("div_frm_delegat", "innerHTML", $delegati -> select(0, "xajax_selectDelegat(this.value, '". $client -> id ."')"));
	$objResponse -> append("div_frm_delegat", "innerHTML", '<div id="frm_delegat">'.$delegat -> frmContent().'</div>');
	$objResponse -> append("div_frm_factura", "innerHTML", '
	    <div>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>
    </div>

	');
	$adrese = new TertiAdrese("where tert_id = '". $factura -> tert_id ."'");
	$objResponse -> assign("div_frm_adresa", "innerHTML", $adrese -> select()); 
	$objResponse -> assign("div_filtru_furnizori", "innerHTML", "");
	
	
	copyResponse($objResponse, initControl());
	
	$objResponse -> script("
			$('#agent_id').change(
				function() {
					xajax_selectAgent($(this).val(), $('#tert_id').val());
				}
			);
	");
	
	$objResponse -> script("
			$('#scadenta').change(
				function() {
					xajax_calculeazaScadenta($(this).val(), 0, $('#data_factura').val());
				}
			);
	");
	$objResponse -> script("
			$('#data_factura').change(
				function() {
					xajax_calculeazaScadenta($('#scadenta').val(), 0, $('#data_factura').val());
				}
			);
	");
	$objResponse -> script("
			$('#data_scadenta').change(
				function() {
					xajax_calculeazaScadenta(0, $('#data_scadenta').val(), $('#data_factura').val());
				}
			);
	");
	$objResponse -> script("$('#natura_tranzactie_a').change(
		function() {
			xajax_changeNaturaTranzactieiA($(this).val());
		}
	);
	");
	
	switch($client -> tip) {
		case "intern": {
			$objResponse -> script("$('#tip_factura').val('interna');");
			$objResponse -> script("$('#valuta').val('LEI');");
		}break;
		case "extern_ue": {
			$objResponse -> script("$('#tip_factura').val('extern_ue');");
			$objResponse -> script("$('#valuta').val('EUR');");
		}break;
		case "extern_nonue": {
			$objResponse -> script("$('#tip_factura').val('extern_nonue');");
			$objResponse -> script("$('#valuta').val('EUR');");
		}break;
	}
	
	$objResponse -> script(
	"
	$('#valuta').change(
		function (){
			xajax_changeCurs($(this).val());
		}
	); 
	"
	);
	
	$objResponse -> script(
	"	
		$('#tip_factura').change(
			function() {
				switch($(this).val()) {
					case 'interna': {
						$('#valuta').val('LEI');
					}break;
					case 'extern_ue': {
						$('#valuta').val('EUR');
					}break;
					case 'extern_nonue': {
						$('#valuta').val('EUR');
					}break;
					case 'agent': {
						$('#numar_doc').attr('readonly', false);
						$('#numar_doc').val('');
					}break;
				}
			}
		)
	"
	);
	
	return $objResponse;
}

function changeCurs($valuta) {
	$curs = new Cursuri();
	$curs -> getLast($valuta); 
	if(count($curs)) {
		$valoare = $curs -> valoare;
	} else {
		$valoare = '0.00';
	}
	$objResponse = new xajaxResponse();
	$objResponse -> assign("curs_valutar", "value", $valoare);
	return $objResponse;
}

function calculeazaScadenta($zile=0, $data=0, $data_emitere) {
	
	if($zile) {

		$scadenta = strtotime ( '+'. $zile .' day' , strtotime ( data_c($data_emitere) ) ) ;
		$scadenta = date ( 'Y-m-d' , $scadenta );
		$objResponse = new xajaxResponse();
		$objResponse -> assign("data_scadenta", "value", c_data($scadenta));
		return $objResponse;
	}
	
	if($data) {
		$diff = get_time_difference($data_emitere, $data);
		$zile = $diff['days'];
		$objResponse = new xajaxResponse();
		$objResponse -> assign("scadenta", "value", $zile);
		return $objResponse;
	}
}

function selectAgent($agent_id, $tert_id=0) {
	$agent = new AgentiTerti("where agent_id = '$agent_id' and tert_id='$tert_id' and gestiune_id = ". $_SESSION['user'] -> gestiune_id ."");
	$objResponse = new xajaxResponse();
	if(count($agent)) {
		$objResponse -> assign("comision", "value", $agent -> comision);
		return $objResponse;
	}
	else {
		$objResponse -> assign("comision", "value", '0.00');
		return $objResponse;
	}
}

function selectDelegat($delegat_id, $tert_id=0) {
	$objResponse = new xajaxResponse();
	if($delegat_id > 0) {
		$delegat = new Delegati($delegat_id);
		$objResponse -> assign("frm_delegat", "innerHTML", $delegat -> frmContent());
		$objResponse -> assign("lbl_frm_auto_numar", "innerHTML", "Numar Auto");
		return $objResponse;
	}

	if($delegat_id == 0) {
		$delegat = new Delegati();
		$delegat -> tert_id = $tert_id;
		$objResponse -> assign("frm_delegat", "innerHTML", $delegat -> frmContent());
		$objResponse -> assign("lbl_frm_auto_numar", "innerHTML", "Numar Auto");
		return $objResponse;
	}
	if($delegat_id == -1) {
		$objResponse -> assign("frm_delegat", "innerHTML", '<input type="hidden" id="delegat_id" name="delegat_id" value="'. $delegat_id .'">');
		$objResponse -> assign("lbl_frm_auto_numar", "innerHTML", "Numar Auto");
		return $objResponse;
	}
	
	if($delegat_id == -2) {
		$objResponse -> assign("frm_delegat", "innerHTML", '<input type="hidden" id="delegat_id" name="delegat_id" value="'. $delegat_id .'">');
		$objResponse -> assign("lbl_frm_auto_numar", "innerHTML", "Numar AWB");
		return $objResponse;
	}
	if($delegat_id == -3) {
		$objResponse -> assign("frm_delegat", "innerHTML", '
		<input type="hidden" id="delegat_id" name="delegat_id" value="'. $delegat_id .'">
		Nume Transportator <br>
		<input type="text" id="transportator" name="transportator">
		');
		$objResponse -> assign("lbl_frm_auto_numar", "innerHTML", "Numar AUTO");
		return $objResponse;
	}
}

function frmComponenta($continut_id=NULL, $factura_id=NULL) {
	$continut = new FacturiContinut($continut_id);
	$objResponse = new xajaxResponse();
	
	$objResponse -> assign("div_frm_continut", "innerHTML", $continut -> frmFacturaContinut());
	$objResponse -> script($continut -> scriptFactura());
	
	if(!$continut_id) $objResponse -> assign("div_info_produs", "innerHTML", "&nbsp;");
	else $objResponse -> assign("div_info_produs", "innerHTML", $continut -> produs -> denumire);
	$objResponse -> script("\$('#cautare_produs').focus().select();");
	copyResponse($objResponse, switchTab('frm'));
	if($continut_id) {
		copyResponse($objResponse, selectProdus($continut -> produs_id, false));
	}
	return $objResponse;	
}

function salveazaComponenta($frmValues, $factura_id) {
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	if(!$factura_id) {
		return alert('Introduceti si salvat antet factura!');
	}
	
	if(!$frmValues['produs_id']) {
		return alert('Selectati produsul pe care se face intrarea!');
	}
	
	$produs = new ViewProduseGestiuni(" where produs_id = '". $frmValues['produs_id'] ."' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	
	$continut = new FacturiContinut($frmValues);
	$continut -> denumire = $produs -> denumire;
	$continut -> cod_produs = $produs -> cod_produs;
	$continut -> cod_bare = $produs -> cod_bare;
	$continut -> nc8 = $produs -> nc8;
	$continut -> factura_id = $factura_id;
	$continut -> save();
	
	
	$stoc = $produs -> getStoc();
	
	$objResponse = lista($factura_id);
	if($stoc <= 0) {
		copyResponse($objResponse, alert('Atentie produsul adaugat nu se afla in stoc!'));
		$objResponse -> script("$('#btnOk').focus()");
	} else {
		if($frmValues['cantitate'] > $stoc) {
			copyResponse($objResponse, alert('Atentie ati introdus o cantitate mai mare decat stocul disponibil pentru produsul adaugat!'));
			$objResponse -> script("$('#btnOk').focus()");
		}
	}
	copyResponse($objResponse, frmComponenta());
	$objResponse -> assign("div_detalii_produs", "innerHTML", "");
	return $objResponse;
}

function stornare($frmValues, $factura_id) {
	if(!$frmValues['produs_id']) {
		return alert('Selectati un produs!');
	}
	$dialog = new Dialog(800, 600, "", "win_stornare");	
	$factura = new Facturi($factura_id);
	$produs_id = $frmValues['produs_id'];
	$cantitate = $frmValues['cantitate'];
	$produs = new Produse($produs_id);
	$dialog -> title = "Stornare ".$produs -> denumire;
	$sql = " inner join facturi using(factura_id) where 
	facturi.tert_id = '". $factura -> tert_id ."'
	and facturi.gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'
	and produs_id = '$produs_id'
	and storno = 0
	and cantitate_stornata < cantitate
	order by data_factura desc";
	$continut = new FacturiContinut($sql);
	if(!count($continut)) {
		return alert('Produsul selectat nu a fost vandut acestui tert!');
	}
	$dialog -> append('<strong>Cantitate Stornare:</strong> '. $frmValues['cantitate'] .'');
	
	$dialog -> append('<h3>Facturi Emise</h3>');
	$dialog -> append('<form id="frm_continut_stornare" name="frm_continut_stornare" onSubmit="return false;">');
	$dialog -> append(Html::overflowDiv($continut -> listaStornare(), "300px"));
	$dialog -> append('</form>');
	
	$dialog -> addButton("Renunta", "xajax_frmComponenta(0, '$factura_id');");
	$dialog -> addButton("Salveaza", "xajax_salveazaStornare('$factura_id', xajax.getFormValues('frm_continut_stornare'), '". $produs -> id ."');");
	$objResponse = $dialog -> open();
	$objResponse -> script("$('#win_stornare .tablesorter').tablesorter();");
	return $objResponse;
}

function salveazaStornare($factura_id, $frm, $produs_id) {
	$i=0;
	$retururi = array();
	$is_ok = true;
	foreach($frm['continut_id'] as $continut_id) {
		$continut = new FacturiContinut($continut_id);
		if($frm['cantitate_storno'][$i] > ($continut -> cantitate - $continut -> cantitate_stornata)) {
			$is_ok = false;
			break;
		}
		if($frm['cantitate_storno'][$i] > 0) { 
			$retururi[] = array("continut" => $continut, "cantitate" => $frm['cantitate_storno'][$i]);
		}	
		$i++;
	}
	
	if(!$is_ok) {
		return alert('Cantitate pe care doriti sa o stornati este mai mare decat cantitatea ramasa facturata!');
	}
	
	foreach($retururi as $retur) {
		$continut = new FacturiContinut($retur['continut'] -> _data);
		$continut -> continut_id = 0;
		$continut -> factura_id = $factura_id;
		$continut -> produs_id = $produs_id;
		$continut -> cantitate = $retur['cantitate']*(-1);
		$continut -> storno = 1;
		
		$continut -> val_tva_ron = ($continut -> pret_ron_cu_tva - $continut -> pret_vanzare_ron)*$continut -> cantitate;
		$continut -> val_tva_val = ($continut -> pret_ron_cu_tva - $continut -> pret_vanzare_val)*$continut -> cantitate;
		
		$continut -> val_vanzare_ron = ($continut -> pret_vanzare_ron)*$continut -> cantitate;
		$continut -> val_vanzare_val = ($continut -> pret_vanzare_val)*$continut -> cantitate;
		
		$continut -> val_ron_cu_tva = ($continut -> pret_ron_cu_tva)*$continut -> cantitate;
		$continut -> val_val_cu_tva = ($continut -> pret_val_cu_tva)*$continut -> cantitate;
		
		$continut -> storno_id = $retur['continut'] -> id;
		$continut -> save();
		
		$retur['continut'] -> cantitate_stornata += $retur['cantitate'];
		$retur['continut'] -> save();
		
	}
	$objResponse = lista($factura_id);
	copyResponse($objResponse, closeDialog('win_stornare'));
	copyResponse($objResponse, frmComponenta(0, $factura_id));
	return $objResponse;
}

function stergeComponenta($continut_id) {
	if(!$continut_id) {
		return alert('Selectati componenta pe care doriti sa o stergeti');
	}
	$continut = new FacturiContinut($continut_id);
	$factura_id = $continut -> factura_id;
	$continut -> delete();
	if($continut -> storno == 1) {
		$stornat = new FacturiContinut($continut -> storno_id);
		$stornat -> cantitate_stornata += $continut -> cantitate;
		$stornat -> save();
	}
	$objResponse = lista($factura_id);
	copyResponse($objResponse, frmComponenta());
	return $objResponse;
}

function lista($factura_id)
{
	$factura = new Facturi($factura_id);
	$objResponse = new xajaxResponse();
	$continutFactura = $factura -> continut -> lista("", "xajax_frmComponenta('<%continut_id%>');");
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
	$factura = new Facturi($frmValues['factura_id']);
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
	$dialog -> addButton("Salveaza Factura", "<%close%>xajax_salveazaFactura(". $factura -> id .");");
	$dialog -> addButton("Anuleaza Factura", "<%close%>xajax_anuleazaFactura(". $factura -> id .");");
	$dialog -> addButton("Continua Editare", "<%close%>");
	$objResponse = openDialog($dialog);
	return $objResponse;
}


function salveazaFactura($factura_id) {
	$factura = new Facturi($factura_id);
	$factura -> scadStoc();
	$factura -> salvat = 'DA';
	if($factura -> totalFactura() < 0) $factura -> achitat = 'DA';
	$factura -> save();
	$objResponse = new xajaxResponse();
	if($factura -> totalFactura() < 0) {
		
		$dialog = new Dialog(500, 300,'', "win_factura_incasare");
		$dialog -> append("Factura este negativa si va fi adaugata ca incasare pentru client");
		$dialog -> append("<br> Selectati tipul incasarii:<br />");
		$mod_plata = new ModalitatiPlata("where descriere like '%FACTURA%'");
		$dialog -> append($mod_plata -> select());
		$dialog -> addButton("Adauga incasare", "xajax_adaugaIncasare('$factura_id', $('#mod_plata_id').val())");
		$objResponse = $dialog -> open();
	}
	else {
		$dialog = new Dialog(500, 300,'', "win_factura_save");
		$dialog -> append("Factura a fost salvata");
		$dialog -> addButton("Emite factura noua", "xajax_location('". DOC_ROOT ."iesiri/introducere_factura/')" );
		$dialog -> addButton("Tiparire Factura", "popup('". DOC_ROOT ."print/factura_pdf.php?factura_id=". $factura -> id ."', 'factura_pdf');");
		$objResponse = $dialog -> open();
	}
	
	$objResponse -> script("is_saved = true;");
	return $objResponse;
}

function adaugaIncasare($factura_id, $mod_plata_id) {
	$factura = new Facturi($factura_id);
	$incasare = new Incasari();
	$incasare -> mod_plata_id = $mod_plata_id;
	$incasare -> numar_doc = $factura -> numar_doc;
	$incasare -> tert_id = $factura -> tert_id;
	$incasare -> gestiune_id = $factura -> gestiune_id;
	$incasare -> societate_id = $factura -> societate_id;
	$incasare -> utilizator_id = 0;
	if($factura -> tert -> tip == "intern") {
		$incasare -> suma = $factura -> totalFactura()*(-1);
	}
	else {
		$incasare -> suma = $factura -> totalFacturaValuta()*(-1);
	}
	$incasare -> data_doc = $factura -> data_factura;
	$incasare -> save();
	
	$objResponse = alert('Am adaugat incasare pentru factura');
	return $objResponse; 
}

function anuleazaFactura($factura_id) {
	
	$factura = new Facturi($factura_id); 
	$factura -> anuleaza();
	
	$dialog = new Dialog(800, 600, "", "win_an_factura");
	$dialog -> title = "Info";
	$dialog -> modal = true;
	$dialog -> close = false;
	$dialog -> append("Factura a fost anulata");
	$dialog -> addButton("Emite Factura Noua", "xajax_location('".DOC_ROOT."iesiri/introducere_factura/');");
	$dialog -> addButton("Evidenta Factura", "xajax_location('".DOC_ROOT."iesiri/evidenta_facturi/');"); 
	$objResponse = $dialog -> open();
	$objResponse -> script("is_saved = true;");
	return $objResponse;
}

function calculator($frm, $factura_id, $mod="pret_ach_ron") {
	
	$factura = new Facturi($factura_id);
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
			$response['pret_vanzare_val'] = $response['pret_vanzare_ron'] / $factura -> curs_valutar;
			
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


function discount() {
	$dialog = new Dialog(800, 600, "", "win_discount");
	$dialog -> title = "Adauga discount";
	$dialog -> append('<fieldset>');
	$dialog -> append("<legend>Reducere preturi</legend>");
	$dialog -> append('<input type="text" id="procent_reducere" name="procent_reducere" > %');
	$dialog -> append('<input type="button" value="Aplica Reducere" onClick="xajax_reducerePreturi($(\'#factura_id\').val(),$(\'#procent_reducere\').val());$(\'#win_discount\').dialog(\'close\');">'); 	
	$dialog -> append('</fieldset>');
	return $dialog -> open();
}

function reducerePreturi($factura_id, $procent) {
	$factura = new Facturi($factura_id);
	$factura -> reducerePreturi($procent);
	$objResponse = lista($factura_id);
	return $objResponse;
}
?>