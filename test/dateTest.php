<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

 var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), "2013-04-20 22:00:00"));
  var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), "2013-04-19 22:00:00"));

$s_date = "2013-04-04 20:00:00";
/*
for ($i = 0; $i < 1000; $i++) {
    $date = date(DATETIME_DB_FORMAT, strtotime($s_date . " $i day"));
    echo "<h1>$date</h1>";
    var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));
}
 */
?>
