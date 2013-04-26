<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$event=  EventUtil::getEventById(1000674);

$event->getHeaderVideo();


var_dump($event->headerVideo);

echo "<p/>";

var_dump($event);

?>
