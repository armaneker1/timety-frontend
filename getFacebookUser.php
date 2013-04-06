<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


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
    echo $e->getMessage();
}
if (!empty($user)) {
    // check username if exist return new username 
    $username = strtolower($user['username']);
    $result = UserUtils::checkUser($uid, 'facebook', $username, $access_token, null);
    $type = $result['type'];
    $user = new User();
    $user = $result['user'];
    if (!empty($user)) {
        $_SESSION['id'] = $user->id;
        $_SESSION['oauth_id'] = $uid;
        $_SESSION['username'] = $user->userName;
        $_SESSION['oauth_provider'] = 'facebook';
        setcookie(COOKIE_KEY_UN, base64_encode($user->userName), time() + (365 * 24 * 60 * 60), "/");
        setcookie(COOKIE_KEY_PSS, base64_encode($user->getPassword()), time() + (365 * 24 * 60 * 60), "/");
        setcookie(COOKIE_KEY_RM, true, time() + (365 * 24 * 60 * 60), "/");
        if ($type == 1) {
            if (isset($_SESSION["te_invitation_code"]) && !empty($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) {
                UtilFunctions::incInvitationCodeCount($_SESSION["te_invitation_code"]);
            }
            header("Location: " . HOSTNAME);
        } else if ($type == 2) {
            UtilFunctions::curl_post_async(PAGE_AJAX_FACEBOOK_USER_INTEREST, array("userId" => $user->id));
            header("Location: " . PAGE_ABOUT_YOU);
        }
    } else {
        header("Location: " . HOSTNAME);
    }
} else {
    header('Location: ' . PAGE_FB_LOGIN);
}
?>
