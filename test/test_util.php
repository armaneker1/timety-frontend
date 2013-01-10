<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';


var_dump(Neo4jUserSettingsUtil::subscribeUserCategory(1000007,146));
var_dump(Neo4jUserSettingsUtil::subscribeUserCategory(1000007,45));

//var_dump(Neo4jUserSettingsUtil::getUserSubscribeCategory(1000006,146));


?>
