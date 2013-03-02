<?php
require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../utils/SettingFunctions.php';

$eventID = "1000293";
$event = new Event();
$event = Neo4jEventUtils::getNeo4jEventById($eventID);
var_dump($event);
if ($event->privacy == 1 || $event->privacy == "1") {
    
}
?>