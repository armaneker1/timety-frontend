<?php

ini_set('max_execution_time', 300);

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
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$redis = new Predis\Client();
$key = "deneme:1:1";


/*
for ($i = 1100; $i < 1120; $i++) {
    $obj = new stdClass();
    $obj->id = $i;
    $obj->name = "Hasan" . $i;
    $obj->privacy = "true";
    $array = array();
    if ($i % 2 == 0) {
        array_push($array, "10");
        array_push($array, "20");
        array_push($array, "30");
    } else {
        array_push($array, "40");
        array_push($array, "50");
        array_push($array, "60");
    }
    $obj->tags = $array;
    $redis->zadd($key, $i, json_encode($obj));
}
 */

var_dump($redis->zrange($key, 0, -1));

/*
$redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
$a = $redis->removeItemByIdReturnItem($key, 1101);
var_dump($a);
 */

/*
$redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
$a = $redis->removeItemById($key, 14);
var_dump($a);
 */

/*
  var_dump($redis->zrange($key, 0, -1));

  $timeparts = explode(" ",microtime());
  $currenttime1 = bcadd(($timeparts[0]*1000),bcmul($timeparts[1],1000));
  $redis->getProfile()->defineCommand('seacrhEventByTag', 'SeacrhEventByTag');
  $a = $redis->seacrhEventByTag($key, "[1,20,60]");
  $timeparts = explode(" ",microtime());
  $currenttime2 = bcadd(($timeparts[0]*1000),bcmul($timeparts[1],1000));
  echo ($currenttime2-$currenttime1)/1000; */
//var_dump($a);
?>
