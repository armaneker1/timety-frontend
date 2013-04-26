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


$type = null;
if (isset($_GET["type"]))
    $type = $_GET["type"];
if (isset($_POST["type"]))
    $type = $_POST["type"];

if (empty($type)) {
    $type = 0;
}

$ajax_guid = null;
if (isset($_GET["ajax_guid"]))
    $ajax_guid = $_GET["ajax_guid"];
if (isset($_POST["ajax_guid"]))
    $ajax_guid = $_POST["ajax_guid"];

if (!empty($userId)) {
    if (!SessionUtil::isUser($userId) && !SessionUtil::checkAjaxGUID($ajax_guid)) {
        $res = new stdClass();
        $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }
    Neo4jUserUtil::updateUserStatistics($userId, $type);
}
?>
