<?php
require_once("cfg.php");
set_time_limit(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="" method="post" enctype="multipart/form-data" name="frm" id="frm">
  Fiser csv : 
  <label>
  <input type="file" name="file" id="file" />
  </label> <br />
<br />
<label>
<input type="submit" name="button" id="button" value="Submit" />
</label>

</form>
<?php

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$filename = $_FILES['file']['name'];
 	move_uploaded_file($_FILES["file"]["tmp_name"],$_FILES["file"]["name"]);
 	$filecontent = file_get_contents($_FILES["file"]["name"]);
 	$file_array = explode("\r\n", $filecontent);
	$gestiune_id = 1;
	foreach($file_array as $row) { 
		$info = explode(",", $row);
		if($info[0]) {
		$produs = new Produse();
		$produs -> denumire = $info[3];
		$produs -> cod_produs = $info[2];
		$produs -> nc8 = $info[1];
		$cat = new Categorii("where denumire = '". $info[0] ."'");
		if(count($cat)) {
			$categorie_id = $cat -> id;
		} else {
			$cat = new Categorii();
			$cat -> denumire = $info[0];
			$cat -> save();
			$categorie_id = $cat -> id;
			$cat -> asociazaCuGestiuni(array($gestiune_id));
		}
		$produs -> categorie_id = $categorie_id;
		$um = new UnitatiMasura(" where denumire = '". $info[5] ."'");
		if(count($um)) {
			$um_id = $um -> id;
		} else {
			$um = new UnitatiMasura();
			$um -> denumire = $info[5];
			$um -> save();
			$um_id = $um -> id;
		}
		$produs -> unitate_masura_id = $um_id;
		$produs -> tip_produs = "marfa";
		$produs -> cota_tva_id = 1;
		$produs -> pret_referinta = "EUR";
		$produs -> ambalare = $info[6];
		$produs -> save();
		
		$produs -> asociazaCuGestiuni(array($gestiune_id), array("pret_ron" => "0.00", "pret_val" => $info[4]));
		echo $produs -> denumire.'<br>';
		}
	}
}
?>
</body>
</html>
