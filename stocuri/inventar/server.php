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
    
    if ($frmFiltre['inchis_filtru'])
    {
        $sql .= " and inchis=1";
    }
    
    if ($frmFiltre['data_doc_filtru'])
    {
        $sql .= " and DATEDIFF(data_inventar,'".data_c($frmFiltre['data_doc_filtru'])."')=0";
    }
    
    $sql .= " order by data_inventar desc";
    
    $model = new Inventare($sql);
    
    if ($frmPager['pagesize'] == 1)
        $frmPager['pagesize'] = count($model);
    $model->pageLength($frmPager['pagesize']);
    
    $info = paginator($action, $model, $frmPager['curentpage']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplay", "value", $info['pagedisplay']);
    $objResponse->assign("curentpage", "value", $info['curentpage']);
    $objResponse->assign("grid", "innerHTML", $info['page']->lista("", "xajax_frm('<%inventar_id%>')", $selected));
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function lista_content($inventar_id, $frmFiltreContent = array(), $frmPagerContent = array(), $action = "first", $selected = 0)
{
    if (!$inventar_id)
        return;
    
	$inv = new Inventare($inventar_id);    
    $produse_inventar = new InventarContinut(" WHERE inventar_id = $inventar_id LIMIT 1");
    
    //se adauga inventar nou
    if ($produse_inventar->count() == 0)
    {
        $gestiune_id = $_SESSION['user']->gestiune_id;
        $query = "
				insert into inventar_continut(inventar_id,produs_id,stoc_scriptic,stoc_faptic, pret_achizitie)
				select $inventar_id,pg.produs_id,stoc_la_data(pg.produs_id, pg.gestiune_id, '". $inv -> data_inventar ."'),
				stoc_la_data(pg.produs_id, pg.gestiune_id, '". $inv -> data_inventar ."'),
				stoc_la_data_valoric(pg.produs_id, pg.gestiune_id, '". $inv -> data_inventar ."') / stoc_la_data(pg.produs_id, pg.gestiune_id, '". $inv -> data_inventar ."')
				from view_produse_gestiuni pg
				where pg.gestiune_id = $gestiune_id and pg.tip_produs in ('marfa', 'mp')";
        global $db;
        $r = $db->query($query);
    }
    //return alert("aici");
    
    $sql = " INNER JOIN produse p on inventar_continut.produs_id = p.produs_id";
    
    if ($frmFiltreContent['produs_filtru'])
    {
        $sql .= " WHERE p.denumire LIKE '%".$frmFiltreContent['produs_filtru']."%'";
    }
    else
        $sql .= " WHERE 1";
        
    $sql .= " AND inventar_id= $inventar_id order by p.denumire asc ";
    
    $model = new InventarContinut();
    $model->prepareQuery($sql);
    
    if ($frmPagerContent['pagesizecontent'] == 1)
        $frmPagerContent['pagesizecontent'] = $model->expectedResult();
    //$model->pageLength($frmPagerContent['pagesizecontent']);
    if(!$frmPagerContent['curentpagecontent']) $frmPagerContent['curentpagecontent'] = 1;
	if(!$frmPagerContent['pagesizecontent']) $frmPagerContent['pagesizecontent'] = 30;
	
    $info = paginated($action, $model, $frmPagerContent['curentpagecontent'], $frmPagerContent['pagesizecontent']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplaycontent", "value", $info['pagedisplay']);
    $objResponse->assign("curentpagecontent", "value", $info['curentpage']);
    $objResponse->assign("grid_inventar_continut", "innerHTML", $info['page']->lista());
    //$objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}

function frm($inventar_id = 0)
{
    if ($inventar_id)
    {
        $model = new Inventare($inventar_id);
        
        if ($model->inchis == TRUE)
            return alert("Acest inventar a fost inchis si nu poate fi modificat.");
            
        $objResponse = new xajaxResponse();
        $objResponse->assign("inventar_id", "value", $inventar_id);
        $objResponse->assign("numar_doc", "value", $model->numar_doc);
        $objResponse->assign("data_inventar", "value", c_data($model->data_inventar));
        copyResponse($objResponse, switchTab('frm'));
        copyResponse($objResponse, lista_content($inventar_id, array("produs_filtru"=>""), array("pagesizecontent"=>"30", "curentpagecontent"=>""), 'first'));
        return $objResponse;
    }
    else
    {
        $objResponse = new xajaxResponse();
        copyResponse($objResponse, switchTab('frm'));
        $objResponse->assign("inventar_id", "value", "");
        $objResponse->assign("numar_doc", "value", "");
        $objResponse->assign("data_inventar", "value", "");
        $objResponse->assign("grid_inventar_continut", "innerHTML", "");
        return $objResponse;
    }
}

function cancel()
{
    $objResponse = new xajaxResponse();
    $objResponse->assign("inventar_id", "value", "");
    $objResponse->assign("numar_doc", "value", "");
    $objResponse->assign("data_inventar", "value", "");
    $objResponse->assign("grid_inventar_continut", "innerHTML", "");
    
    copyResponse($objResponse, switchTab('lista'));
    return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array(), $frmFiltreContent = array(), $frmPagerContent = array(), $action = "first")
{
    if (!$_SESSION['user'])
    {
        return alert("Date user nevalide");
    }
	
	if(!$frmValues['data_inventar'])
		return alert("Selectati data inventar.");
		
	//data trebuie sa fie mai recenta decat ultimul inventar
	$query = " WHERE gestiune_id=".$_SESSION['user']->gestiune_id;
    $query .= " ORDER BY data_inventar DESC";
	//$lastDateInventar = new Inventare($query);
	
	if(count($lastDateInventar))
	{
		if($frmValues['inventar_id']==0)
		{
			if(data_c($frmValues['data_inventar']) <= $lastDateInventar->data_inventar)
			{
				return alert("Alegeti o data mai recenta decat data ultimului inventar");
			}
		}
		else
		{
			if(data_c($frmValues['data_inventar']) < $lastDateInventar->data_inventar)
			{
				return alert("Alegeti o data mai recenta decat data ultimului inventar");
			}
		}
	}
	
	
    $model = new Inventare($frmValues);
    //get last nr_doc for insert
    if ($model->id == 0)
    {
        $query = " WHERE gestiune_id=".$_SESSION['user']->gestiune_id;
        $query .= " ORDER BY numar_doc DESC LIMIT 1";
        $lastInventar = new Inventare($query);
        if(count($lastInventar)) $model->numar_doc = $lastInventar->numar_doc + 1;
		else $model -> numar_doc = 1;
    }
    $model->gestiune_id = $_SESSION['user']->gestiune_id;
    $model->utilizator_id = $_SESSION['user']->user_id;
    $model->data_inventar = data_c($model->data_inventar, $frmFiltreContent, $frmPagerContent, $action);
    $model->save();
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, lista($frmFiltre, $frmPager, "default", $model->id));
    $objResponse->assign('inventar_id', 'value', $model->id);
 	$objResponse->assign('numar_doc', 'value', $model->numar_doc);
    copyResponse($objResponse, lista_content($model->id, $frmFiltreContent, $frmPagerContent, $action));
    return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array())
{

    $model = new Inventare($id);
	if($model -> inchis == 1) {
		$model -> anulareScaderi();
		$model -> anulareIntrari();
		$model -> inchis = "0";
		$model -> save();
	} else {
		$model->sterge();
	}
    
    $objResponse = lista($frmFiltre, $frmPager, "default");
    copyResponse($objResponse, switchTab('lista'));
    return $objResponse;
}

function save_content($id, $stoc_faptic)
{
    if ($id == "")
        return alert("Selectati un produs");
        
    if (is_numeric($stoc_faptic) == FALSE)
        return alert("Introduceti o valoare reala pt stoc");
        
    $continut = new InventarContinut($id);
    $continut->stoc_faptic = $stoc_faptic;
    $continut->save();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("diferenta_".$id, "innerHTML", treizecimale($continut -> stoc_faptic - $continut -> stoc_scriptic));
	return $objResponse;
}

function save_pret($id, $pret)
{ 
    if ($id == "") 
        return alert("Selectati un produs");
        
    if (is_numeric($pret) == FALSE)
        return alert("Introduceti o valoare reala pt pret");
        
    $continut = new InventarContinut($id);
    $continut->pret_achizitie = $pret;
    $continut->save();
	$objResponse = new xajaxResponse();
	return $objResponse;
}

function close_inventar($id)
{
    if ($id == "")
        return alert("Salvati un inventar");
    
	if(!$_SESSION['user']) {
		return xLogin();
	}    
    $inventar = new Inventare($id);
    $inventar -> anulareScaderi();
	
    //echilibrez stocurile
    $produse = new InventarContinut(" WHERE inventar_id=$id");
    $gestiune_id = $_SESSION['user']->gestiune_id;
    foreach ($produse as $produs_inventar)
    {
        if ($produs_inventar->stoc_scriptic > $produs_inventar->stoc_faptic)
        {
            //return alert($produs_inventar->id);
          $produs_inventar->produs->scadStoc($produs_inventar->stoc_scriptic - $produs_inventar->stoc_faptic, $gestiune_id, $produs_inventar->id, "InventarContinutIesiri");
        }
        else if ($produs_inventar->stoc_scriptic < $produs_inventar->stoc_faptic)
        {
        	$lot = new Loturi();
			$lot -> doc_id = $inventar -> id;
			$lot -> gestiune_id = $inventar -> gestiune_id;
			$lot -> societate_id = $inventar -> gestiune -> punct_lucru -> societate_id;
			$lot -> doc_comp_id = $produs_inventar -> id;
			$lot -> doc_tip = "inventar";
			$lot -> produs_id = $produs_inventar -> produs_id;
			$lot -> cantitate_init =  $produs_inventar->stoc_faptic - $produs_inventar->stoc_scriptic;
			$lot -> cantitate_ramasa = $produs_inventar->stoc_faptic - $produs_inventar->stoc_scriptic;
			$lot -> pret_intrare_ron = $produs_inventar -> pret_achizitie;
			$lot -> data_intrare = $inventar -> data_inventar;
			$lot -> tip_lot = "inventar";
			$lot -> save();
        }
    }
    
    //inchid inventarul
    $inventar->inchis = 1;
    $inventar->save();
    
  
    return alert('Inventarul a fost salvat. Stocurile au fost actualizate!');
}

function listeaza($inventar_id)
{
    if ($inventar_id == 0)
        return alert("Selectati un inventar.");
        
    $objResponse = new xajaxResponse();
    $p = new InventarPrint($inventar_id);
    
    $objResponse->assign('print_inventar', 'innerHTML', $p->getHtml());
    $objResponse->script("$('#tabs').tabs('enable', 2);");
    copyResponse($objResponse, switchTab('print_preview'));
	$objResponse -> script("CallPrintContent('print_inventar');");
    return $objResponse;
}

?>
