<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$eventId = 1000881;
$SQL = "DELETE  FROM " . TBL_VIDEOS . " WHERE eventId=" . $eventId;

TimeteVideos::deleteByFilter($db, $filter)
?>
