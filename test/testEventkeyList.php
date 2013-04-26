<?php

session_start();
header("charset=utf8");
require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$keyList = EventKeyListUtil::getEventKeyList(1000336);

var_dump($keyList);
?>
