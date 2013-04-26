<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$res = new Result();
$res->error = true;
$res->success = false;
$res->param = "";

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$categoryId = null;
if (isset($_GET["categoryId"]))
    $categoryId = $_GET["categoryId"];

try {
    if (!empty($userId) && !empty($categoryId)) {
        if (!SessionUtil::isUser($userId)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        $result = UserSettingsUtil::unsubscribeUserCategory($userId, $categoryId);
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
