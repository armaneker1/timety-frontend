<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$r=  Neo4jEventUtils::getEventLikesCount(1000354);
?>