<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$reqUserId = null;
if (isset($_GET["reqUserId"]))
    $reqUserId = $_GET["reqUserId"];



$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$res = new Result();
$res->error = true;
$res->success = false;

try {
    $array = array();
    $result = array();

    $array = RedisUtils::getUserFollowings($reqUserId);

    $userFollowing = array();
    if (!empty($userId) && $userId != $reqUserId) {
        $userFollowing = RedisUtils::getUserFollowers($userId);
    } else {
        $userFollowing = RedisUtils::getUserFollowers($reqUserId);
    }

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
                $followed = false;
                if (!empty($userFollowing)) {
                    foreach ($userFollowing as $follow) {
                        if (!empty($follow) && !empty($follow->id) && $follow->id == $val->id) {
                            $followed = true;
                            break;
                        }
                    }
                }
                $obj->followed = $followed;
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
        $res->param = LanguageUtils::getText("LANG_AJAX_NO_RESULT");
        $json_response = json_encode($res);
        echo $json_response;
    }
} catch (Exception $e) {
    $res->error = true;
    $res->success = false;
    $res->param = $e->getMessage();
}
?>
