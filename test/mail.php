<?php

ini_set('max_execution_time', 300);
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$fu = UserUtils::getUserById(6618357);
$tu = UserUtils::getUserById(6618344);

$follow_color = "#588cc8";
$following_color = "#84C449";
$style = $follow_color;
$flw_text = "follow";
if (RedisUtils::isUserInFollowings(6618344, 6618357)) {
    $style = $following_style;
    $flw_text = "following";
}
$params = array(
    array('folw_bg_color', $style),
    array('folw_text', $flw_text),
    array('name', $tu->firstName),
    array('followerName', $fu->firstName),
    array('followerSurname', $fu->lastName),
    array('followerUsername', $fu->userName),
    array('bio', $fu->about),
    array('img', PAGE_GET_IMAGEURL . urlencode($fu->getUserPic()) . "&h=90&w=90"),
    array('$profileUrl', HOSTNAME . $fu->userName),
    array('email_address', $tu->email));
MailUtil::sendSESMailFromFile("followedBy.html", $params, "" . $tu->getFullName() . " <" . $tu->email . ">", "You have a new follower on Timety!");
?>
