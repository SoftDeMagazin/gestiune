<?php
require_once("cfg.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php
$produse = new Produse("where categorie_id = 10");

foreach($produse as $prod) {

	$produs_nou = new Produse($prod -> id);
	$produs_nou -> produs_id = 0;
	$produs_nou -> denumire = str_replace("40ML", "MP", $produs_nou -> denumire);
	$produs_nou -> categorie_id = 20;
	$produs_nou -> tip_produs = "mp";
	$produs_nou -> vanzare_pos = "0";
	$produs_nou -> save();
	
	$produs_nou -> asociazaCuGestiuni(array(1,2,3,4), array("pret_ron" => "0.00", "pret_val" => "0.00", "modificat" => 1));
	
	$retetar = new Retetar();
	$retetar -> produs_id = $prod -> id;
	$retetar -> componenta_id = $produs_nou -> id;
	$retetar -> cantitate = '0.04';
	$retetar -> save();
}

?>
</body>
</html>
