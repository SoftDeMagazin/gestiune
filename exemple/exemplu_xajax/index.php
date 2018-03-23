<?php
require_once("common.php");
$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');

// din javascript apelezi cu xajax_numelefunctiei(parametrii)
?>

<div id="div_id"></div>

<input type="button" value="Functia Test"	onClick="xajax_test()">


<input type="text" id="nume" value="">
<input type="button" value="Functia Hello"	onClick="xajax_helloWorld(document.getElementById('nume').value)">
