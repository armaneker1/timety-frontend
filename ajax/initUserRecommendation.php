<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];

if (!SessionUtil::isUser($userId)) {
    $res = new stdClass();
    $res->error = "user not logged in";
    $json_response = json_encode($res);
    echo $json_response;
    exit(1);
}
RedisUtils::initUser($userId);
?>
