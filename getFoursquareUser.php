<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


$success = TRUE;
$errortext = "";


if (isset($_GET['error'])) {
    header('Location: ' . PAGE_LOGIN);
} else if (isset($_GET['add'])) {

    if (isset($_SESSION['id'])) {
        $l_user = UserUtils::getUserById($_SESSION['id']);

        $foursquare = new FoursquareAPI(FQ_CLIENT_ID, FQ_CLIENT_SECRET);
        $token = $foursquare->GetToken($_GET['code'], HOSTNAME . FQ_CALLBACK_URL);
        if (!empty($token)) {
            try {
                $foursquare->SetAccessToken($token);
                $res = $foursquare->GetPrivate("users/self");
                $details = json_decode($res);
                $res = $details->response;
                $user = $res->user;


                $fcUser = UserUtils::getSocialProviderWithOAUTHId($user->id, FOURSQUARE_TEXT);
                if (empty($fcUser)) {
                    $provider = new SocialProvider();
                    $provider->oauth_provider = FOURSQUARE_TEXT;
                    $provider->oauth_token = $token;
                    $provider->oauth_uid = $user->id;
                    $provider->status = 0;
                    $provider->user_id = $l_user->id;

                    UserUtils::updateSocialProvider($provider);
                } else {
                    $success = FALSE;
                    $errortext = "Foursquare account exists!";
                }
            } catch (Exception $e) {
                echo 'Error -> ' . $e->getMessage();
            }
        } else {
            echo "User empty1";
        }
    } else {
        echo "User empty2";
    }
    include('layout/layout_header.php');
    if ($success) {
        echo "<body onload=\"window.close();window.opener.document.getElementById('addSocialReturnButton').click();\"></body>";
    } else {
        echo "<body onload=\"window.close();jQuery(window.opener.document.getElementById('addSocialErrorReturnButton')).attr('errortext','" . $errortext . "');window.opener.document.getElementById('addSocialErrorReturnButton').click();\"></body>";
    }
} else {
    $foursquare = new FoursquareAPI(FQ_CLIENT_ID, FQ_CLIENT_SECRET);
    $token = $foursquare->GetToken($_GET['code'], HOSTNAME . FQ_CALLBACK_URL);
    if (!empty($token)) {
        try {
            $foursquare->SetAccessToken($token);
            $res = $foursquare->GetPrivate("users/self");
            $details = json_decode($res);
            $res = $details->response;
            $user = $res->user;
            $uid = $user->id;
            // check username if exist return new username
            $username = strtolower($user->firstName . $user->lastName);
            $access_token = $token;

            $result = UserUtils::checkUser($uid, 'foursquare', $username, $access_token, null);


            $type = $result['type'];
            $user = new User();
            $user = $result['user'];

            if (!empty($user)) {
                SessionUtil::storeLoggedinUser($user);
                if ($type == 1) {
                    RegisterAnaliticsUtils::increasePageRegisterCount("getFoursquareUser.php?login=1");
                    if (isset($_SESSION["te_invitation_code"]) && !empty($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) {
                        UtilFunctions::incInvitationCodeCount($_SESSION["te_invitation_code"]);
                    }
                    header("Location: " . HOSTNAME);
                } else if ($type == 2) {
                    RegisterAnaliticsUtils::increasePageRegisterCount("getFoursquareUser.php?signup=1");
                    header("Location: " . PAGE_ABOUT_YOU);
                }
            } else {
                header("Location: " . HOSTNAME);
            }
        } catch (Exception $e) {
            echo 'Error -> ' . $e->getMessage();
        }
    } else {
        header('Location: ' . PAGE_FQ_LOGIN);
    }
}
?>
