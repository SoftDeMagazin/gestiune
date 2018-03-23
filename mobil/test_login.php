<?php
if(!isset($_SESSION['user'])) { 
	header("Location: ".DOC_ROOT."mobil/login.php");
	exit();
}
?>