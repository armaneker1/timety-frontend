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

$array = RedisUtils::getUserFollowings($userId);
$result = 0;
for ($i = 0; !empty($array) && $i < sizeof($array); $i++) {
    $usr = $array[$i];
    if (!empty($usr)) {
        if ($usr->id == $fUserId) {
            $result = 1;
            break;
        }
    }
}
echo $result;
?>
