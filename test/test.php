<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$time = 1369965600;
$result = strftime('%A', $time);

var_dump($result);
?>