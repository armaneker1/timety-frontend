<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


$success = TRUE;
$errortext = "";

$l_user = SessionUtil::checkLoggedinUser();
LanguageUtils::setUserLocale($l_user);
if (!empty($l_user)) {
    $userFunctions = new UserUtils();
    $facebook = new Facebook(array(
                'appId' => FB_APP_ID,
                'secret' => FB_APP_SECRET,
                'cookie' => true
            ));

    try {
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
        $access_token = $facebook->getAccessToken();
    } catch (Exception $e) {
        echo LanguageUtils::getText("LANG_PAGE_ADD_FB_ERROR") . $e->getMessage();
    }
    if (!empty($user)) {
        try {
            $fcUser = $userFunctions->getSocialProviderWithOAUTHId($uid, FACEBOOK_TEXT);
            if (empty($fcUser)) {
                $provider = new SocialProvider();
                $provider->oauth_provider = FACEBOOK_TEXT;
                $provider->oauth_token = $access_token;
                $provider->oauth_uid = $uid;
                $provider->status = 0;
                $provider->user_id = $l_user->id;

                $userFunctions->updateSocialProvider($provider);
            } else {
                $success = FALSE;
                $errortext = LanguageUtils::getText("LANG_PAGE_ADD_FB_USER_TAKEN");
            }
        } catch (Exception $e) {
            echo  LanguageUtils::getText("LANG_PAGE_ADD_FB_ERROR") . $e->getMessage();
        }
    } else {
        echo LanguageUtils::getText("LANG_PAGE_ADD_FB_USER_EMPTY")." 001";
    }
} else {
    echo LanguageUtils::getText("LANG_PAGE_ADD_FB_USER_EMPTY")." 002";
}
?>
<head><?php
$timety_header = LanguageUtils::getText("LANG_PAGE_ADD_FB_TITLE");
LanguageUtils::setLocaleJS($l_user);
include('layout/layout_header.php');
?></head>
<?php if ($success) { ?>
    <body onload="window.close();window.opener.document.getElementById('addSocialReturnButton').click();" ></body>
<?php } else { ?>
    <body onload="window.close();jQuery(window.opener.document.getElementById('addSocialErrorReturnButton')).attr('errortext','<?= $errortext ?>');window.opener.document.getElementById('addSocialErrorReturnButton').click();" ></body>
<?php } ?>
