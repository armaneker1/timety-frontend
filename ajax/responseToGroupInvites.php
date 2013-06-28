<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();

$result = new Result();
if (isset($_POST["g"]) && isset($_POST["u"]) && isset($_POST["r"])) {
    $userId = $_POST["u"];
    $groupId = $_POST["g"];
    $resp = $_POST["r"];
    try {
        if (!empty($userId) && !empty($groupId) && (!empty($resp) || $resp == 0)) {
            if (!SessionUtil::isUser($userId)) {
                $res = new stdClass();
                $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
                $json_response = json_encode($res);
                echo $json_response;
                exit(1);
            }
            $result = InviteUtil::responseToGroupInvites($userId, $groupId, $resp);
        }
    } catch (Exception $e) {
        $result->error = $e->getMessage();
    }
} else {
    $result->error = true;
}
$json_response = json_encode($result);
echo $json_response;
?>
