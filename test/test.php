<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

if (preg_match("/^events:(.*?):following$/", "events:1212121:following")) {
    echo "A match was found.";
}
if (preg_match("/^events:(.*?):following$/", "ddevents:1212121:slowing2")) {
    echo "A match was found.";
}
?>