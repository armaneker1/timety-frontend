<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();


//user_id
$uid = null;
if (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
}
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
}


if (!empty($uid)) {
    $user = UserUtils::getUserById($uid);
    if (!empty($user)) {
        $array = RedisUtils::getUserFollowers($uid);
        $result = array();
        foreach ($array as $val) {
            $obj = new stdClass();
            $obj->id = $val->id;
            $obj->fullName = $val->firstName . " " . $val->lastName;
            $obj->username = $val->userName;
            $obj->userPicture = $val->getUserPic();
            $obj->followed = SocialUtil::checkFollowStatus($uid, $val->id);
            array_push($result, $obj);
        }

        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->users = $result;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "User not found";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "User Id is empty";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
