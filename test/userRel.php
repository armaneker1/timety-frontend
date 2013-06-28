<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$eventId = 1000598;
$userId = 6618344;

Neo4jFuctions::responseToEventInvites2($userId, $eventId, 2);
?>
