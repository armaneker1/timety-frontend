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
require_once __DIR__ . '/../utils/Queue.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

ini_set('max_execution_time', 300);
$redis = new Predis\Client();

$type = 1;
if (isset($_GET["type"])) {
    if (!empty($_GET["type"])) {
        $type = $_GET["type"];
    }
}

$keys = $redis->keys("*");

if ($type == 1 || $type == "1") {
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
} else if ($type == 2 || $type == "2") {
    foreach ($keys as $key) {
        echo "<h2>" . $key . "</h2>";
        $events = $redis->zrangebyscore($key, "-inf", "+inf");
        foreach ($events as $evt) {
            $evt2= new Event();
            $evt2 = json_decode($evt);
            echo $evt2->id."<p/>";
            var_dump($evt2->userEventLog);
            echo "<h1></h1>";
        }
    }
}
?>