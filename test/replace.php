<?php

ini_set('max_execution_time', 300);


session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$about = "About i\xc3\xa7ndeh%20kdkel";

if (!empty($about)) {
    $about=str_replace("%20", " ", $about);
}

var_dump($about);
?>
