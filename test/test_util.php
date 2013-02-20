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

$keys = $redis->keys("*");

foreach ($keys as $key) {
    echo "<h2>" . $key . "</h2>";
    $events = $redis->zrangebyscore($key, "-inf", "+inf");
    $dub = array();
    $dubs = array();
    foreach ($events as $evt) {
        $evt2 = json_decode($evt);
        if (in_array($evt2->id, $dub)) {
            $i = array_search($evt2->id, $dub);
            $arr = $dubs[$i];
            array_push($arr, $evt);
            $dubs[$i] = $arr;
        } else {
            array_push($dub, $evt2->id);
            $arr = array();
            array_push($arr, $evt);
            array_push($dubs, $arr);
        }
    }

    foreach ($dubs as $d) {
        if (sizeof($d) > 1) {
            foreach ($d as $f) {
                echo $f . "<p/>";
            }
            echo "<br/><br/>";
        }
    }
}
?>