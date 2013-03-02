<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];

RedisUtils::initUser($userId);

/*
$events = json_decode(RedisUtils::getUpcomingEventsForUser($userId, 0, 1000, null, null, 1));
$evt=new Event();
foreach ($events as $evt){
    echo "<h2>$evt->title ($evt->id)</h2>";
}*/
?>
