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

require_once __DIR__ . '/../../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

$array = array();
array_push($array, LANG_EN_US);
array_push($array, LANG_TR_TR);
foreach ($array as $ar) {
    $query = "START tag=node:" . IND_TIMETY_TAG . "_" . $ar . "('" . PROP_TIMETY_TAG_ID . ":*')  RETURN tag";
    $query = new Cypher\Query($client, $query, null);
    $result = $query->getResultSet();
    foreach ($result as $row) {
        $ro = $row[0];
        $ro->setProperty(PROP_TIMETY_LANG_CODE, $ar)->save();
    }
}
?>
