<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$res = new Result();
$res->error = true;
$res->success = false;
$res->param = "";

$fuser = null;
if (isset($_GET["fuser"]))
    $fuser = $_GET["fuser"];

$tuser = null;
if (isset($_GET["tuser"]))
    $tuser = $_GET["tuser"];

try {
    if (!empty($tuser) && !empty($fuser)) {
        if (!SessionUtil::isUser($fuser)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        $result = UserSettingsUtil::unsubscribeUserFriend($fuser, $tuser);
        if (!empty($result)) {
            $res->error = !$result;
            $res->success = $result;
        }
    }
} catch (Exception $e) {
    $res->param = $e->getMessage();
}

$json_response = json_encode($res);
echo $json_response;
?>
