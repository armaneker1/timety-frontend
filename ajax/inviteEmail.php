<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$result = new Result();
try {
    $query = null;
    if (isset($_POST["e"]))
        $query = $_POST["e"];

    $userId = null;
    if (isset($_POST["u"]))
        $userId = $_POST["u"];
    if (!UtilFunctions::check_email_address($query)) {
        // set locale
        LanguageUtils::setAJAXLocale();
        $result->success = false;
        $result->error = LanguageUtils::getText("LANG_AJAX_INVITE_MAIL_INVALID_EMAIL");
    } else if (!empty($userId)) {
        $user = UserUtils::getUserById($userId);
        if (!empty($user)) {
            LanguageUtils::setUserLocale($user);
            if ($user->email == $query) {
                $result->success = false;
                $result->error = LanguageUtils::getText("LANG_AJAX_INVITE_MAIL_ERROR_SAME_USER");
            } else {
                $result->success = false;
                $params = array(array('name', $user->firstName), array('surname', $user->lastName), array('username', $user->userName), array('bio', $user->about), array('email_address', $query));
                $res = MailUtil::sendSESMailFromFile(LanguageUtils::getLocale() . "_invite.html", $params, $query, LanguageUtils::getText("LANG_MAIL_INVITE_SUBJECT"));
                $result->success = true;
                $result->param = $res;
            }
        } else {
            LanguageUtils::setAJAXLocale();
            $result->success = false;
            $result->error = LanguageUtils::getText("LANG_AJAX_INVITE_MAIL_INVALID_USER");
        }
    }
} catch (Exception $e) {
    $result->success = false;
    $result->error = $e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>