<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];


$fUserId = null;
if (isset($_GET["fUserId"]))
    $fUserId = $_GET["fUserId"];
if (isset($_POST["fUserId"]))
    $fUserId = $_POST["fUserId"];

$result = RedisUtils::isUserInFollowings($userId, $fUserId);
echo $result;
?>
