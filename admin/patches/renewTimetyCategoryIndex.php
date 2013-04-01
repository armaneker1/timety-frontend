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

$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
$query = "START root=node:".IND_TIMETY_CATEGORY."('".PROP_TIMETY_CAT_NAME.":*') RETURN root";
$query = new Cypher\Query($client, $query, null);
$result=$query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        $id=$row['root']->getProperty(PROP_TIMETY_CAT_ID);
        $timetyCategoryIndex->remove($row['root'], PROP_CATEGORY_ID);
        $timetyCategoryIndex->add($row['root'], PROP_TIMETY_CAT_ID,$id);
        $timetyCategoryIndex->save();
    }
}
?>
