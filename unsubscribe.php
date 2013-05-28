<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


$user = new User();
$user = SessionUtil::checkLoggedinUser();
LanguageUtils::setUserLocale($user);

if (isset($_GET['guid'])) {
    $guid = "";
    if (isset($_GET["guid"])) {
        $guid = $_GET["guid"];
    }
    $guid = base64_decode($_GET["guid"]);
    $type = "e";
    if (!empty($guid)) {
        $array = explode(";", $guid);
        if (!empty($array) && sizeof($array) == 3) {
            $userId = $array[0];
            $userName = $array[1];
            if (!empty($userId) && !empty($userName)) {
                $user = UserUtils::getUserById($userId);
                if (!empty($user) && $user->userName == $userName) {
                    UserUtils::setUserWeeklyMail($userId,0);
                    $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_UNREGISTER_WEEKLY_MAIL");
                    $type = "i";
                } else {
                    $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_USER_DOESNT_EXIST");
                }
            } else {
                $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_USER_DOESNT_EXIST");
            }
        } else {
            $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_PARAMETERS_WRONG");
        }
    } else {
        $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_PARAMETERS_WRONG");
    }
    $_SESSION[INDEX_MSG_SESSION_KEY] = '';
    $m = new HtmlMessage();
    $m->type = $type;
    $m->message = $confirm_msg;
    $_SESSION[INDEX_MSG_SESSION_KEY] = json_encode($m);
    header('Location: ' . HOSTNAME);
    exit(1);
} else {
    header('Location: ' . HOSTNAME);
    exit(1);
}
?>
