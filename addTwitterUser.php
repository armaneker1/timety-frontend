<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


$success = TRUE;
$errortext = "";
$l_user = SessionUtil::checkLoggedinUser();
LanguageUtils::setUserLocale($l_user);
if (!empty($l_user) && !empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {

    $userFunctions = new UserUtils();

    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
    $_SESSION['access_token'] = $access_token;
    $user_info = $twitteroauth->get('account/verify_credentials');
    if (isset($user_info->error)) {
        // Something's wrong, go back to square 1
        echo LanguageUtils::getText("LANG_PAGE_ADD_TW_USER_EMPTY") . " 001";
    } else {
        $uid = $user_info->id;
        $fcUser = $userFunctions->getSocialProviderWithOAUTHId($uid, TWITTER_TEXT);
        if (empty($fcUser)) {
            $provider = new SocialProvider();
            $provider->oauth_provider = TWITTER_TEXT;
            $provider->oauth_token = $access_token['oauth_token'];
            $provider->oauth_token_secret = $access_token['oauth_token_secret'];
            $provider->oauth_uid = $uid;
            $provider->status = 0;
            $provider->user_id = $l_user->id;

            $userFunctions->updateSocialProvider($provider);
        } else {
            $success = FALSE;
            $errortext = LanguageUtils::getText("LANG_PAGE_ADD_TW_USER_TAKEN");
        }
    }
} else {
    // Something's missing, go back to square 1
    echo LanguageUtils::getText("LANG_PAGE_ADD_TW_USER_EMPTY") . " 002";
}
?>
<head><?php $timety_header = LanguageUtils::getText("LANG_PAGE_ADD_TW_TITLE");
include('layout/layout_header.php'); ?></head>
<?php if ($success) { ?>
    <body onload="window.close();window.opener.document.getElementById('addSocialReturnButton').click();" ></body>
<?php } else { ?>
    <body onload="window.close();jQuery(window.opener.document.getElementById('addSocialErrorReturnButton')).attr('errortext','<?= $errortext ?>');window.opener.document.getElementById('addSocialErrorReturnButton').click();" ></body>
<?php } ?>
