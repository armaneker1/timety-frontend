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

$userId = 6618366;

$userNode = Neo4jUserUtil::getUserNodeById($userId);
$array = array();
array_push($array, 96);
array_push($array, 95);
array_push($array, 147);
array_push($array, 145);
array_push($array, 148);


foreach ($array as $tag) {
    $ta = Neo4jTimetyTagUtil::getTimetyTagNodeById($tag);
    $userNode->relateTo($ta, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
}
?>
