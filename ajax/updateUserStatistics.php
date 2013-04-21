<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];


$type = null;
if (isset($_GET["type"]))
    $type = $_GET["type"];
if (isset($_POST["type"]))
    $type = $_POST["type"];

if (empty($type)) {
    $type = 0;
}

if (!empty($userId)) {
    if (!SessionUtil::isUser($userId)) {
        $res = new stdClass();
        $res->error = "user not logged in";
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }
    Neo4jUserUtil::updateUserStatistics($userId, $type);
}
?>
