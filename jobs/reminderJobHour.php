<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';

$array=  ReminderUtil::getUpcomingEvents(0);
var_dump($array);

?>
