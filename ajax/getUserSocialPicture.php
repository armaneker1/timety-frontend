<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$result = new Result();
$result->error = true;
$result->success = false;
$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];


$type = null;
if (isset($_GET["type"]))
    $type = $_GET["type"];

$crop = null;
if (isset($_GET["crop"]))
    $crop = $_GET["crop"];


if (!empty($userId) && !empty($type) && ($type == 'fb' || $type == 'tw')) {
    if (!SessionUtil::isUser($userId)) {
        $res = new stdClass();
        $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }
    $user = UserUtils::getUserById($userId);
    if (!empty($user) && !empty($user->id)) {
        $socialProviders = $user->socialProviders;
        if (!empty($socialProviders)) {
            $provider = new SocialProvider();
            for ($i = 0; $i < sizeof($socialProviders); $i++) {
                $provider = $socialProviders[$i];
                if ($provider->oauth_provider == FACEBOOK_TEXT && $type == 'fb') {
                    if (!empty($crop)) {
                        $content = file_get_contents("http://graph.facebook.com/" . $provider->oauth_uid . "/picture?type=large");
                        if (!file_exists($upload_path)) {
                            mkdir($upload_path, 0777, true);
                        }
                        $large_image_location = $large_image_location . ".png";
                        file_put_contents($large_image_location, $content);
                        $_SESSION['user_file_ext'] = ".png";
                        $result->error = false;
                        $result->success = true;
                        $result->pic = $large_image_path . $_SESSION['user_file_ext'];
                        $result->width = -1;
                        $result->height = -1;
                        try {
                            $info = ImageUtil::getRealSize($large_image_location);
                            $result->width = $info[0];
                            $result->height = $info[1];
                        } catch (Exception $exc) {
                            error_log($exc->getTraceAsString());
                        }
                    } else {
                        $result->error = false;
                        $result->success = true;
                        $result->pic = "http://graph.facebook.com/" . $provider->oauth_uid . "/picture?type=large";
                        $result->pic = UserUtils::changeserProfilePic($user->id, "http://graph.facebook.com/" . $provider->oauth_uid . "/picture?width=106&height=106", FACEBOOK_TEXT, FALSE);
                    }
                } elseif ($provider->oauth_provider == TWITTER_TEXT && $type == 'tw') {
                    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                    $user_info = $twitteroauth->get('account/verify_credentials');
                    if (!isset($user_info->error)) {
                        if (!empty($crop)) {
                            $url = UserUtils::handleTwitterImage($user_info->profile_image_url);
                            $content = file_get_contents($url);
                            if (!file_exists($upload_path)) {
                                mkdir($upload_path, 0777, true);
                            }
                            $large_image_location = $large_image_location . ".png";
                            file_put_contents($large_image_location, $content);
                            $_SESSION['user_file_ext'] = ".png";
                            $result->error = false;
                            $result->success = true;
                            $result->pic = $large_image_path . $_SESSION['user_file_ext'];
                            $result->width = -1;
                            $result->height = -1;
                            try {
                                $info = ImageUtil::getRealSize($large_image_location);
                                $result->width = $info[0];
                                $result->height = $info[1];
                            } catch (Exception $exc) {
                                error_log($exc->getTraceAsString());
                            }
                        } else {
                            $userProfilePic = $user_info->profile_image_url;
                            $result->error = false;
                            $result->success = true;
                            $result->pic = $userProfilePic;
                            $result->pic = UserUtils::changeserProfilePic($user->id, $userProfilePic, TWITTER_TEXT, FALSE);
                        }
                    }
                }
            }
        }
    }
}

$json_response = json_encode($result);
echo $json_response;
?>
