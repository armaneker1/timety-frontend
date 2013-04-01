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
HttpAuthUtils::checkHttpAuth();
$events = array();

/*
 * Add events
 */
array_push($events, "1000134"); 
array_push($events, "1000135"); 
array_push($events, "1000136"); 
array_push($events, "1000137"); 
array_push($events, "1000260"); 
array_push($events, "1000107"); 
array_push($events, "1000108"); 
array_push($events, "1000138"); 
array_push($events, "1000139"); 
array_push($events, "1000263"); 
array_push($events, "1000109"); 
array_push($events, "1000110"); 
array_push($events, "1000131"); 
array_push($events, "1000140");
array_push($events, "1000177"); 
array_push($events, "1000178"); 
array_push($events, "1000179"); 
array_push($events, "1000180"); 
array_push($events, "1000182"); 
array_push($events, "1000181"); 
array_push($events, "1000092"); 
array_push($events, "1000254"); 
array_push($events, "1000183"); 
array_push($events, "1000184"); 
array_push($events, "1000185"); 
array_push($events, "1000186"); 
array_push($events, "1000187"); 
array_push($events, "1000188"); 
array_push($events, "1000192"); 
array_push($events, "1000101"); 
array_push($events, "1000189"); 
array_push($events, "1000193"); 
array_push($events, "1000190"); 
array_push($events, "1000191");
array_push($events, "1000194");
array_push($events, "1000195");
array_push($events, "1000196");
array_push($events, "1000197");
array_push($events, "1000198");

/*
 * Add events
 */
$i = 0;
if (!empty($events) && sizeof($events) > 0) {
    foreach ($events as $evtId) {
        if (!empty($evtId)) {
            echo "<h1>$evtId</h1>";
            echo "<h2>Timety Tag</h2>";
            $tags = Neo4jEventUtils::getEventTimetyTags($evtId);
            var_dump($tags);
            echo "<h2>Tag</h2>";
            $tags = Neo4jEventUtils::getEventTags($evtId);
            var_dump($tags);
            echo "<h2>Categories</h2>";
            $tags = Neo4jEventUtils::getEventCategories($evtId);
            var_dump($tags);
        }
    }
}
?>
