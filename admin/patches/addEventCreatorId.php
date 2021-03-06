<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$array = Neo4jEventUtils::getAllEventsNode("");

foreach ($array as $evt)
{
    EventUtil::updateCreatorId($evt->getProperty(PROP_EVENT_ID), $evt->getProperty(PROP_EVENT_CREATOR_ID));
}

?>
