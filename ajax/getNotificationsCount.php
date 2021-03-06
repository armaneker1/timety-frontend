<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_GET["userId"]))
    $query = $_GET["userId"];
try {
    $result = new Result();
    $result->error = true;

    if (!empty($query)) {
        if (!SessionUtil::isUser($query)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        $user = UserUtils::getUserById($query);
        if (!empty($user)) {
            $result = $user->getUserNotificationCount();
        }
    }
} catch (Exception $e) {
    $result->error = $e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>
