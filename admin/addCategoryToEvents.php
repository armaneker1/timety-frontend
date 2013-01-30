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
$array = array();

array_push($array, array("1000090", "2"));
array_push($array, array("1000093", "2"));
array_push($array, array("1000094", "12"));
array_push($array, array("1000099", "12"));
array_push($array, array("1000098", "12"));
array_push($array, array("1000097", "12"));
array_push($array, array("1000089", "12"));
array_push($array, array("1000092", "2"));
array_push($array, array("1000091", "2"));
array_push($array, array("1000095", "2"));

foreach ($array as $evt) {
    $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $evt[0] . "'), " .
            " cat=node:" . IND_TIMETY_CATEGORY . "('" . PROP_TIMETY_CAT_ID . ":" . $evt[1] . "') " .
            " CREATE (cat) -[r:" . REL_EVENTS . "]-> (event)  ";
    $query = new Cypher\Query($client, $query, null);
    $query->getResultSet();
}
?>