<?php 
require_once("common.php");
$xajax->processRequest();

//helpers
function scriptDataScadenta()
{
    $objResponse = new xajaxResponse();
    $objResponse->script("
			$('#data_scadenta').blur(
			function () {
				if(!$(this).val() || $(this).val() == '__.__.____') {
					$(this).val($('#data_factura').val());
				}
			}
			);		
	");
    return $objResponse;
    
}


//xajax functions
function load($factura_id = NULL)
{
    $objResponse = new xajaxResponse();
    if ($factura_id)
    {
        $factura = new FacturiIntrari($factura_id);
        $factura->data_factura = c_data($factura->data_factura);
        $factura->data_scadenta = c_data($factura->data_scadenta);
        copyResponse($objResponse, lista($factura->id));
        
        if ($factura->tert->tip == "extern_ue")
        {
            $continut_intrastat = new ContinutIntrastat("where `factura_intrare_id` = '$factura_id'");
            if (count($continut_intrastat))
                $objResponse->assign("grid_intrastat", "innerHTML", $continut_intrastat->listaEditare());
        }
        else
        {
            $objResponse->assign("intrastat", "innerHTML", "Nu se aplica la facturile emise de furnizorii interni!");
        }
        
        $objResponse->assign("div_frm_factura", "innerHTML", $factura->frmFactura($factura->tert_id));
        $objResponse->append("div_frm_factura", "innerHTML", '<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi_intrari\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>');
        //$objResponse -> append("div_frm_factura", "innerHTML", "".$factura -> factura_intrare_id()."");
        $objResponse->assign("txtCautareFurnizor", "value", $factura->tert->denumire);
        
        
        copyResponse($objResponse, frmComponenta(0, $factura->id));
        copyResponse($objResponse, switchTab("antet"));
    }
    copyResponse($objResponse, initControl());
    copyResponse($objResponse, scriptDataScadenta());
    
    return $objResponse;
}

function frmFurnizor()
{
    $client = new ViewTertiGestiuni();
    $objResponse = new xajaxResponse();
    $dialog = new Dialog(800, 600, '', 'win_add_client');
    $dialog->title = "Adauga client";
    $dialog->append($client->frmDefault());
    $dialog->addButton("Salveaza", "xajax_salveazaFurnizor(xajax.getFormValues('frm_view_terti_gestiuni'));<%close%>");
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

function salveazaFurnizor($frmValues)
{
    $model = new Terti($frmValues);
    $objResponse = new xajaxResponse();
    if (!$model->validate($objResponse))
    {
        return $objResponse;
    }
    $model->save();
    $model->disociazaGestiuni($frmValues['gestiune_id']);
    $model->asociazaCuGestiuni($frmValues['gestiune_id']);
    $objResponse = selectFurnizor($model->id);
    $objResponse->script("\$('#win_add_furnizor').dialog('close');");
    return $objResponse;
}

function filtruFurnizor($filtru)
{
    $furnizori = new Terti();
    $objResponse = new xajaxResponse();
    if ($furnizori->cautare($filtru))
    {
        $objResponse->assign("div_filtru_furnizori", "innerHTML", $furnizori->selectMultiple());
    }
    $objResponse->script("
		$('#sel_furnizor').keyup(
			function(event) {
				if(event.keyCode == 13) {
					xajax_selectFurnizor(this.options[this.selectedIndex].value);
					$('#div_filtru_furnizori').hide();
				}
			}
		);
		
		$('#sel_furnizor').change(
			function(event) {
				$('#txtCautareFurnizor').val(this.options[this.selectedIndex].text);
			}
		);
		
		$('#sel_furnizor').dblclick(
			function(event) {
					xajax_selectFurnizor(this.options[this.selectedIndex].value);
					$('#div_filtru_furnizori').hide();
			}
		);
	");
    return $objResponse;
}

function selectFurnizor($tert_id)
{
    $furnizor = new Terti($tert_id);
    $factura = new FacturiIntrari();
    $factura->tert_id = $tert_id;
    $factura->data_factura = c_data(data());
    $factura->data_scadenta = c_data(data());
    if ($furnizor->tip == "intern")
    {
        $factura->curs_valutar = 1;
    }
    else
    {
        $curs = new Cursuri();
		$curs -> getLast('EUR');
        $factura->curs_valutar = $curs->valoare;
        $cota_tva = new CoteTva();
        $cota_tva->getTvaZero();
        $factura->cota_tva_id = $cota_tva->id;
    }
    $objResponse = new xajaxResponse();
    $objResponse->assign("tert_id", "value", $furnizor->id);
    $objResponse->assign("txtCautareFurnizor", "value", $furnizor->denumire." - ".$furnizor->cod_fiscal);
    $objResponse->assign("div_filtru_furnizori", "innerHTML", "");
    $objResponse->assign("div_frm_factura", "innerHTML", $factura->frmFactura($tert_id));
    $objResponse->append("div_frm_factura", "innerHTML", '<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi_intrari\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>');
    
    $objResponse->script("$('#numar_doc').focus().select();");
    $objResponse->script("$('#natura_tranzactie_a').change(
		function() {
			xajax_changeNaturaTranzactieiA($(this).val());
		}
	);
	");
	
    $objResponse->script("
			$('#scadenta').change(
				function() {
					xajax_calculeazaScadenta($(this).val(), 0, $('#data_factura').val());
				}
			);
	");
    $objResponse->script("
			$('#data_factura').change(
				function() {
					xajax_calculeazaScadenta($('#scadenta').val(), 0, $('#data_factura').val());
				}
			);
	");
    $objResponse->script("
			$('#data_scadenta').change(
				function() {
					xajax_calculeazaScadenta(0, $('#data_scadenta').val(), $('#data_factura').val());
				}
			);
	");
	
	$objResponse -> script("
	$('#valuta').change(
		function () {
			xajax_changeCurs($(this).val());
		}
	);	
	");
	
	$objResponse->script("
		$('#tip_doc').change(
			function() {
				switch($(this).val()) {
					case 'factura': {
						$('#cota_tva_id').val(1);
					}break;
					case 'bon_fiscal': {
						$('#cota_tva_id').val(1);
					}break;
					case 'aviz': {
						$('#cota_tva_id').val(2);
					}break; 
					case 'factura_retur': {
						$('#cota_tva_id').val(1);
					}break;
					
				}
			}
		);
	");
	
    copyResponse($objResponse, initControl());
    copyResponse($objResponse, scriptDataScadenta());
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

function calculeazaScadenta($zile = 0, $data = 0, $data_emitere)
{

    if ($zile)
    {
    
        $scadenta = strtotime('+'.$zile.' day', strtotime(data_c($data_emitere)));
        $scadenta = date('Y-m-d', $scadenta);
        $objResponse = new xajaxResponse();
        $objResponse->assign("data_scadenta", "value", c_data($scadenta));
        return $objResponse;
    }
    
    if ($data)
    {
        $diff = get_time_difference($data_emitere, $data);
        $zile = $diff['days'];
        $objResponse = new xajaxResponse();
        $objResponse->assign("scadenta", "value", $zile);
        return $objResponse;
    }
}

function changeNaturaTranzactieiA($cod)
{
    $copii = new NaturaTranzactiei("where parent_code = '$cod'");
    $objResponse = new xajaxResponse();
    $objResponse->assign("div_frm_natura_tranzactie_b", "innerHTML", $copii->select_copil());
    return $objResponse;
}

function salveazaAntet($frmValues)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
    $factura = new FacturiIntrari($frmValues);
    $gestiune = new Gestiuni($_SESSION['user']->gestiune_id);
    $objResponse = new xajaxResponse();
    if (!$factura->validate($objResponse))
    {
        return $objResponse;
    }
    
    if ($factura->id)
    {
        $factura_prev = new FacturiIntrari($factura->id);
        if ($factura->curs_valutar != $factura_prev->curs_valutar && $factura->tert->tip != 'intern')
        {
			$factura -> updatePreturiLei();
            $dialog = new Dialog(400, 300, "", "window_info_valutar");
            $dialog->append("Ati modificat cursul valutar!");
            $dialog->append("<br />
			Preturile de intrare in lei au fost actualizate!
			");
            $dialog->addButton("Ok", "<%close%>");
            copyResponse($objResponse, $dialog->open());
        }
    }
    
    // completez ce este comun
    $factura->data_factura = data_c($factura->data_factura);
    $factura->data_scadenta = data_c($factura->data_scadenta);
    $factura->gestiune_id = $gestiune->id;
    $factura->societate_id = $gestiune->punct_lucru->societate_id;
    $factura->utilizator_id = $_SESSION['user']->user_id;

    // dc este factura noua, adaug si data introducere
    if (!$factura->id)
    {
        $factura->data_introducere = data();
    }
   
   	//verific daca exista o factura de la acelasi furnizor cu acelasi numar si data
   	if(!$factura->id) {
		$test = new FacturiIntrari(" 
					where 
						numar_doc = '". $frmValues['numar_doc'] ."' 
						and tert_id = '". $frmValues['tert_id'] ."'
						and data_factura = '". data_c($frmValues['data_factura']) ."'
						and salvat = 'DA'
				
					");
		if(count($test)) {
			$tert = new Terti($frmValues['tert_id']);
			return alert('Documentul cu nr. '. $frmValues['numar_doc'] .' de la '. $tert -> denumire .' este deja inregistrat!');
		}		
	}
    // salvez factura
    $factura->save();
    
    $factura->data_factura = c_data($factura->data_factura);
    $factura->data_scadenta = c_data($factura->data_scadenta);
    
    $objResponse->assign("div_frm_factura", "innerHTML", $factura->frmFactura($factura->tert_id));
    $objResponse->append("div_frm_factura", "innerHTML", '
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_facturi_intrari\'))">
            </div>
            </label>
          </div>
          </td>
          </tr>
      </table>');   
	  
	 
	$objResponse -> assign("div_totaluri", "innerHTML", $factura -> totaluriFactura());  
	  
	copyResponse($objResponse, switchTab('frm'));
    copyResponse($objResponse, frmComponenta(0, $factura->id));
	copyResponse($objResponse, lista($factura -> id));
    copyResponse($objResponse, switchTab('frm'));
	$objResponse -> script("\$('#cautare_produs').focus().select();");
	$objResponse -> script("is_saved = false;");
    return $objResponse;
}

function filtruProduse($filtru)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
    if ($filtru)
    {
        $sql = "where gestiune_id = '".$_SESSION['user']->gestiune_id."' and denumire like '$filtru%' 
		and tip_produs in ('mp', 'marfa', 'ambalaj')
		order by denumire asc";
        $produse = new ViewProduseGestiuni($sql);
        $objResponse = new xajaxResponse();
        $objResponse->assign("div_select_produse", "innerHTML", $produse->select(30));
        return $objResponse;
    }
    else
    {
        $sql = "where gestiune_id = '".$_SESSION['user']->gestiune_id."'
		and tip_produs in ('mp', 'marfa', 'ambalaj')
		order by denumire asc";
        $produse = new ViewProduseGestiuni($sql);
        $objResponse = new xajaxResponse();
        $objResponse->assign("div_select_produse", "innerHTML", $produse->select(30));
        return $objResponse;
    }
}


function selectProdus($produs_id)
{
    $produs = new Produse($produs_id);
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", $produs->id);
    $objResponse->assign("div_info_produs", "innerHTML", $produs->denumire."");
	
	// clear discount 
	$objResponse->assign('discount_continut','value','');
	$objResponse->assign('discount_procentual_continut','checked','true');

    
    $stoc = new Stocuri("where produs_id = '$produs_id' and gestiune_id = '".$_SESSION['user']->gestiune_id."'");
    if (count($stoc))
    {
        $stoc_cant = $stoc->stoc;
    }
    else
    {
        $stoc_cant = 0;
        $stoc_val = 0;
        $pmp = 0;
    }
    
    $objResponse->assign("div_detalii_produs", "innerHTML", "<strong>NC8:</strong> ".$produs->btnInfoNC8()."<br />
	<strong>Stoc actual:</strong> ".$stoc_cant."");
	
    $loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '".$_SESSION['user']->gestiune_id."' and cantitate_ramasa <> 0");
    $objResponse->append("div_detalii_produs", "innerHTML", Html::overflowDiv($loturi->infoLoturi(), '90px'));
    
    $objResponse->assign("div_frm_unitate_masura", "innerHTML", $produs->unitate_masura());
    $objResponse->assign("unitate_masura_id", "disabled", TRUE);
    $objResponse->script("\$('#cantitate').focus().select();");

    
    return $objResponse;
}


function frmProdus($produs_id = 0)
{
    $produs = new ViewProduseGestiuni();
    $dialog = new Dialog(800, 700, "", "win_frm_produs");
    $dialog->append($produs->frmDefault());
    $dialog->title = "Adaugare/Editare produs";
    $dialog->addButton("Salveaza", "xajax_salveazaProdus(xajax.getFormValues('frm_view_produse_gestiuni'))");
    $dialog->addButton("Renunta");
    $objResponse = openDialog($dialog);
    $objResponse->script("\$('#denumire').focus().select();");
	
	$gest = new Gestiuni("where 1");
	$selected = array($_SESSION['user'] -> gestiune_id);

	$objResponse -> assign("div_frm_gest", "innerHTML", "Gestiune<br />".$gest -> selectMulti($selected));
	$objResponse -> script("$('#frm_view_produse_gestiuni #gestiune_id').multiSelect();");

    copyResponse($objResponse, initControl());
    return $objResponse;
}

function salveazaProdus($frmValues)
{
    $produs = new Produse($frmValues);
    $objResponse = new xajaxResponse();
    $produs->save();
	
	$produs -> disociazaGestiuni($frmValues['gestiune_id']);
	$produs -> asociazaCuGestiuni($frmValues['gestiune_id'], array("pret_ron"=>$frmValues['pret_ron'],"pret_val"=>$frmValues['pret_val']));

    $objResponse = selectProdus($produs->id);
    $objResponse->script("\$('#win_frm_produs').dialog('close')");
    return $objResponse;
}


function frmComponenta($continut_id = NULL, $factura_id = NULL)
{
    $factura = new FacturiIntrari($factura_id);
    $continut = new FacturiIntrariContinut($continut_id);
	if(!$continut_id) $continut -> cota_tva_id = $factura -> cota_tva_id;
    $objResponse = new xajaxResponse();
    $objResponse->assign("div_frm_continut", "innerHTML", $factura->frmContinutFactura($continut_id));
    $objResponse->assign("unitate_masura_id", "readonly", "readonly");
    if (!$continut_id)
        $objResponse->assign("div_info_produs", "innerHTML", "&nbsp;");
    else
        $objResponse->assign("div_info_produs", "innerHTML", $continut->produs->denumire);
    $objResponse->script("\$('#cautare_produs').focus().select();");
    $objResponse->script($factura->scriptFactura());
    $objResponse->assign("unitate_masura_id", "disabled", TRUE);
    
	
	switch($factura -> tip_doc) {
		case "aviz": {
		}
		case "factura_retur": {
		}
		case "factura": {
			$objResponse -> script("$('#pret_ach_ron').attr('readonly', false);");
			$objResponse -> script("$('#pret_ron_cu_tva').attr('readonly', true);");
			$objResponse -> script("$('#val_ach_ron').attr('readonly', false);");
			$objResponse -> script("$('#val_ron_cu_tva').attr('readonly', true);");
			$objResponse -> script("$('#lbl_pret_fara_tva').css('color', 'red');");
			$objResponse -> script("$('#lbl_val_fara_tva').css('color', 'red');");
		}break;
		case "bon_fiscal": {
			$objResponse -> script("$('#pret_ron_cu_tva').attr('readonly', false);");
			$objResponse -> script("$('#pret_ach_ron').attr('readonly', true);");
			$objResponse -> script("$('#val_ach_ron').attr('readonly', true);");
			$objResponse -> script("$('#val_ron_cu_tva').attr('readonly', false);");
			$objResponse -> script("$('#lbl_pret_cu_tva').css('color', 'red');");
			$objResponse -> script("$('#lbl_val_cu_tva').css('color', 'red');");
		}break;
		default: {
			$objResponse -> script("$('#pret_ach_ron').attr('readonly', false);");
			$objResponse -> script("$('#pret_ron_cu_tva').attr('readonly', true);");
			
		}break;
	}
	
    copyResponse($objResponse, switchTab("frm"));
    return $objResponse;
}

function salveazaComponenta($frmValues, $factura_id)
{
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	
    if (!$factura_id)
    {
        return alert('Introduceti si salvat antet factura!');
    }
    
    if (!$frmValues['produs_id'])
    {
        return alert('Selectati produsul pe care se face intrarea!');
    }
	
	$factura = new FacturiIntrari($factura_id);
	
    if (can_be_modified($factura_id) == FALSE)
        return alert("Nir-ul are asociate loturi de pe care s-a consumat, deci nu poate fi modificat.");
        
    $produs = new ViewProduseGestiuni("where produs_id = '".$frmValues['produs_id']."' and gestiune_id = '".$_SESSION['user']->gestiune_id."'");
    	
    $continut = new FacturiIntrariContinut($frmValues);
    $continut -> factura_intrare_id = $factura_id;
	
	//informatii despre produs
    $continut -> pret_vanzare = $produs->pret_ron;
    $continut -> tip_produs = $produs->tip_produs;
    $continut -> unitate_masura_id = $produs->unitate_masura_id;
	
	if($continut -> pret_ach_ron) {
		$continut -> calculeazaTotaluriRon();
	} else {
		if($continut -> pret_ach_val) {
			$continut -> pret_ach_ron = $continut -> pret_ach_val * $factura -> curs_valutar;
			$continut -> calculeazaTotaluriRon();
		}
	}
	
	if($continut -> pret_ach_val) {
		$continut -> calculeazaTotaluriVal();
	} else {
		if($continut -> pret_ach_ron && $factura -> curs_valutar) {
			$continut -> pret_ach_val = douazecimale($continut -> pret_ach_ron / $factura -> curs_valutar);
			$continut -> calculeazaTotaluriVal();
		}
	}
	
    $continut -> save();
    
    $objResponse = lista($factura_id);
    copyResponse($objResponse, frmComponenta(0, $factura_id));
    return $objResponse;
}

function stergeComponenta($continut_id)
{
    if (!$continut_id) {
        return alert('Selectati o componenta!');
    }
    
    $continut = new FacturiIntrariContinut($continut_id);
    $factura_id = $continut->factura_intrare_id;
    
    if (can_be_modified($factura_id) == FALSE)
        return alert("Nir-ul are asociate loturi de pe care s-a consumat, deci nu poate fi modificat.");
        
    $continut->delete();
    
    $objResponse = lista($factura_id);
    copyResponse($objResponse, frmComponenta(0, $factura_id));
    return $objResponse;
}

function lista($factura_id)
{
    $factura = new FacturiIntrari($factura_id);
    $objResponse = new xajaxResponse();
    $continutFactura = $factura->continut->lista("", "xajax_frmComponenta('<%continut_id%>', '<%factura_intrare_id%>');", $selected);
    $objResponse->assign("grid", "innerHTML", $continutFactura);
    $objResponse->assign("div_preview_factura", "innerHTML", $continutFactura);
    
    $objResponse->assign("txt_total", "value", $factura->totalFaraTva());
    $objResponse->assign("txt_total_tva", "value", $factura->totalTva());
    $objResponse->assign("txt_total_factura", "value", $factura->totalFactura());
    
	switch($factura -> tert -> tip) {
		case "intern": {
			$objResponse->assign("txt_total_val", "value", douazecimale($factura->totalCuDiscount()));
   			$objResponse->assign("txt_total_tva_val", "value", douazecimale($factura->totalTvaCuDiscount()));
    		$objResponse->assign("txt_total_factura_val", "value", douazecimale($factura->totalFacturaCuDiscount()));
		}break;
		case "extern_ue": {
			$objResponse->assign("txt_total_val", "value", douazecimale($factura->totalFaraTvaValuta()));
   			$objResponse->assign("txt_total_tva_val", "value", douazecimale($factura->totalTvaValuta()));
    		$objResponse->assign("txt_total_factura_val", "value", douazecimale($factura->totalFacturaValuta()));
		}break;
	}
   
    return $objResponse;
}

function inchideFactura($factura_id)
{
    $factura = new FacturiIntrari($factura_id);
	$factura -> salveazaTotaluri();
	
	if(!$factura -> valideazaMasaAmbalaje()) {
		return alert('
		<strong>Atentie!</strong><br>
		Masa Bruta: '. $factura -> masa_totala_bruta .' Kg<br>
		Masa Neta: '. $factura -> masa_totala_neta .' Kg<br>		
		Ambalaje pe factura: '. douazecimale($factura -> calculeazaMasaAmbalaje()) .' Kg<br>
		Ambalaje de introdus: '. douazecimale($factura -> masa_totala_bruta - $factura -> masa_totala_neta - $factura -> calculeazaMasaAmbalaje()) .' Kg<br>
		');
	}
	
    $dialog = new Dialog(800, 600, "", "win_inchide_factura");
    $dialog->title = "Salvare Factura";
    $dialog->append($factura->sumar());
    $dialog->append('
	<fieldset>
	<legend>Continut NIR</legend>
	<div style="height:300px;overflow:scroll; overflow-x:hidden;">
	'.$factura->continut->lista().'
	</div>
	</fieldset>
	');
	$dialog->addButton("FACTURA CHELTUIELI", "<%close%>;xajax_salveazaFactura(".$factura->id.");");
    $dialog->addButton("VALIDARE NIR", "<%close%>;xajax_genereazaNir(".$factura->id.");");
    $dialog->addButton("CONTINUA EDITARE", "<%close%>");
    $objResponse = openDialog($dialog);
    return $objResponse;
}

function genereazaNir($factura_id)
{
    if (can_be_modified($factura_id) == FALSE)
        return alert("Nir-ul are asociate loturi de pe care s-a consumat, deci nu poate fi modificat.");
        
    $factura = new FacturiIntrari($factura_id);
    
	// dc este o editare de nir, sterg loturile introduse pe nirul vechi
    if ($factura->salvat == 'DA')
    {
        $nir_vechi = new Niruri(" where factura_intrare_id = $factura_id");
        $nir_vechi->stergeLoturi();
        $nir_vechi->delete();
    }
    
    $factura->salvat = 'DA';
    $factura->save();
    $factura -> salveazaTotaluri();
	
    $nir = new Niruri();
    $nir->genereazaNir($factura_id);
    // dc este o editare de factura cu nir, am grija sterg toate loturile si adaug din nou
    $nir->genereazaLoturi();
	
    $objResponse = new xajaxResponse();
    if ($factura->tert->tip == "extern_ue")
    {
        $continut_intrastat = new ContinutIntrastat();
        $continut_intrastat->genereazaContinut($factura_id);
        copyResponse($objResponse, switchTab("intrastat"));
        $objResponse->assign("grid_intrastat", "innerHTML", $continut_intrastat->listaEditare());
    }
    else
    {
        $objResponse->assign("intrastat", "innerHTML", "Nu se aplica la facturile emise de furnizorii interni!");
    }

    
    $dialog = new Dialog(800, 600, "", "win_sumar_nir");
    $dialog->title = "Info NIR";
	$dialog -> close = false;
	$dialog -> modal = true;
    $dialog->append("NIR-ul a fost salvat!");
	$dialog->addButton("Adauga NIR nou", "xajax_location('".DOC_ROOT."intrari/introducere_factura/');");
    $dialog->addButton("Evidenta NIR-uri", "xajax_location('".DOC_ROOT."niruri/evidenta_niruri/');");
    $dialog->addButton("ADAUGA PLATA", "xajax_location('".DOC_ROOT."situatii_financiare/furnizori/tert.php?tert_id=".$factura -> tert_id."&societate_id=".$factura -> societate_id."&action=plata&factura_id=". $factura -> id ."');");
	$dialog->addButton("Tipareste NIR", "xajax_xPrintNir('".$nir->id."');");
	
	copyResponse($objResponse, $dialog->open());
	$objResponse -> script("is_saved = true;");
    return $objResponse;
}

function genereazaContinutIntrastat($factura_id)
{
}

function salveazaFactura($factura_id)
{
    $factura = new FacturiIntrari($factura_id);
    $factura->salvat = 'DA';
	$factura->cheltuieli = 'DA';
    $factura->save();
	
	$objResponse = new xajaxResponse();
    $dialog = new Dialog(800, 600, "", "win_sumar_nir");
    $dialog->title = "Info NIR";
	$dialog -> modal = true;
	$dialog -> close = false;
    $dialog -> append("Am salvat factura de cheltuieli!");
	$dialog -> addButton("ADAUGA PLATA", "xajax_location('".DOC_ROOT."situatii_financiare/furnizori/tert.php?tert_id=".$factura -> tert_id."&societate_id=".$factura -> societate_id."&action=plata&factura_id=". $factura -> id ."');");
    $dialog -> addButton("EVIDENTA FACTURI", "window.location.href = '".DOC_ROOT."intrari/evidenta_facturi/';");
	copyResponse($objResponse, $dialog -> open());
	$objResponse -> script("is_saved = true;");
    return $objResponse;
}

function calculator($frm, $factura_id, $mod = "pret_ach_ron")
{
    $factura = new FacturiIntrari($factura_id);
    $cota_tva = new CoteTva($frm['cota_tva_id']);
    $objResponse = new xajaxResponse();
    $response = array();
    $produs = new Produse($frm['produs_id']);
	if(count($produs)) {
		$ambalare = 1;
	} else {
		$ambalare = 1;
	}
    switch ($mod)
    {
        case "cantitate":
            {
            	$response['cantitate_bax'] = ($frm['cantitate'] / $ambalare);	
                $response['val_ach_ron'] = ($frm['cantitate'] * $frm['pret_ach_ron']);
                $response['val_ach_val'] = ($frm['cantitate'] * $frm['pret_ach_val']);
				if($frm['pret_ach_ron']) $response['pret_ron_cu_tva'] = douazecimale(($frm['pret_ach_ron'] * (100 + $cota_tva -> valoare))/(100));
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				$response['val_cu_tva_ron'] = ($response['pret_ron_cu_tva']*$frm['cantitate']);
            }break;
		case "cantitate_bax":
            {
            	$response['cantitate_bax'] = ($frm['cantitate_bax']);
				$frm['cantitate'] = ($response['cantitate_bax'] * $ambalare);
				$response['cantitate'] = $frm['cantitate'];	
                $response['val_ach_ron'] = ($frm['cantitate'] * $frm['pret_ach_ron']);
                $response['val_ach_val'] = ($frm['cantitate'] * $frm['pret_ach_val']);
				if($frm['pret_ach_ron']) $response['pret_ron_cu_tva'] = douazecimale(($frm['pret_ach_ron'] * (100 + $cota_tva -> valoare))/(100 ));
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				$response['val_cu_tva_ron'] = ($response['pret_ron_cu_tva']*$frm['cantitate']);
            }break;	
        case "pret_ach_ron":
            {
                $response['pret_ach_ron'] = $frm['pret_ach_ron'];
				$response['pret_ach_bax'] = $frm['pret_ach_ron']*$ambalare;
				$response['pret_ron_cu_tva'] = (($frm['pret_ach_ron'] * (100 + $cota_tva -> valoare))/(100 ));
				$response['pret_cu_tva_bax'] = ($response['pret_ron_cu_tva']*$ambalare);
				
                $response['pret_ach_val'] = ($frm['pret_ach_ron'] / $factura->curs_valutar);
                $response['val_ach_ron'] = ($frm['cantitate'] * $frm['pret_ach_ron']);
                $response['val_ach_val'] = ($frm['cantitate'] * $response['pret_ach_val']);
                
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				
				$response['val_cu_tva_ron'] = ($response['pret_ron_cu_tva']*$frm['cantitate']);
            }break;
        case "pret_ach_bax":
            {
            	$frm['pret_ach_ron'] = $frm['pret_ach_bax'] / $ambalare;
                $response['pret_ach_ron'] = ($frm['pret_ach_ron']);
				$response['pret_ron_cu_tva'] = (($frm['pret_ach_ron'] * (100 + $cota_tva -> valoare))/(100 ));
				$response['pret_cu_tva_bax'] = ($response['pret_ron_cu_tva']*$ambalare);
				
                $response['pret_ach_val'] = ($frm['pret_ach_ron'] / $factura->curs_valutar);
                $response['val_ach_ron'] = ($frm['cantitate'] * $frm['pret_ach_ron']);
                $response['val_ach_val'] = ($frm['cantitate'] * $response['pret_ach_val']);
                
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				
				$response['val_cu_tva_ron'] = ($response['pret_ron_cu_tva']*$frm['cantitate']);
            }break;			
        case "pret_ron_cu_tva":
            {
				$response['pret_ron_cu_tva'] = $frm['pret_ron_cu_tva'];
                $response['pret_ach_ron'] = (($frm['pret_ron_cu_tva'] * (100))/(100 + $cota_tva -> valoare));
                $response['pret_ach_val'] = ($response['pret_ach_ron'] / $factura->curs_valutar);
                $response['val_ach_ron'] = ($frm['cantitate'] * $response['pret_ach_ron']);
                $response['val_ach_val'] = ($frm['cantitate'] * $response['pret_ach_val']);
                
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				
				$response['val_cu_tva_ron'] = ($response['pret_ron_cu_tva']*$frm['cantitate']);
            }
            break;
        case "pret_ach_val":
            {
                $response['pret_ach_val'] = $frm['pret_ach_val'];
                $response['pret_ach_ron'] = ($frm['pret_ach_val'] * $factura->curs_valutar);
                $response['val_ach_ron'] = ($frm['cantitate'] * $response['pret_ach_ron']);
                $response['val_ach_val'] = ($frm['cantitate'] * $frm['pret_ach_val']);
                
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
            }break;
        case "val_ach_ron":
            {
                $response['val_ach_ron'] = $frm['val_ach_ron'];
                $response['val_ach_val'] = ($frm['val_ach_ron'] / $factura->curs_valutar);
                $response['pret_ach_ron'] = ($frm['val_ach_ron'] / $frm['cantitate']);
				$response['pret_ach_bax'] = (($frm['val_ach_ron'] / $frm['cantitate']) * $ambalare);
                $response['pret_ach_val'] = ($response['val_ach_val'] / $frm['cantitate']);
				$response['pret_ron_cu_tva'] = (($response['pret_ach_ron'] * (100 + $cota_tva -> valoare))/(100 ));
               	$response['pret_cu_tva_bax'] = ($response['pret_ron_cu_tva'] * $ambalare);	
			    $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				$response['val_cu_tva_ron'] = ($response['pret_ron_cu_tva']*$frm['cantitate']);
            }break;
        case "val_ach_val":
            {
                $response['val_ach_val'] = $frm['val_ach_val'];
                $response['val_ach_ron'] = ($frm['val_ach_val'] * $factura->curs_valutar);
                $response['pret_ach_ron'] = ($response['val_ach_ron'] / $frm['cantitate']);
                $response['pret_ach_val'] = ($frm['val_ach_val'] / $frm['cantitate']);
                
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
            }break;
        case "val_cu_tva_ron":
            {
                $response['val_cu_tva_ron'] = $frm['val_cu_tva_ron'];
				$response['val_ach_ron'] = (($response['val_cu_tva_ron'] * 100) / (100 + $cota_tva -> valoare));
                $response['val_ach_val'] = ($response['val_ach_ron'] / $factura->curs_valutar);
                $response['pret_ach_ron'] = ($response['val_ach_ron'] / $frm['cantitate']);
                $response['pret_ach_val'] = ($response['val_ach_val'] / $frm['cantitate']);
				$response['pret_ron_cu_tva'] = (($response['pret_ach_ron'] * (100 + $cota_tva -> valoare))/(100 ));
                $response['val_tva_ron'] = (($response['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($response['val_ach_val'] * $cota_tva->valoare) / 100);
				
            }break;
        case "val_tran_ron":
            {
                $response['val_tran_ron'] = $frm['val_tran_ron'];
                $response['val_tran_val'] = ($frm['val_tran_ron'] / $factura->curs_valutar);
                
                $response['val_tva_ron'] = (($frm['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($frm['val_ach_val'] * $cota_tva->valoare) / 100);
            }break;
        case "val_tran_val":
            {
                $response['val_tran_val'] = $frm['val_tran_val'];
                $response['val_tran_ron'] = ($frm['val_tran_val'] * $factura->curs_valutar);
                
                $response['val_tva_ron'] = (($frm['val_ach_ron'] * $cota_tva->valoare) / 100);
                $response['val_tva_val'] = (($frm['val_ach_val'] * $cota_tva->valoare) / 100);
            }break;
    }
    foreach ($response as $key=>$value)
    {
        $objResponse->assign($key, "value", douazecimale($value));
    }
    return $objResponse;
}

function calculeazaCoteTransport($factura_id, $cota_transport_total)
{
    $continut = new ContinutIntrastat("where `factura_intrare_id` = '$factura_id'");
    $continut->calculeazaCoteTransport($cota_transport_total);
    $objResponse = new xajaxResponse();
    $continut = new ContinutIntrastat("where `factura_intrare_id` = '$factura_id'");
    $objResponse->assign("grid_intrastat", "innerHTML", $continut->listaEditare());
    return $objResponse;
    
}

function salveazaDateIntrastat($factura_id, $frm, $frmModCalcul)
{
    $nr_r = count($frm['continut_id']);
    
    //validari
    for ($i = 0; $i < $nr_r; $i++)
    {
        if ( empty($frm['masa_neta'][$i]))
        {
            return alert("Nu ati introdus Masa neta in KG pentru toate categoriile NC8!");
        }
        if (!is_numeric($frm['masa_neta'][$i]))
        {
            return alert("Masa Neta in KG trebuie sa aiba valori numerice!");
        }
    }

    
    for ($i = 0; $i < $nr_r; $i++)
    {
        $cont = new ContinutIntrastat($frm['continut_id'][$i]);
        $cont->masa_neta = $frm['masa_neta'][$i];
        if ($frmModCalcul['mod_calcul'] == "manual")
        {
            $cont->val_transport_ron = $frm['val_transport_ron'][$i];
            $cont->val_statistica_ron = $cont->val_facturata_ron - $frm['val_transport_ron'][$i];
        }
        $cont->tara_origine = $frm['tara_origine'][$i];
        $cont->tara_expediere = $frm['tara_expediere'][$i];
        $cont->save();
    }
    
    if ($frmModCalcul['mod_calcul'] == "automat")
    {
        $continut = new ContinutIntrastat("where `factura_intrare_id` = '$factura_id'");
        $continut->calculeazaCoteTransport($frmModCalcul['cota_transport_total']);
    }
    $objResponse = new xajaxResponse();
    $continut = new ContinutIntrastat("where `factura_intrare_id` = '$factura_id'");
    $objResponse->assign("grid_intrastat", "innerHTML", $continut->listaEditare());
    return $objResponse;
}

/**
 * converteste um primita in um gestiune
 *
 *  daca se primeste produsul cu o alta um, se transforma in um din baza
 * @return
 */
function calculeaza_cantitate()
{
    $objResponse = new xajaxResponse();
    
    //instantiez obiectul jquery dialog
    $dialog = new Dialog(400, 350, '', 'win_compute_qty');
    $dialog->title = "Calculeaza cantitate";
    
    //--------- construiesc elementele html componente ale dialog-ului --------------
    
    //codul js pentru onkeyup, in input text-ul de cautare a produsului
    $js_code = "switch(event.keyCode) 
				{
            			case 40:					
            				$('#sel_produse').attr('selectedIndex', 0);
            				$('#sel_produse').focus();						
    				        break;
    				     default: 
    				        OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filter_products', 100);
						break;
				}";
				
    // input text pentru calcul conversie cumparat-gestiune
    $html_code = "<p>
					Cantitate cumparata: <input type=\"text\" name=\"cant_cumparata\" id=\"cant_cumparata\">
				</p>
				<p>
					Cantitate ambalata: <input type=\"text\" name=\"cant_ambalata\" id=\"cant_ambalata\">
				</p>
				<p>
					Pret unitar fara TVA: <input type=\"text\" name=\"pret_cumparare\" id=\"pret_cumparare\">
				</p>
					Pret unitar cu TVA: <input type=\"text\" name=\"pret_cumparare_tva\" id=\"pret_cumparare_tva\">
				";

				
    //adaug codul html la dialog
    $dialog->append($html_code);
    
    // adaug buton de conversie
    $dialog->addButton("OK", "$('#cantitate').val($('#cant_cumparata').val() * $('#cant_ambalata').val());
						 $('#val_ach_ron').val($('#pret_cumparare').val() * $('#cant_cumparata').val());
						 xajax_calculator(xajax.getFormValues('frm_facturi_intrari_continut'), 
						$('#factura_intrare_id').val(), 
						'val_ach_ron');
						<%close%>");
						
    //adaug buton de inchidere
    $dialog->addButton("Renunta", "<%close%>");
    
    $objResponse = $dialog -> open();
    $objResponse -> script("
		$('#pret_cumparare_tva').change(
			function() {
			var tva = $('#cota_tva_id option:selected').text();
			var pret_fara_tva = ($(this).val() * 100) / ( 100*1 + tva*1);
			$('#pret_cumparare').val(pret_fara_tva.toFixed(2));
			}
		)
	");
    return $objResponse;
    
}


/**
 * verifica dc o factura poate fi modificata
 *
 * dc pentru o factura s-a generat nu nir, s-au generat implicit si loturile aferente
 * dc se modifica o factura (si implicit nirul acesteia) loturile trebuiesc regenerate
 * loturile se pot regenera doar dc nu s-a consumat din ele
 *
 * @param object $factura_id
 * @return
 */
function can_be_modified($factura_id)
{
    // obtin nirul asociat facturii
    $nir = new Niruri("where factura_intrare_id = $factura_id");
    
    if (count($nir) == 0)
        return TRUE; // nu s-a salvat nir pt factura, poate fi modificata
        
    // obtin loturile de pe nir, din care s-a consumat
    $loturi = new Loturi("where doc_id = ".$nir->nir_id." and doc_tip = 'nir' and cantitate_init > cantitate_ramasa");
    if (count($loturi) > 0)
        return FALSE; // s-a consumat, nu putem modifica
        
    return TRUE;
}
?>
