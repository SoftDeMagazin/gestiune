<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');
?>
<?php
if($_SERVER['REQUEST_METHOD'] == "POST") {
	$data = $_POST;
} else {
	$data = $_GET;
}
$rpt_name = $data['rpt_name'];
$rpt = new $rpt_name($data);
echo $rpt -> getPdf();
?>