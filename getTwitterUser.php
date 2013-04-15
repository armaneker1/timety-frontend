<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
    $_SESSION['access_token'] = $access_token;
    $user_info = $twitteroauth->get('account/verify_credentials');
    if (isset($user_info->error)) {
        // Something's wrong, go back to square 1
        RegisterAnaliticsUtils::increasePageRegisterCount("getTwitterUser.php?denied=1");
        header('Location: ' . PAGE_TW_LOGIN);
    } else {
        $uid = $user_info->id;
        // check username if exist return new username
        $username = strtolower($user_info->screen_name);

        $result = UserUtils::checkUser($uid, 'twitter', $username, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $type = $result['type'];
        $user = new User();
        $user = $result['user'];
        if (!empty($user)) {
            SessionUtil::storeLoggedinUser($user);
            if ($type == 1) {
                RegisterAnaliticsUtils::increasePageRegisterCount("getTwitterUser.php?login=1");
                if (isset($_SESSION["te_invitation_code"]) && !empty($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) {
                    UtilFunctions::incInvitationCodeCount($_SESSION["te_invitation_code"]);
                }
                header("Location: " . HOSTNAME);
            } else if ($type == 2) {
                RegisterAnaliticsUtils::increasePageRegisterCount("getTwitterUser.php?signup=1");
                UtilFunctions::curl_post_async(PAGE_AJAX_TWITTER_USER_INTEREST, array("userId" => $user->id));
                header("Location: " . PAGE_ABOUT_YOU);
            }
        } else {
            header("Location: " . HOSTNAME);
        }
    }
} else {
    // Something's missing, go back to square 1
    header('Location: ' . PAGE_TW_LOGIN);
}
?>
