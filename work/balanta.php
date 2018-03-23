<?php
define("DOC_ROOT", "../");
require_once("cfg.php");


$dg = new DataGrid(array("width"=>"100%", "border" => 1));
$dg -> addHeadColumn("Denumire");
$dg -> addHeadColumn("Pret");


$dg -> addColumn("COla");
$dg -> addColumn("5.5");
$dg -> index();
$dg -> addColumn("COla", array("style" => "color:red;"));
$dg -> addColumn("5.5");
$dg -> index();
echo $dg -> getDataGrid();
?>