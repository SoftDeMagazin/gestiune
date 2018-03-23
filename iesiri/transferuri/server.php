<?php 
session_start();
header("Cache-control: private"); // IE 6 Fix
require_once ("common.php");
$xajax->processRequest();

function load($transfer_id = NULL)
{
    $objResponse = new xajaxResponse();
    
    if ($transfer_id)
    {
        $transfer = new Transferuri($transfer_id);
        $objResponse = lista($transfer_id);
        $objResponse->assign('transfer_id', 'value', $transfer->transfer_id);
        $objResponse->assign('data', 'value', c_data($transfer->data));
        $objResponse->assign('gestiune_sursa_id', 'value', $transfer->gestiune_sursa_id);
        $objResponse->assign('gestiune_destinatie_id', 'value', $transfer->gestiune_destinatie_id);
        $objResponse->script("$('#tabs').tabs('enable', 1);");
    }
    else
    {
        $transfer = new Transferuri();
    }
    
    return $objResponse;
}

function showWorkingPoints($shoudlShow)
{
    $objResponse = new xajaxResponse();
    if ($shoudlShow == true)
    {
        $html = "Punct de lucru <br>";
        
        $puncte_lucru = new PuncteLucru("where 1 order by denumire asc");
        $html .= $puncte_lucru->select_single("xajax_filterByWorkingPoint(document.getElementById('punct_lucru_id').value)");
        
        $objResponse->assign("outsideDestinations", "innerHTML", $html);
    }
    else
    {
        $objResponse->assign("outsideDestinations", "innerHTML", "");
    }
    return $objResponse;
}

function filterByWorkingPoint($punct_lucru_id)
{
    $objResponse = new xajaxResponse();
    $query = "";
    
    if ($punct_lucru_id == 0)
    {
        $query = "WHERE 1 ORDER BY denumire ASC";
    }
    else
    {
        $query = "WHERE punct_lucru_id=".$punct_lucru_id;
    }
    
    $gestiuni = new Gestiuni($query);
    $objResponse->assign("gestiune_dest", "innerHTML", $gestiuni->select("", "gestiune_destinatie_id"));
    
    return $objResponse;
}

function saveHeader($frmValues)
{
    if ($frmValues['gestiune_sursa_id'] == $frmValues['gestiune_destinatie_id'])
        return alert("Gestiunea sursa trebuie sa fie diferita de gestiunea destinatie.");
        
    $objResponse = new xajaxResponse();
    
    $transfer = new Transferuri($frmValues);
    if ($trasfer->data)
        $transfer->data = data_c($transfer->data);
    $transfer->save();
    
    $objResponse->script("$('#tabs').tabs('enable', 1);");
    copyResponse($objResponse, switchTab('frm'));
    $objResponse->assign('transfer_id', 'value', $transfer->id);
    $objResponse->script("\$('#cautare_produs').focus().select();");
    return $objResponse;
}

function filterProducts($filtru)
{
    if ($filtru)
    {
        $produse = new Produse("where denumire like '$filtru%' order by denumire asc");
        $objResponse = new xajaxResponse();
        $objResponse->assign("div_select_produse", "innerHTML", $produse->select());
        return $objResponse;
    }
    else
    {
        $objResponse = new xajaxResponse();
        return $objResponse;
    }
}

function selectProdus($produs_id)
{
    $produs = new Produse($produs_id);
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", $produs->id);
    $objResponse->assign("div_produs", "innerHTML", $produs->denumire);
    $objResponse->assign("um", "value", $produs->unitate_masura->denumire);
    $objResponse->assign("div_detalii_produs", "innerHTML", "<strong>Ambalare:</strong>".$produs->ambalare."</br> <strong>Stoc: 0.000</strong>");
    $objResponse->assign("div_frm_unitate_masura", "innerHTML", $produs->unitate_masura());
    $objResponse->script("\$('#cantitate').focus().select();");
    return $objResponse;
}

function editComponent($component_id)
{
    $objResponse = new xajaxResponse();
    $component = new TransferuriComponente($component_id);
    
    $objResponse->assign("transfer_componenta_id", "value", $component->transfer_componenta_id);
    $objResponse->assign("produs_id", "value", $component->produs_id);
    $objResponse->assign("div_produs", "innerHTML", $component->produs->denumire);
    $objResponse->assign("cantitate", "value", $component->cantitate);
    
    return $objResponse;
}

function saveComponent($frmValues, $transfer_id)
{
    if (!$transfer_id)
    {
        return alert('Introduceti si salvati antet transfer!');
    }
    
    if (!$frmValues['produs_id'])
    {
        return alert('Selectati produsul pe care se face intrarea!');
    }
    
    if (!$frmValues['cantitate'])
    {
        return alert('Completati cantitatea!');
    }
    
    $componenta = new TransferuriComponente($frmValues);
    $componenta->transfer_id = $transfer_id;
    $componenta->save();
    
    $objResponse = lista($transfer_id);
    $objResponse->assign("div_produs", "innerHTML", "");
    $objResponse->assign("div_detalii_produs", "innerHTML", "");
    $objResponse->assign("cantitate", "value", "");
    return $objResponse;
    
}

function deleteComponent($componenta_id)
{
    $componenta = new TransferuriComponente($componenta_id);
    $transfer_id = $componenta->transfer_id;
    $componenta->delete();
    
    $objResponse = lista($transfer_id);
    return $objResponse;
}

function lista($transfer_id)
{
    $componente = new TransferuriComponente("WHERE transfer_id=".$transfer_id);
    $objResponse = new xajaxResponse();
    
    $continutTransfer = $componente->lista("", "xajax_frmComponenta('<%transfer_componenta_id%>');", $selected);
    
    $objResponse->assign("grid", "innerHTML", $continutTransfer);
    $objResponse->assign("div_preview_factura", "innerHTML", $continutTransfer);
    $objResponse->script("\$('.tablesorter').tablesorter();");
    
    return $objResponse;
}

function saveTransfer($frmValues)
{
    if (!$frmValues['transfer_id'])
    {
        return alert('Introduceti si salvati antet transfer!');
    }
    
    $transfer = new Transferuri($frmValues);
    $transfer->salvat = 1;
    $transfer->data = data_c($transfer->data);
    $transfer->save();
    $objResponse = new xajaxResponse();
    return $objResponse;
}

function validateTransfer($frmValues)
{
    if (!$frmValues['transfer_id'])
    {
        return alert('Introduceti si salvati antet transfer!');
    }
    
    $transfer = new Transferuri($frmValues);
    $transfer->valid = 1;
    $transfer->data = data_c($transfer->data);
    $transfer->save();
    $objResponse = new xajaxResponse();
    return $objResponse;
}

function decreaseStocks($transfer_id)
{
    $componente = new TransferuriComponente("WHERE transfer_id=".$transfer_id);
    foreach ($componente as $comp)
    {
    
    }
}

?>
