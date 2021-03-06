<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];


$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$followers = null;
if (isset($_GET["f"]))
    $followers = $_GET["f"];

$res = new Result();
$res->error = true;
$res->success = false;

try {
    $array = array();
    $result = array();
    if (!empty($userId) && $query == "?-1") {
        $array = UserSettingsUtil::getUserSubscribeFriends($userId);
    } elseif (!empty($userId) && $query == "?-2") {
        //$array = Neo4jUserUtil::getUserFollowList($userId);
        $array = RedisUtils::getUserFollowings($userId);
    } elseif (!empty($userId)) {
        if ($query == "*") {
            $query = "";
        }
        $array = SocialFriendUtil::getFriendList($userId, $query,$followers);
    }

    if (!empty($array) && sizeof($array) > 0) {
        $val = new User();
        for ($i = 0; $i < sizeof($array); $i++) {
            $val = $array[$i];
            $val=  UtilFunctions::cast('User', $val);
            if (!empty($val) && !empty($val->id)) {
                $obj = new stdClass();
                $obj->id = $val->id;
                $obj->fullName = $val->getFullName();
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
        $res->param =LanguageUtils::getText("LANG_AJAX_NO_RESULT");
        $json_response = json_encode($res);
        echo $json_response;
    }
} catch (Exception $e) {
    $res->error = true;
    $res->success = false;
    $res->param = $e->getMessage();
}
?>
