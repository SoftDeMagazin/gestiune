<?php
require_once("cfg.php");
$produse = new Produse("where categorie_id = 1");

foreach($produse as $produs) {
	echo $produs -> denumire.' -- Categorie: '.$produs -> categorie -> denumire;
	echo '<br/>';
}
?>