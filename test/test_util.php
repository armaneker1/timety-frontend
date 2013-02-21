<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../utils/Queue.php';


ini_set('max_execution_time', 300);
$redis = new Predis\Client();

$arman = 6618338;
$hasan = 6618346;
$event = new Event();
$armanEvents = $redis->zrangebyscore(REDIS_PREFIX_USER . $arman . REDIS_SUFFIX_MY_TIMETY, "-inf", "+inf");
$hasanEvents = $redis->zrangebyscore(REDIS_PREFIX_USER . $hasan . REDIS_SUFFIX_FOLLOWING, "-inf", "+inf");

echo "<h2>Arman my timety</h2>";
$i = 0;
foreach ($armanEvents as $event) {
    $event = json_decode($event);
    echo "$i -> $event->id  -> " . json_encode($event->userEventLog) . "<p/>";
    $i++;
}
echo "<h2>Hasan Following</h2>";
$i = 0;
foreach ($hasanEvents as $event) {
    $event = json_decode($event);
    echo "$i -> $event->id  -> " . json_encode($event->userEventLog) . "<p/>";
    $i++;
}

//Queue::followUser($hasan, $arman);
?>