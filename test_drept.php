<?php
$url = $_SERVER['PHP_SELF'];
$list = explode("/",$url);
$list[count($list) - 1] = "";
foreach($list as $li) {
	if($li) $module_url .= $li."/";
} 
$modul = new Module("where url='".$module_url."'");

if(count($modul)) {
	$permision = $_SESSION['user'] -> permissions[''. $modul -> id .''];
	if(isset($permision)) {
		if(!$permision -> getView()) {
			header("Location:".DOC_ROOT."home/");
		}
	}
	else {
		header("Location:".DOC_ROOT."home/");
	}
}
?>