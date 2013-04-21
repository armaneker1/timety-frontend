<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$eventId = null;
if (isset($_POST["eventId"]))
    $eventId = $_POST["eventId"];
if (isset($_GET["eventId"]))
    $eventId = $_GET["eventId"];

$revert = null;
if (isset($_POST["revert"]))
    $revert = $_POST["revert"];
if (isset($_GET["revert"]))
    $revert = $_GET["revert"];


$res = new Result();
$res->error = true;
$res->success = false;

try {
    if (!empty($eventId) && !empty($userId)) {
        if (!SessionUtil::isUser($userId)) {
            $res = new stdClass();
            $res->error = "user not logged in";
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        if (!empty($revert) && $revert == 1) {
            $result = SocialUtil::revertReshareEvent($userId, $eventId);
        } else {
            $result = SocialUtil::reshareEvent($userId, $eventId);
        }
        if (empty($result) || $result->error || !$result->success) {
            $res->error = true;
            $res->success = false;
            array_push($res->param, "An Error Occured");
        } else {
            $res = new Result();
            $res->error = false;
            $res->success = true;
        }
    } else {
        array_push($res->param, "Parameters Invalid");
    }
} catch (Exception $e) {
    array_push($res->param, $e->getMessage());
}
$json_response = json_encode($res);
echo $json_response;
?>
