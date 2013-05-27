<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$limit = null;
if (isset($_GET["limit"]))
    $limit = $_GET["limit"];

$userId = null;
if (isset($_GET["u"]))
    $userId = $_GET["u"];

try {
    if (!empty($userId)) {
        $result = array();
        $followings = SocialFriendUtil::getUserFollowList($userId);
        $friendList = SocialFriendUtil::getPopularUserList($userId, $limit, $query);
        if (!empty($friendList) && sizeof($friendList) > 0) {
            $val = new User();
            for ($i = 0; $i < sizeof($friendList); $i++) {
                $val = $friendList[$i];
                $val=  UtilFunctions::cast('User', $val);
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
                    $obj->fullName = $val->getFullName();
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
