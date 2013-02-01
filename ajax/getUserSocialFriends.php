<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$userId = null;
if (isset($_GET["u"]))
    $userId = $_GET["u"];

try {
    if (!empty($userId)) {
        $result = array();

        $follow = SocialFriendUtil::getUserFollowList($userId);
        $friendList = SocialUtil::getUserSocialFriend($userId);

        if (!empty($friendList) && sizeof($friendList) > 0) {
            $val = new User();
            for ($i = 0; $i < sizeof($friendList); $i++) {
                $val = $friendList[$i];
                $key = false;
                if (!empty($follow) && !empty($val->id)) {
                    $key = in_array($val->id, $follow);
                }
                $val->id = "u_" . $val->id;
                $val->label = $val->firstName . " " . $val->lastName . " (" . $val->userName . ")";
                $val->isFriend = $key;
                array_push($result, $val);
            }
        }
        $json_response = json_encode($result);
        echo $json_response;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
