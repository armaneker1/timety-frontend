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

require_once __DIR__ . '/../../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$array_del = array();
array_push($array_del, 54);
array_push($array_del, 96);
array_push($array_del, 139);
array_push($array_del, 110);
array_push($array_del, 116);
array_push($array_del, 117);
array_push($array_del, 84);
array_push($array_del, 95);
array_push($array_del, 149);


$array = array();
array_push($array, 96);
array_push($array, 139);
array_push($array, 110);
array_push($array, 116);
array_push($array, 117);
array_push($array, 84);
array_push($array, 95);
array_push($array, 149);

$userId = 6618346;
$userNode = Neo4jUserUtil::getUserNodeById($userId);
foreach ($array_del as $tag) {
    $ta = Neo4jUserUtil::removeUserTag($userId, $tag);
}
foreach ($array as $tag) {
    $ta = Neo4jTimetyTagUtil::getTimetyTagNodeById($tag);
    $userNode->relateTo($ta, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
}
RedisUtils::initUser($userId);

$userId = 6618344;
$userNode = Neo4jUserUtil::getUserNodeById($userId);
foreach ($array_del as $tag) {
    $ta = Neo4jUserUtil::removeUserTag($userId, $tag);
}
foreach ($array as $tag) {
    $ta = Neo4jTimetyTagUtil::getTimetyTagNodeById($tag);
    $userNode->relateTo($ta, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
}
RedisUtils::initUser($userId);
?>
