<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$userId = null;
if (isset($_GET["u"]))
    $userId = $_GET["u"];

try {
    if (!empty($userId)) {
        if (!SessionUtil::isUser($userId)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        $result = array();

        $followings = SocialFriendUtil::getUserFollowList($userId);
        $friendList = SocialUtil::getUserSocialFriend($userId);

        if (!empty($friendList) && sizeof($friendList) > 0) {
            $val = new User();
            for ($i = 0; $i < sizeof($friendList); $i++) {
                $val = $friendList[$i];
                if ($val->id != $userId) {
                    $followed = false;
                    if (!empty($followings) && !empty($val->id)) {
                        foreach ($followings as $follow) {
                            if (!empty($follow) && !empty($follow->id) && $follow->id == $val->id) {
                                $followed = true;
                                break;
                            }
                        }
                    }
                    $obj = new stdClass();
                    $obj->id = $val->id;
                    $obj->fullName = $val->firstName . " " . $val->lastName;
                    $obj->username = $val->userName;
                    $obj->userPicture = $val->getUserPic();
                    $obj->followed = $followed;
                    array_push($result, $obj);
                }
            }
        }
        $json_response = json_encode($result);
        echo $json_response;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
