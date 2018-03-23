<?php 
header("Cache-control: private"); // IE 6 Fix
require_once ("common.php");
$xajax->processRequest();

function lista($frmFiltre = array(), $frmPager = array(), $action = "first", $selected = 0)
{
    $sql = "WHERE gestiune_id=".$_SESSION['user']->gestiune_id;
    
    if ($frmFiltre['nr_doc_filtru'])
    {
        $sql .= " and numar_doc = ".$frmFiltre['nr_doc_filtru'];
    }
    
    if ($frmFiltre['utilizator_id'])
    {
        $in = implode(",", $frmFiltre['utilizator_id']);
        $sql .= " and utilizator_id in (".$in.")";
    }
    
    if ($frmFiltre['realizat_filtru'])
    {
        $sql .= " and realizat=1";
    }
    
    if ($frmFiltre['data_doc_filtru'])
    {
        $sql .= " and DATEDIFF(data,'".data_c($frmFiltre['data_doc_filtru'])."')=0";
    }
    
    $sql .= " order by data";
    
    $model = new NecesarAprovizionare($sql);
    
    if ($frmPager['pagesize'] == 1)
        $frmPager['pagesize'] = count($model);
    $model->pageLength($frmPager['pagesize']);
    
    $info = paginator($action, $model, $frmPager['curentpage']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplay", "value", $info['pagedisplay']);
    $objResponse->assign("curentpage", "value", $info['curentpage']);
    $objResponse->assign("grid", "innerHTML", $info['page']->lista("", "xajax_frm('<%necesar_aprovizionare_id%>')", $selected));
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function lista_retete($necesar_id, $frmFiltreContent = array(), $frmPagerContent = array(), $action = "first", $selected = 0)
{
    if (!$necesar_id)
        return;
        
    $model = new NecesarAprovizionareContinut(" WHERE doc_id = $necesar_id");
    
    if ($frmPagerContent['pagesizecontent'] == 1)
        $frmPagerContent['pagesizecontent'] = count($model);
    $model->pageLength($frmPagerContent['pagesizecontent']);
    
    $info = paginator($action, $model, $frmPagerContent['curentpagecontent']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplaycontent", "value", $info['pagedisplay']);
    $objResponse->assign("curentpagecontent", "value", $info['curentpage']);
    if (count($info['page']))
        $objResponse->assign("grid_necesar_retete", "innerHTML", $info['page']->lista("", "xajax_edit_recipe('<%nac_id%>')", $selected));
    else
        $objResponse->assign("grid_necesar_retete", "innerHTML", "");
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function lista_mp($necesar_id, $frmFiltreContent = array(), $frmPagerContent = array(), $action = "first", $selected = 0)
{
    if (!$necesar_id)
        return;
        
    $model = new NecesarAprovizionareContinutMp(" WHERE doc_id = $necesar_id");
    
    if ($frmPagerContent['pagesize_mp'] == 1)
        $frmPagerContent['pagesize_mp'] = count($model);
    $model->pageLength($frmPagerContent['pagesize_mp']);
    
    $info = paginator($action, $model, $frmPagerContent['curentpage_mp']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplay_mp", "value", $info['pagedisplay']);
    $objResponse->assign("curentpage_mp", "value", $info['curentpage']);
    if (count($info['page']))
    {
        $objResponse->assign("grid_necesar_mp", "innerHTML", $info['page']->lista($necesar_id));
        //return alert($info['page']->lista($necesar_id));
    }
    else
        $objResponse->assign("grid_necesar_mp", "innerHTML", "");
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function frm($necesar_id = 0)
{
    if ($necesar_id)
    {
        $model = new NecesarAprovizionare($necesar_id);

        
        $objResponse = new xajaxResponse();
        $objResponse->assign("necesar_aprovizionare_id", "value", $necesar_id);
        $objResponse->assign("numar_doc", "value", $model->numar_doc);
        $objResponse->assign("data", "value", c_data($model->data));
        copyResponse($objResponse, switchTab('frm'));
        copyResponse($objResponse, lista_retete($necesar_id));
        copyResponse($objResponse, lista_mp($necesar_id));
        return $objResponse;
    }
    else
    {
        $objResponse = new xajaxResponse();
        copyResponse($objResponse, switchTab('frm'));
        $objResponse->assign("necesar_aprovizionare_id", "value", "");
        $objResponse->assign("numar_doc", "value", "");
        $objResponse->assign("data", "value", "");
        $objResponse->assign("grid_necesar_retete", "innerHTML", "");
        $objResponse->assign("grid_necesar_mp", "innerHTML", "");
        return $objResponse;
    }
}

function cancel()
{
    $objResponse = new xajaxResponse();
    $objResponse->assign("necesar_aprovizionare_id", "value", "");
    $objResponse->assign("numar_doc", "value", "");
    $objResponse->assign("data", "value", "");
    $objResponse->assign("grid_necesar_retete", "innerHTML", "");
    $objResponse->assign("grid_necesar_mp", "innerHTML", "");
    
    copyResponse($objResponse, switchTab('lista'));
    return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array(), $frmFiltreContent = array(), $frmPagerContent = array(), $action = "first")
{
    if (!$_SESSION['user'])
    {
        return alert("Date user nevalide");
    }
    
    $model = new NecesarAprovizionare($frmValues);
    
    //dc avem un document nou
    if ($model->id == 0)
    {
        //get numar_doc
        $query = " WHERE gestiune_id=".$_SESSION['user']->gestiune_id;
        $query .= " ORDER BY numar_doc DESC LIMIT 1";
        $lastInventar = new NecesarAprovizionare($query);//am obtinut numarul ultimului doc de acest tip
        if ($lastInventar->count() == 0)//dc nu sunt alte doc, incepem de la 1
            $model->numar_doc = 1;
        else
            $model->numar_doc = $lastInventar->numar_doc + 1;//altfel incrementam
            
        $model->gestiune_id = $_SESSION['user']->gestiune_id;
        $model->utilizator_id = $_SESSION['user']->user_id;
    }
    
    $model->data = data_c($model->data);
    $model->save();
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, lista($frmFiltre, $frmPager, "default", $model->id));
    $objResponse->assign('necesar_aprovizionare_id', 'value', $model->id);
    $objResponse->assign('numar_doc', 'value', $model->numar_doc);
    return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array())
{
    $model = new NecesarAprovizionare($id);
    $model->delete();
    $objResponse = lista($frmFiltre, $frmPager, "default");
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
        $produse = new Produse("WHERE 1");
        $objResponse = new xajaxResponse();
        $objResponse->assign("div_select_produse", "innerHTML", $produse->select());
        return $objResponse;
    }
}

function selectProdus($produs_id)
{
    $produs = new Produse($produs_id);
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", $produs->id);
    $objResponse->assign("produs", "value", $produs->denumire);
    $objResponse->assign("um", "value", $produs->unitate_masura->denumire);
	$objResponse->assign("cantitate", "value", "");
    $objResponse->script("\$('#cantitate').focus().select();");
    return $objResponse;
}

function save_product($doc_id, $frmValues = array())
{
    if (!$doc_id)
        return alert("Salvati antetul.");
        
    $product_id = $frmValues['produs_id'];
    $cantitate = $frmValues['cantitate'];
    
    if (!$product_id)
        return alert("Selectati un produs.");
    if (!$cantitate)
        return alert("Introduceti cantitatea.");
	if(is_numeric($cantitate) == false)
		return alert("Completati o valoare reala pentru cantitate.");
        
    //save recipe
    $continut_reteta = new NecesarAprovizionareContinut($frmValues);
    $continut_reteta->produs_id = $product_id;
    $continut_reteta->cantitate_dorita = $cantitate;
    $continut_reteta->doc_id = $doc_id;
    $continut_reteta->save();
    
    //save components
    save_product_comp($product_id, $doc_id, $continut_reteta->id, $cantitate);
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, lista_retete($doc_id));
    copyResponse($objResponse, lista_mp($doc_id));
	copyResponse($objResponse, cancel_product());
    return $objResponse;
}

function save_product_comp($product_id, $doc_id, $nac_id, $cantitate)
{
    $produs = new Produse("WHERE produs_id=$product_id");
    $mps = $produs->getMateriiPrime($cantitate);
    foreach ($mps as $mp)
    {
        $sql = " WHERE produs_id =".$mp['produs_id'];
        $sql .= " AND doc_id = $doc_id AND nac_id = $nac_id";
        $necesar_mp = new NecesarAprovizionareContinutMp($sql);
        $stoc = new Stocuri("WHERE produs_id=".$mp['produs_id']);
        $stoc_mp = 0;
        if ($stoc->count() == 1)
        {
            $stoc_mp = $stoc->stoc;
        }
        
        if ($necesar_mp->count() == 1)
        {
            $necesar_mp->cantitate_necesara = $mp['cantitate'];
            $necesar_mp->stoc = $stoc_mp;
            $necesar_mp->save();
        }
        else
        {
            $necesar_mp = new NecesarAprovizionareContinutMp();
            $necesar_mp->produs_id = $mp['produs_id'];
            $necesar_mp->cantitate_necesara = $mp['cantitate'];
            $necesar_mp->doc_id = $doc_id;
            $necesar_mp->nac_id = $nac_id;
			$necesar_mp->stoc = $stoc_mp;
            $necesar_mp->save();
        }
        
    }
}

function cancel_product()
{
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", "");
    $objResponse->assign("produs", "value", "");
    $objResponse->assign("um", "value", "");
    $objResponse->assign("cantitate", "value", "");
	 $objResponse->assign("nac_id", "value", "");
    return $objResponse;
}

function edit_recipe($necesar_reteta_id)
{
    if (!$necesar_reteta_id)
        return alert("Selectati o reteta");
        
    $reteta = new NecesarAprovizionareContinut($necesar_reteta_id);
    if ($reteta->count() != 1)
        return;
        
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, selectProdus($reteta->produs_id));
    $objResponse->assign('cantitate', 'value', $reteta->cantitate_dorita);
    $objResponse->assign('nac_id', 'value', $reteta->nac_id);
    return $objResponse;
}

function delete_recipe($necesar_reteta_id, $doc_id)
{
    if (!$necesar_reteta_id)
        return alert("Selectati un produs");
        
    $reteta = new NecesarAprovizionareContinut($necesar_reteta_id);
    if ($reteta->count() != 1)
        return;
    $cantitate = $reteta->cantitate_dorita;
    $produs_id = $reteta->produs_id;
    $nac_id = $reteta->id;
    
    //delete recipe
    $reteta->delete();
    
    //decrease quant for components
    $produs = new Produse($produs_id);
    $mps = $produs->getMateriiPrime($cantitate);
    foreach ($mps as $mp)
    {
        $sql = " WHERE produs_id =".$mp['produs_id'];
        $sql .= " AND doc_id = $doc_id AND nac_id = $nac_id";
        $necesar_mp = new NecesarAprovizionareContinutMp($sql);
        if ($necesar_mp->count() == 0)
            continue;
            
        $necesar_mp->delete();
        
    }
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, lista_retete($doc_id));
    copyResponse($objResponse, lista_mp($doc_id));
    return $objResponse;
}

function listeaza($necesar_id)
{
    if ($necesar_id == 0)
        return alert("Selectati un necesar aprovizionare.");
        
    $objResponse = new xajaxResponse();
    $p = new NecesarAprovizionarePrint($necesar_id);
    
    $objResponse->assign('print_necesar', 'innerHTML', $p->getHtml());
    $objResponse->script("$('#tabs').tabs('enable', 2);");
    copyResponse($objResponse, switchTab('print_preview'));
    $objResponse->script("CallPrintContent('print_necesar');");
    return $objResponse;
}

?>
