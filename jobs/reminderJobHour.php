<?php
session_start();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setLocale();
 EventUtil::updateEventReminder(1000017, 1);

?>
