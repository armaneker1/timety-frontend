<?php

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';

$eventList = EventUtil::getAllEvents();

foreach ($eventList as $event) {
    echo "<h2>" . $event->id . "</h2>";
    Neo4jEventUtils::removeEventCategories($event->id);
    Neo4jEventUtils::removeEventTags($event->id);
    echo "<h3>Done</h3>";
}
?>
