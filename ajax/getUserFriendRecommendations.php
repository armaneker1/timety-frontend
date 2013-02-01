<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

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
        $friendList = SocialFriendUtil::getPopularUserList($userId, $limit, $query);
        if (!empty($friendList) && sizeof($friendList) > 0) {
            $val = new User();
            for ($i = 0; $i < sizeof($friendList); $i++) {

                $val = $friendList[$i];
                $val->id = "u_" . $val->id;
                $val->label = $val->firstName . " " . $val->lastName . " (" . $val->userName . ")";
                $val->isFriend = false;
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
