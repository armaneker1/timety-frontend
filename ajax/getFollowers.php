<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];


$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$res = new Result();
$res->error = true;
$res->success = false;

try {
    $array = array();
    $result = array();

    //$array = Neo4jUserUtil::getUserFollowerList($userId);
    $array = RedisUtils::getUserFollowers($userId);


    if (!empty($array) && sizeof($array) > 0) {
        $val = new User();
        for ($i = 0; $i < sizeof($array); $i++) {
            $val = $array[$i];
            if (!empty($val) && !empty($val->id)) {
                $obj = new stdClass();
                $obj->id = $val->id;
                $obj->fullName = $val->firstName . " " . $val->lastName;
                $obj->username = $val->userName;
                $obj->userPicture = $val->getUserPic();
                $obj->followed = SocialUtil::checkFollowStatus($userId, $val->id);
                array_push($result, $obj);
            }
        }
    }

    if (!empty($result) && sizeof($result) > 0) {
        $json_response = json_encode($result);
        echo $json_response;
    } else {
        $res->error = true;
        $res->success = false;
        $res->param = "No result";
        $json_response = json_encode($res);
        echo $json_response;
    }
} catch (Exception $e) {
    $res->error = true;
    $res->success = false;
    $res->param = $e->getMessage();
}
?>
