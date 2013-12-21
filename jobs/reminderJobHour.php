<?php
session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setLocale();
 EventUtil::updateEventReminder(1000017, 1);

?>
