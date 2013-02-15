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


$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);

Neo4jEventUtils::inviteUserToEvent($eventIndex->findOne(PROP_EVENT_ID, 1000330), $userIndex->findOne(PROP_USER_ID, 6618351));
?>