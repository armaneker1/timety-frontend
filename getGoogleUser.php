<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_Oauth2Service.php';


$success = TRUE;
$errortext = "";
LanguageUtils::setLocale(null);
if (isset($_GET['error'])) {
    RegisterAnaliticsUtils::increasePageRegisterCount("getGoogleUser.php?denied=1");
    header('Location: ' . PAGE_SIGNUP);
} else {
    $google = new Google_Client();
    $google->setApplicationName(GG_APP_NAME);
    $google->setClientId(GG_CLIENT_ID);
    $google->setClientSecret(GG_CLIENT_SECRET);
    $google->setRedirectUri(HOSTNAME . GG_CALLBACK_URL);
    $google->setDeveloperKey(GG_DEVELOPER_KEY);
    try {
        $oauth2 = new Google_Oauth2Service($google);
        if (isset($_GET['code'])) {
            $google->authenticate();
            $_SESSION['gg_access_token'] = $google->getAccessToken();
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
            exit(-1);
        }
        if (isset($_SESSION['gg_access_token'])) {
            $google->setAccessToken($_SESSION['gg_access_token']);
        }
        $access = $google->getAccessToken();
        if ($access) {
            $me = $oauth2->userinfo->get();
            if (!empty($me)) {
                $email = $me['email'];
                $userId = $me['id'];
                $userName = $me['name'];
                $userName = strtolower($userName);
                $userName = str_replace(" ", "", $userName);
                if (isset($_SESSION["gg_type_social"]) && $_SESSION["gg_type_social"] == "add") {
                    if (isset($_SESSION['id'])) {
                        $l_user = UserUtils::getUserById($_SESSION['id']);
                        try {
                            $ggUser = UserUtils::getSocialProviderWithOAUTHId($userId, GOOGLE_PLUS_TEXT);
                            if (empty($ggUser)) {
                                $provider = new SocialProvider();
                                $provider->oauth_provider = GOOGLE_PLUS_TEXT;
                                $provider->oauth_token = $access;
                                $provider->oauth_uid = $userId;
                                $provider->status = 0;
                                $provider->user_id = $l_user->id;
                                UserUtils::updateSocialProvider($provider);
                            } else {
                                $success = FALSE;
                                $errortext = LanguageUtils::getText("LANG_PAGE_GET_GOOGLE_USER_ERROR_TAKEN");
                            }
                        } catch (Exception $e) {
                            $errortext = LanguageUtils::getText("LANG_PAGE_GET_GOOGLE_USER_ERROR") . $e->getMessage();
                        }
                    } else {
                        echo LanguageUtils::getText("LANG_PAGE_GET_GOOGLE_USER_EMPT_USER")." 001";
                    }
                    $timety_header = LanguageUtils::getText("LANG_PAGE_GET_GOOGLE_USER_TITLE");
                    LanguageUtils::setLocaleJS();
                    include('layout/layout_header.php');
                    if ($success) {
                        echo "<body onload=\"window.close();window.opener.document.getElementById('addSocialReturnButton').click();\"></body>";
                    } else {
                        echo "<body onload=\"window.close();jQuery(window.opener.document.getElementById('addSocialErrorReturnButton')).attr('errortext','" . $errortext . "');window.opener.document.getElementById('addSocialErrorReturnButton').click();\"></body>";
                    }
                } else {
                    $result = UserUtils::checkUser($userId, GOOGLE_PLUS_TEXT, $userName, $access, null);
                    $type = $result['type'];
                    $user = new User();
                    $user = $result['user'];
                    if (!empty($user)) {
                        SessionUtil::storeLoggedinUser($user);
                        if ($type == 1) {
                            RegisterAnaliticsUtils::increasePageRegisterCount("getGoogleUser.php?login=1");
                            if (isset($_SESSION["te_invitation_code"]) && !empty($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) {
                                UtilFunctions::incInvitationCodeCount($_SESSION["te_invitation_code"]);
                            }
                            header("Location: " . HOSTNAME);
                        } else if ($type == 2) {
                            RegisterAnaliticsUtils::increasePageRegisterCount("getGoogleUser.php?signup=1");
                            header("Location: " . PAGE_ABOUT_YOU);
                        }
                    } else {
                        header("Location: " . HOSTNAME);
                    }
                }
            } else {
                //header('Location: ' . PAGE_FQ_LOGIN);
            }
        } else {
            //header('Location: ' . PAGE_FQ_LOGIN);
        }
    } catch (Exception $exc) {
        var_dump($exc);
        //header('Location: ' . PAGE_FQ_LOGIN);
    }
}
?>
