<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();
$key = "events:city:5";

$redis = new Predis\Client();
$events = $redis->zrangebyscore($key, strtotime("now"), "+inf");
echo "<h1>84,95,96,145,1021</h1>";
foreach ($events as $event) {
    $event = json_decode($event);
    $event = UtilFunctions::cast("Event", $event);
    echo "<h2>$event->title - $event->id</h2>";
    if (!empty($event->tags)) {
        if (in_array(84, $event->tags) || in_array(95, $event->tags) || in_array(96, $event->tags) || in_array(145, $event->tags) || in_array(1021, $event->tags)) {
            var_dump($event->tags);
        }else{
            var_dump($event->tags);
        }
    }
}
?>
