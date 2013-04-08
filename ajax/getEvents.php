<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$pageNumber = null;
if (isset($_GET["pageNumber"]))
    $pageNumber = $_GET["pageNumber"];

$pageItemCount = null;
if (isset($_GET["pageItemCount"]))
    $pageItemCount = $_GET["pageItemCount"];

$date = date(DATETIME_DB_FORMAT);
if (isset($_GET["date"]))
    $date = $_GET["date"];

$query = null;
if (isset($_GET["query"]))
    $query = $_GET["query"];

$type = null;
if (isset($_GET["type"]))
    $type = $_GET["type"];


$category = "-1";
if (isset($_GET["category"]))
    $category = $_GET["category"];

$reqUserId = "-1";
if (isset($_GET["reqUserId"]))
    $reqUserId = $_GET["reqUserId"];

$city_channel = "-1";
if (isset($_GET["city_channel"]))
    $city_channel = $_GET["city_channel"];

$tagIds = null;
if (isset($_GET["tagIds"]))
    $tagIds = $_GET["tagIds"];

$res = new Result();
$res->error = true;
$res->success = false;
/*
 * $userId= user id that logged in -1 default guest
 * list events after given date dafault current date
 * $type = events type 1=Popular,2=Mytimete,3=following,4=an other user's public events default 1
 * 5=i created
 * 6=i liked
 * 7=i reshared
 * 8=i joined
 * 9= categories
 * 10=user created
 * 11=user liked
 * 12=user reshared
 * 13=user joined
 * $query search paramaters deeafult "" all
 * $pageNumber deafult 0
 * $pageItemCount default 15
 */

if ($userId != null && $pageNumber != "" && $pageItemCount != null && $type != null) {
    echo Neo4jFuctions::getEvents($userId, $pageNumber, $pageItemCount, $date, $query, $type, $category, $reqUserId, $city_channel,$tagIds);
} else {
    $json_response = json_encode($res);
    echo $json_response;
}
?>
