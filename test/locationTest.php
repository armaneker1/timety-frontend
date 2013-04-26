<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

 $response = LocationUtils::getCityCountry(41.005199,28.978562);
 var_dump($response);
?>
