<?php
require_once("cfg.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NIR</title>
</head>

<body>
<object ID="WebBrowser1" WIDTH="0" HEIGHT="0" CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>
<style>@media print { #buttons { display:none } } </style>
<style>
body {
font-family:'Arial', Helvetica, sans-serif;
font-size:11px;
}

</style>
<div id="buttons">
<button onClick="WebBrowser1.ExecWB(7, 6);">Preview</button>  
<button onClick="window.print();">Print</button>
<button onClick="window.close();">Close</button>
</div>
<?php

$nir_id = $_GET['nir_id'];
$print = new PrintNir($nir_id);
echo $print -> getHtml();
?>
</body>
</html>
