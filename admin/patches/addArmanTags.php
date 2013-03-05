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

require_once __DIR__ . '/../../utils/Functions.php';


$array = array();
array_push($array, 96);
array_push($array, 139);
array_push($array, 54);

$userId = 6618346;
$userNode = Neo4jUserUtil::getUserNodeById($userId);

foreach ($array as $tag) {
    $ta = Neo4jTimetyTagUtil::getTimetyTagNodeById($tag);
    $userNode->relateTo($ta, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
}


$userId = 6618344;
$userNode = Neo4jUserUtil::getUserNodeById($userId);

foreach ($array as $tag) {
    $ta = Neo4jTimetyTagUtil::getTimetyTagNodeById($tag);
    $userNode->relateTo($ta, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
}
?>
