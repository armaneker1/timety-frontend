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

require_once __DIR__ . '/../../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
$array = Neo4jTimetyCategoryUtil::getTimetyList("");

foreach ($array as $tcat) {
    echo "<h2>$tcat->name($tcat->id)</h2>";
    $tags = Neo4jTimetyTagUtil::getTimetyTagsFromCat($tcat->id);
    foreach ($tags as $tag) {
        var_dump($tag->id." ".$tag->name);
        Neo4jTimetyTagUtil::insertTimetyTag($tcat->id, $tag->name, LANG_TR_TR, $tag->id);
        Neo4jTimetyTagUtil::insertTimetyTag($tcat->id, $tag->name, LANG_EN_US, $tag->id);
    }
    echo "Done<br/>";
}
?>
