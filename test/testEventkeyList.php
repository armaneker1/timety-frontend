<?php

session_start();
header("charset=utf8");
require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();


$eventId = 1234567;
$key1 = "asas1";
$key2 = "asas2";
$key3 = "asas3";
$key4 = "asas4";

EventKeyListUtil::updateEventKey($eventId, $key1, true);
EventKeyListUtil::updateEventKey($eventId, $key1, false);
EventKeyListUtil::updateEventKey($eventId, $key2, true);
EventKeyListUtil::updateEventKey($eventId, $key2, true);

EventKeyListUtil::updateEventKey($eventId, $key3, true);
EventKeyListUtil::updateEventKey($eventId, $key4, true);
EventKeyListUtil::updateEventKey($eventId, $key3, true);


var_dump(EventKeyListUtil::getEventKeyList($eventId));


EventKeyListUtil::deleteRecordForEvent($eventId);
?>
