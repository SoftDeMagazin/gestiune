<?php
require_once("cfg.php");
/*
 * clasa DataGrid permite cu usurinta crearea de tabele
 */
 $proprietati_tag_table = array("width" => "100%", "border" => "1");
$dg = new DataGrid($proprietati_tag_table);

/*
 * antet tabel
 */
$dg -> addHeadColumn("Denumire");
$dg -> addHeadColumn("Categorie", array("style" => "color:red"));

$produse = new Produse("where 1 limit 0, 30");

foreach($produse as $produs) {
	$dg -> addColumn($produs -> denumire);
	$dg -> addColumn($produs -> categorie -> denumire, array("align" => "right"));
	//trec la urmatorul rand
	$dg -> index();
} 

//afisez dg-ul
echo $dg -> getDataGrid();
?>