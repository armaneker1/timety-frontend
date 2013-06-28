<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$fromUserId = null;
if (isset($_POST["fuser"]))
    $fromUserId = $_POST["fuser"];

$toUserId = null;
if (isset($_POST["tuser"]))
    $toUserId = $_POST["tuser"];

$result = new Result();
try {
    if (!empty($fromUserId) && !empty($toUserId) && $fromUserId != $toUserId) {
        if (!SessionUtil::isUser($fromUserId)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        $result = SocialUtil::followUser($fromUserId, $toUserId);
    }
} catch (Exception $e) {
    $result->error = $e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>
