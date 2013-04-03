<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];


$eventId = null;
if (isset($_GET["eventId"]))
    $eventId = $_GET["eventId"];

$res = new Result();
$res->error = true;
$res->success = false;
if (!empty($userId) && !empty($eventId)) {
    $res = Neo4jEventUtils::getEventUserRelationCypher($eventId, $userId);
}

$json_response = json_encode($res);
echo $json_response;
?>
