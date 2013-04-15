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

$redis = new Predis\Client();
$events = $redis->zrangebyscore("user:events:6618346:mytimety", "-inf", "+inf");

for ($i = 0; $i < sizeof($events); $i++) {
    $event = json_decode($events[$i]);
    $event = UtilFunctions::cast("Event", $event);
    var_dump($event);
}
?>
