<?php 
require_once ('../app/thirdparty/nusoap/nusoap.php');
$wsdl = 'http://188.173.163.14/pos/pos_sync_ws.php?wsdl';
$client = new nusoap_client($wsdl, true);

$err = $client->getError();
if ($err) {
    // Display the error
    echo '<p><b>Constructor error: '.$err.'</b></p>';
    // At this point, you know the call that follows will fail
}


$param = array(
'sale_header'=>array(  'gestiune_id'=>'7',
						'pos_id'=>'1',
						'data_economica'=>'2009-01-01'
					 ),
'sale_details'=>array(
						array(
								'produs_id'=>'6',
								'pret_vanzare'=>'1',
								'cantitate'=>'1'
							),
						array(
								'produs_id'=>'7',
								'pret_vanzare'=>'2',
								'cantitate'=>'3'
							),
						)
);

$result = $client->call('test_conn');
echo $client -> response;
if ($client->fault) {
    echo '<p><b>Fault: ';
    print_r($result);
    echo '</b></p>';
} else {
    // Check for errors
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<p><b>Method Error: '.$err.'</b></p>';
    } else {
        // Display the result
        print_r($result);
    }
}

unset($soapclient);
?>
