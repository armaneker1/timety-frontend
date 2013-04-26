<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
LanguageUtils::setLocale();

$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
            'cookie' => true
        ));

if (isset($_GET['error']) || isset($_GET['error_reason']) || isset($_GET['error_description'])) {
    RegisterAnaliticsUtils::increasePageRegisterCount("getFacebookUser.php?denied=1");
    header('Location: ' . PAGE_SIGNUP);
    exit(1);
}

try {
    $uid = $facebook->getUser();
    $user = $facebook->api('/me');
    $access_token = $facebook->getAccessToken();
} catch (Exception $e) {
    echo LanguageUtils::getText("LANG_PAGE_GET_FACEBOOK_ERROR").$e->getMessage();
}
if (!empty($user)) {
    // check username if exist return new username 
    $username = strtolower($user['username']);
    $result = UserUtils::checkUser($uid, 'facebook', $username, $access_token, null);
    $type = $result['type'];
    $user = new User();
    $user = $result['user'];
    if (!empty($user)) {
        SessionUtil::storeLoggedinUser($user);
        if ($type == 1) {
            RegisterAnaliticsUtils::increasePageRegisterCount("getFacebookUser.php?login=1");
            if (isset($_SESSION["te_invitation_code"]) && !empty($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) {
                UtilFunctions::incInvitationCodeCount($_SESSION["te_invitation_code"]);
            }
            header("Location: " . HOSTNAME);
        } else if ($type == 2) {
            RegisterAnaliticsUtils::increasePageRegisterCount("getFacebookUser.php?signup=1");
            UtilFunctions::curl_post_async(PAGE_AJAX_FACEBOOK_USER_INTEREST, array("userId" => $user->id,"ajax_guid"=>  SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
            header("Location: " . PAGE_ABOUT_YOU);
        }
    } else {
       header('Location: ' . PAGE_SIGNUP);
    }
} else {
    header('Location: ' . PAGE_SIGNUP);
}
?>
