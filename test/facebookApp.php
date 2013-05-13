<?php

ini_set('max_execution_time', 300);


session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();



$facebook = new Facebook(array(
            'appId' => "646366242055522",
            'secret' => "920f90f6347de611c5f3bd3ae73080d3",
            'cookie' => false,
            'fileUpload' => true
        ));
$token = $facebook->getAccessToken();

$eventDB = EventUtil::getEventById("1000758");
$pr = "SECRET";
if ($eventDB->privacy == 1 || $eventDB->privacy == "1") {
    $pr = "OPEN";
}
$timezone = "+03:00";
$fileName = __DIR__ . "/../images/ads.jpeg";


$event_info = array(
    "privacy_type" => $pr,
    "name" => $eventDB->title,
    "host" => "Me",
    "start_time" => date('Y-m-d\TH:i:s' . $timezone, strtotime($eventDB->startDateTime)),
    "end_time" => date('Y-m-d\TH:i:s.B' . $timezone, strtotime($eventDB->endDateTime)),
    "location" => $eventDB->location,
    "description" => $eventDB->description,
    "ticket_uri" => HOSTNAME . "/events/" . $eventDB->id,
    basename($fileName) => '@' . $fileName
);
//var_dump($event_info);
$result = $facebook->api('/646366242055522/events', 'post', $event_info);
var_dump($result);
?>
