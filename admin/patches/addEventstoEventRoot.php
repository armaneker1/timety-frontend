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
HttpAuthUtils::checkHttpAuth();

Neo4jEventUtils::checkEventRootExits();

$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
$root_events = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_EVENT);

$array = Neo4jEventUtils::getAllEventsNode("");

foreach ($array as $evt) {
    $id = $evt->getProperty(PROP_EVENT_ID);
    if (!empty($evt) && ( $id == 0 || $id == "0" || !empty($id))) {
        $tmp = Neo4jEventUtils::getEventFromNode($id);
        if (empty($tmp) || empty($tmp->id)) {
            var_dump($id);
            $root_events->relateTo($evt, REL_EVENT)->save();
        }
    }
}
?>
