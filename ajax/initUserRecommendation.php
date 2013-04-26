<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
$ajax_guid = null;
if (isset($_GET["ajax_guid"]))
    $ajax_guid = $_GET["ajax_guid"];
if (isset($_POST["ajax_guid"]))
    $ajax_guid = $_POST["ajax_guid"];

if (!SessionUtil::isUser($userId) && !SessionUtil::checkAjaxGUID($ajax_guid)) {
    $res = new stdClass();
    $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
    $json_response = json_encode($res);
    echo $json_response;
    exit(1);
}
RedisUtils::initUser($userId);
?>
