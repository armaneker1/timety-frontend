<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';


$n=new Neo4jFuctions();
$n->addUserInfo(6618310, "Hasan", "Keklik", USER_TYPE_NORMAL, "keklikhasan");

//var_dump(Neo4jUserSettingsUtil::getUserSubscribeCategory(1000006,146));


?>
