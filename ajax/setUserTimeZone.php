<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$userId = null;
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$zone = null;
if (isset($_POST["zone"]))
    $zone = $_POST["zone"];
if (isset($_GET["zone"]))
    $zone = $_GET["zone"];


$result = new Result();
$result->error = true;
$result->success = false;
if (!empty($zone) && !empty($userId)) {
    if (!SessionUtil::isUser($userId)) {
        $res = new stdClass();
        $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }
    try {
        UserUtils::updateUserTimeZone($userId, $zone);
        $result->error = false;
        $result->success = true;
    } catch (Exception $exc) {
        error_log($exc->getTraceAsString());
    }
}

$json_response = json_encode($result);
echo $json_response;
?>
