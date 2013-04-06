<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();
$redis = new Predis\Client();

$event = new Event();
$events = $redis->zrangebyscore("events:city:5", time(), "+inf");

for ($i = 0; $i < sizeof($events); $i++) {
    $event = json_decode($events[$i]);
    $event = UtilFunctions::cast("Event", $event);
    if ($event->creatorId == 6618414) {
        echo "<h1>$event->id</h1>";
        var_dump($event->tags);
    }
}

$redis->getProfile()->defineCommand('seacrhEventByTag', 'SeacrhEventByTag');
$events = $redis->seacrhEventByTag("events:city:5", '["10","1005"]', time(), '');

var_dump($events);
?>
