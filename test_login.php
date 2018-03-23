<?php
if(!isset($_SESSION['user'])) {
	header("Location: ".DOC_ROOT."autentificare/");
	exit();
}
?>