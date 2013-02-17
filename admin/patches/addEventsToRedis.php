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

$update = false;
if (isset($_GET["update"])) {
    if ($_GET["update"] == "1" || $_GET["update"] == 1) {
        $update = true;
    }
}

$array = Neo4jEventUtils::getAllEventsNode("");
echo "<p>Update or Add : ".$update."</p>";

$i=1;
foreach ($array as $evt) {
    echo "<p>$i Event Id :".$evt->getProperty(PROP_EVENT_ID)."</p>";
    $i++;
    if($update)
    {
        Queue::updateEvent($evt->getProperty(PROP_EVENT_ID));
    }else
    {
        Queue::addEvent($evt->getProperty(PROP_EVENT_ID));
    }
    echo "<p>Event Id :".$evt->getProperty(PROP_EVENT_ID)." Sended</p>";
    echo "<p></p>";
    echo "<p></p>";
}
?>
