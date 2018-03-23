<?php
require_once("cfg.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>
body {
font-family:'Arial', Helvetica, sans-serif;
font-size:11px;
}

fieldset {
	border: 0px solid #000;
}

.bill-item {
	margin-top:2px;
	height: 35px;
	text-align:center;
}
.bill-item-right {
	margin-top:2px;
	height: 35px;
	text-align:right;
}

.product-item {
margin-top:2px;
	height: 35px;
}

.nc8code {

	height: 20px;
	line-height: 20px;
	text-align:center;
	text-decoration:underline;
	border: 0px solid #000;
}

.nc8item {
	height:20px;
}
</style>
</head>

<body>
<?php

$print = new PrintFacturaExterna(10);
echo $print -> getHtml();
?>
</body>
</html>
