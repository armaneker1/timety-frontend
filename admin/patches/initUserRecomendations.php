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
$users = UserUtils::getUserList(0, 100000);
foreach ($users as $user) {
    RedisUtils::initUser($user->id);
}
?>
