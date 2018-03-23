<?php
require_once("../app/include/db/mysqli.php");
require_once("../common/user_profile.php");

$username = $_POST['username'];
$pass = $_POST['pass'];
$gest_id = $_POST['gestiune_id'];

$db = new MySQL();
$sql = "SELECT u.utilizator_id,u.user_name,u.nume,u.rol_id FROM ".
		"utilizatori u ".
		"INNER JOIN gestiuni_utilizatori gu ON gu.gestiune_id ='".$gest_id."' "
		."AND gu.utilizator_id = u.utilizator_id ".
		"WHERE u.user_name='".$username."' AND u.parola='".$pass."'";

$user_data = $db->getRow($sql);

$user_profile = new UserProfile($user_data);
var_dump($user_profile);
$_SESSION['user'] = $user_profile;
?>