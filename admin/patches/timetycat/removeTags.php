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

require_once __DIR__ . '/../../../utils/Functions.php';

$array = Neo4jTimetyCategoryUtil::getTimetyList("");

$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

foreach ($array as $tcat) {
    echo "<h2>$tcat->name($tcat->id)</h2>";
    $tags = Neo4jTimetyTagUtil::getTimetyTagsFromCat($tcat->id);
    foreach ($tags as $tag) {
        var_dump($tag->id . " " . $tag->name);
        Neo4jTimetyTagUtil::removeTimetyTag($tag->id);
        echo "Deleted<br/>";
    }
    echo "Done<br/>";
}
?>