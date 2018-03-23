<?php
class ViewProduseGestiuni extends Produse
{
	var $tbl="view_produse_gestiuni";
	var $key="produs_id";
	var $_relations = array(
		"categorie" => array("type"=>"one", "model"=>"Categorii", "key"=>"categorie_id", "value" => "denumire"),
		"unitate_masura" => array("type"=>"one", "model"=>"UnitatiMasura", "key"=>"unitate_masura_id", "value" => "denumire"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare", "conditions" => "where 1 order by `cod_tva` asc"),
		"stoc" => array("type" => "one", "model" => "Stocuri", "sql" => " where produs_id='<%produs_id%>' and gestiune_id='<%gestiune_id%>'"),
		"tip" => array("type"=>"one", "model"=>"TipuriProduse", "key"=>"tip_produs", "model_key" => "tip"),
		);
	var $_defaultForm = array(
		"produs_id" => array("type" => "hidden"),
		"tip_produs" => array("type"=>"select", "label"=>"Tip produs", "options" => "SELECT tip as `value`, `descriere` FROM `tipuri_produse`", "attributes" => array("tabindex" => 7)),
		"gest" => '<div id="div_frm_gest">Gestiune</div>',
		"denumire" => array("type"=>"text", "label"=>"Denumire", "attributes" => array("style" => "width:400px;")),
		"cod_produs" => array("type"=>"text", "label"=>"Cod Intern", "attributes" => array("style" => "width:400px;")),
		"cod_bare" => array("type"=>"text", "label"=>"Cod Bare", "attributes" => array("style" => "width:400px;")),
		"nc8" => array("type"=>"text", "label"=>"Cod intrastat", "attributes" => array("style" => "width:400px;", "class" => "nc8")),
		"categorie" => array("label"=>"Categorie"),
		"unitate_masura" => array("label"=>"Unitate Masura", "attributes" => array()),
		"cota_tva" => array("label" => "Cota Tva", "attributes" => array("style" => "width:300px;")),
		"pret_referinta" => array("type" => "radiogroup", "label"=>"Pret Vanzare De Referinta", "options" => array("EUR" => "Euro", "LEI" => "Lei"), "attributes" => array("class" => "pret_referinta")) ,
		"pret_ron" => array("type"=>"text", "label"=>"Pret vanzare lei", "attributes" => array()),
		"pret_val" => array("type"=>"text", "label"=>"Pret vanzare euro", "attributes" => array()),
		"stoc_minim" => array("type"=>"text", "label"=>"Stoc minim", "attributes" => array("style" => "width:400px;")),
		"div_furnizor" => 'Furnizor Preferat<div id="div_furnizor"></div>',
		"vanzare_pos" => array("type"=>"select", "label"=>"Vanzare Pe POS", "options" => array("0" => "NU", "1" => "DA")),			
		);	
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("UM");
		$dg -> addHeadColumn("Tip Produs");
		$dg -> addHeadColumn("Categorie");
		$dg -> addHeadColumn("Stoc");
		$dg -> addHeadColumn("Pret Ach. LEI");
		$dg -> addHeadColumn("Pret Vanzare EUR");
		$dg -> addHeadColumn("Pret Vanzare LEI");		
		$dg -> addHeadColumn("NC8");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn($this -> unitate_masura -> denumire);
			$dg -> addColumn($this -> tip -> descriere);
			$dg -> addColumn($this -> categorie -> denumire);
			$dg -> addColumn(Html::onClickLink(treizecimale($this -> getStoc()), "xajax_gblInfoLoturi('". $this -> produs_id ."', '". $this -> gestiune_id ."')"), array("align" => "right", "style" => "color:red;font-weight:bold;"));
			$dg -> addColumn(douazecimale($this -> getPretMediuAchizitie($this -> gestiune_id)));
			$dg -> addColumn(douazecimale($this -> pret_val), ($this -> pret_referinta == "EUR") ? array("style" => "color:red") : array("style" => "color:black"));
			$dg -> addColumn(douazecimale($this -> pret_ron), ($this -> pret_referinta == "LEI") ? array("style" => "color:red") : array("style" => "color:black"));
			$dg -> addColumn($this -> btnInfoNC8());

			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function listaImport($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("&nbsp;");
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Pret Vanzare LEI");		
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn('<input type="checkbox" name="chk_produs[]" value="'. $this -> produs_id .'" class="chk_produs"/>');
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn('<input type="text" name="pret_ron['. $this -> produs_id .']" value="'. $this -> pret_ron .'" />');

			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
		
	function listaStocuri($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Categorie");
		$dg -> addHeadColumn("Stoc");
		$dg -> addHeadColumn("UM");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn($this -> categorie -> denumire);
			$dg -> addColumn(treizecimale($this -> stocLaData(data() ,$this -> gestiune_id)), array("align" => "right", "style" => "color:red;font-weight:bold;"));
			$dg -> addColumn($this -> unitate_masura -> denumire);
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		//$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
		
	function getStoc() {
		if(count($this -> stoc)) {
			$stoc = $this -> stoc -> stoc;
		} else {
			$stoc = '0.000';
		}
		return $stoc;
	}
	
	function getGestiuniAsociate() {
		$rows = $this -> db -> getRowsNum("select gestiune_id from produse_gestiuni where produs_id = '". $this -> id ."'");
		$out = array();
		foreach($rows as $row) {
			$out[] = $row[0];
		}
		return $out;
	}
	
	function getByGestiuneAndProdus($gestiune_id, $produs_id) {
		$this -> fromString("where produs_id = '$produs_id' and gestiune_id='$gestiune_id'");
	}
}
?>