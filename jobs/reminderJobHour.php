<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';

 EventUtil::updateEventReminder(1000017, 1);

?>
