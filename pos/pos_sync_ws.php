<?php 
define(DOC_ROOT, '../');
require_once ('../app/thirdparty/nusoap/nusoap.php');
require_once('../cfg.php');

$ns = 'urn:pos';

$server = new soap_server();
$server->configureWSDL('pos',$ns);
//$server->wsdl->schemaTargetNamespace=$ns;
//$server->soap_defencoding='utf-8';

// ====== WSDL TYPES DECLARATION ===================================

// ---------- Produs ---------------------------------

$server->wsdl->addComplexType(
    'Produs',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'produs_id' => array('name'=>'produs_id','type'=>'xsd:int'),
		'categorie_id' => array('name'=>'categorie_id','type'=>'xsd:int'),
		'denumire_categorie' => array('name'=>'denumire_categorie','type'=>'xsd:string'),
		'denumire' => array('name'=>'denumire','type'=>'xsd:string'),
		'pret_ron' => array('name'=>'pret_ron','type'=>'xsd:float'),
		'pret_val' => array('name'=>'pret_val','type'=>'xsd:float'),
    )
);

// ---------- Produs[] --------------------------------

$server->wsdl->addComplexType(
    'ProdusArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Produs[]')
    ),
    'tns:Produs'
);

// --------- SaleHeader ---------------------------------

$server->wsdl->addComplexType(
    'SaleHeader',
    'complexType',
    'struct',
    'all',
    '',
    array(
		'pos_id' => array('name'=>'nr_pos','type'=>'xsd:int'),       
		'data_economica' => array('name'=>'data_vanzare','type'=>'xsd:date'),
    )
);

// ------- Sale ----------------------------------------

$server->wsdl->addComplexType(
    'Sale',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'produs_id' => array('name'=>'produs_id','type'=>'xsd:int'),
		'pret_vanzare' => array('name'=>'pret_vanzare','type'=>'xsd:float'),
		'cantitate' => array('name'=>'cantitate','type'=>'xsd:float'),
    )
);

// ------ Sale[] -----------------------------------------

$server->wsdl->addComplexType(
    'SaleArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Sale[]')
    ),
    'tns:Produs'
);

// ========== WSDL METHOD REGISTRATION ==============================

$server->register(
	'has_modified_products',
	array('pos_id' => 'xsd:int'),
	array('return' => 'xsd:boolean'),
	$ns);
	
$server->register(
	'get_modified_products',
	array('pos_id' => 'xsd:int'),
	array('return' => 'tns:ProdusArray'),
	'urn:pos',                      // namespace
    'urn:pos#get_modified_products',                 // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'Returneaza Categoriile'            // documentation
);	
	
$server->register(
	'get_all_products',
	array('pos_id' => 'xsd:int'),
	array('return' => 'tns:ProdusArray'),
		'urn:pos',                      // namespace
    'urn:pos#get_all_products',                 // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'Returneaza Categoriile'            // documentation
);	

$server->register(
	'sync_completed',
	array('gestiune_id' => 'xsd:int'),
	array('return' => 'xsd:string'),
		'urn:pos',                      // namespace
    'urn:pos#sync_completed',                 // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'Returneaza Categoriile'            // documentation
);	
	
$server->register(
	'send_sales',
	array('sale_header' => 'tns:SaleHeader',
		'sale_details' => 'tns:SaleArray'),
	array('return' => 'xsd:boolean'),
		'urn:pos',                      // namespace
    'urn:pos#send_sales',                 // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'Returneaza Categoriile'            // documentation
);		


$server->register(
	'test_conn',
	array(),
	array('return' => 'xsd:boolean'),
		'urn:pos',                      // namespace
    'urn:pos#test_conn',                 // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'testeaza conexiunea'            // documentation
);		

$server->register(
	'send_nota',
	array(
		  'pos_id' => 'xsd:int',
		  'data_economica' => 'xsd:date',
		  'data_reala' => 'xsd:date',
		  'suma' => 'xsd:float',
		  'nota_id' => 'xsd:int',
		  'mod_plata' => 'xsd:string'
		  ),
	array('return' => 'xsd:boolean'),
	'urn:pos',                      // namespace
    'urn:pos#send_nota',                 // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'Introduce o nota de plata pentru raport real time'            // documentation
);	
// =========== PROCESS REQUESTS =======================================

$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($HTTP_RAW_POST_DATA);
exit();

// =========== METHOD IMPLEMENTATION =================================

// ---------- function has_modified_products(gestiune_id) ----------

function has_modified_products($gestiune_id)
{
	$ret_value = false;
	$query = "WHERE gestiune_id = $gestiune_id AND modificat=1";
	$pg = new ProduseGestiuni($query);
	if($pg->count() > 0)
	{
		$ret_value = true;
	}	
	
	return new soapval('return','xsd:boolean',$ret_value);
}

// ---------- function get_modified_products(gestiune_id) ---------

function get_modified_products($pos_id)
{
	$products = array();
	$pos = new Posuri($pos_id);
	$gestiune_id = $pos -> gestiune_id;
	$query = "WHERE gestiune_id = $gestiune_id AND modificat=1 and vanzare_pos = 1 and tip_produs in ('marfa', 'reteta')";
	$pg = new ViewProduseGestiuni($query);
	foreach($pg as $p){
		$pr = new Produse($p->produs_id);
		$product=array(
						"produs_id" => $p->produs_id,
						"categorie_id" => $p -> categorie_id,
						"denumire_categorie" => $p -> categorie -> denumire,
						"denumire"  => $p->denumire,
						"pret_ron"  => $p->pret_ron,
						"pret_val"  => $p->pret_val);
		$products[] = $product;
	}
	
	return $products;
}

// ----------- function get_all_products(gestiune_id) --------

function get_all_products($pos_id)
{
	$products = array();
	$pos = new Posuri($pos_id);
	$gestiune_id = $pos -> gestiune_id;
	$query = "WHERE gestiune_id = $gestiune_id and vanzare_pos = 1 and tip_produs in ('marfa', 'reteta')";
	$pg = new ViewProduseGestiuni($query);
	foreach($pg as $p){
		$product=array(
						"produs_id" => $p->produs_id,
						"categorie_id" => $p -> categorie_id,
						"denumire_categorie" => $p -> categorie -> denumire,
						"denumire"  => $p->denumire,
						"pret_ron"  => $p->pret_ron,
						"pret_val"  => $p->pret_val);
		$products[] = $product;
	}
	
	return $products;
}

// ----------- function sync_completed(gestiune_id) --------

function sync_completed($pos_id)
{
	global $server; 
	global $db;
	$pos = new Posuri($pos_id);
	$gestiune_id = $pos -> gestiune_id;
	try
	{
		$query = "WHERE gestiune_id =". $gestiune_id;
		$pg = new ProduseGestiuni($query);
		foreach($pg as $p)
		{
			$p->modificat = '0';
			$p->save();
		}
	}
	catch(Exception $e)
	{
		$server->fault("UnsetModified", $ex->getMessage() . "\r\n" . $ex->getTrace());
	}
	return new soapval('return','xsd:string','OK');
}

// -------- send_sales(sale_header, sale_details) ---------------------------------------

function send_sales($sale_header,$sale_details)
{
	global $server; 
	try
	{
		//save  header
		$pos = new Posuri($sale_header['pos_id']);
		$sale_h 				= new VanzariPos("where pos_id = '". $sale_header['pos_id'] ."' and datediff(data_economica,'". $sale_header['data_economica'] ."')=0");
		if(!count($sale_h)) {
			$sale_h = new VanzariPos();
			$sale_h->pos_id 			= $sale_header['pos_id'];
			$sale_h->data_economica 	= $sale_header['data_economica'];
			if($pos -> gestiune -> scad_stoc_auto == 'DA') {
				$sale_h -> validat = 'DA';
			}
			//$sale_h->data_introducere = date("Y-m-d H:i:s");
			$sale_h->save();
		}
		//save contents and 'vanzari_pos_iesiri'
		
		foreach($sale_details as $sale)
		{
			//content
			
			$produs = new Produse($sale['produs_id']);
			if(!count($produs)) return false;
			
			$sale_d 				= new VanzariPosContinut();
			$sale_d->vp_id 			= $sale_h->vp_id;
			$sale_d->produs_id		= $sale['produs_id'];
			$sale_d->cantitate 		= $sale['cantitate'];
			$sale_d->pret_vanzare	= $sale['pret_vanzare'];
			$sale_d->save();
			if($pos -> gestiune -> scad_stoc_auto == 'DA') {
				$sale_d -> produs -> scadStoc($sale_d -> cantitate, $pos -> gestiune_id, $sale_d -> id, 'VanzariPosContinutIesiri');
			}
		} 
	}
	catch(exception $e)
	{
		$server->fault("",$e->getMessage()."\r\n".$e->getTrace());
	}
	
	return new soapval('return','xsd:boolean',true);
}


	
function test_conn() {
	return true;
}	

function send_nota($pos_id, $data_economica, $data_reala, $suma, $nota_id, $mod_plata) {
	$nota = new NotePos();
	$nota -> pos_id = $pos_id;
	$nota -> data_economica = $data_economica;
	$nota -> data_reala = $data_reala;
	$nota -> suma = $suma;
	$nota -> nota_id = $nota_id;
	$nota -> mod_plata = $mod_plata;
	
	$nota -> save();
	
	return TRUE;
}
?>