<?php
require_once("cfg.php");
?>

<form action="" method="post" enctype="multipart/form-data" name="frm" id="frm">
  Fiser csv : 
  <label>
  <input type="file" name="file" id="file" />
  </label>
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
	foreach($file_array as $row) { 
		$info = explode(",", $row);
		if($info[0]) {
			foreach($info as $key => $val) {
				$info[$key] = str_replace("___", ",", $val);
			}

		
			$tert = new Terti(); 
			
			$tert -> denumire = $info[0];
			$tert -> cod_fiscal = $info[1];
			$tert -> reg_com = $info[2];
			$tert -> judetul = $info[5];
			$tert -> sediul = $info[3].", ".$info[4].", ".$info[5];
			$tert -> banca = $info[7];
			$tert -> iban = $info[6];
			$tert -> cod_tara = substr($info[3], 0, 2);
			if(str_starts_with($info[3], "RO")) {
				$tert -> tip = 'intern';
				$tert -> valuta = 'LEI';
			} else {
				$tert -> tip = 'extern_ue';
				$tert -> valuta = 'EUR';
			}
			
			
			$tert -> save();
			
			$tert -> asociazaCuGestiuni(array(1,2,3), array("categorie_tert_id" => 2));
			
			echo '<br>';	
		}
	}
}
?>