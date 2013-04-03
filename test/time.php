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
var_dump(date("P"));

$zoneS = "+05:00";
$dateS = "2013-03-01 15:03:22";

$a = UtilFunctions::convertTimeZone($dateS, $zoneS);
var_dump($a);
?>
