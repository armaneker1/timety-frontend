<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$res = new Result();
$res->error = true;
$res->success = false;

try {
    if (!empty($userId)) {
        $array = array();
        $result = array();
        $array = SocialFriendUtil::getUserFollowList($userId);
        if (!empty($array) && sizeof($array) > 0) {
            $val = new User();
            for ($i = 0; $i < sizeof($array); $i++) {
                if (!empty($array[$i]) || $array[$i] == 0) {
                    $val = \UserUtils::getUserById($array[$i]);
                    $obj = new stdClass();
                    $obj->id = $val->id;
                    $obj->fullName = $val->firstName . " " . $val->lastName;
                    $obj->username = $val->userName;
                    $obj->userPicture = $val->getUserPic();
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
    } else {
        $res->error = true;
        $res->success = false;
        $res->param = "user Id null";
        $res->param = $e->getMessage();
    }
} catch (Exception $e) {
    $res->error = true;
    $res->success = false;
    $res->param = $e->getMessage();
}
?>
