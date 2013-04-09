<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

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
                        $result->pic =  $large_image_path . $_SESSION['user_file_ext'];
                    } else {
                        $result->error = false;
                        $result->success = true;
                        $result->pic = "http://graph.facebook.com/" . $provider->oauth_uid . "/picture?type=large";
                        $result->pic = UserUtils::changeserProfilePic($user->id, "http://graph.facebook.com/" . $provider->oauth_uid . "/picture?type=large", FACEBOOK_TEXT);
                    }
                } elseif ($provider->oauth_provider == TWITTER_TEXT && $type == 'tw') {
                    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                    $user_info = $twitteroauth->get('account/verify_credentials');
                    if (!isset($user_info->error)) {
                        if (!empty($crop)) {
                            $content = file_get_contents($user_info->profile_image_url);
                            if (!file_exists($upload_path)) {
                                mkdir($upload_path, 0777, true);
                            }
                            $large_image_location = $large_image_location .  ".png";
                            file_put_contents($large_image_location , $content);
                            $_SESSION['user_file_ext'] = ".png";
                            $result->error = false;
                            $result->success = true;
                            $result->pic =  $large_image_path . $_SESSION['user_file_ext'];
                        } else {
                            $userProfilePic = $user_info->profile_image_url;
                            $result->error = false;
                            $result->success = true;
                            $result->pic = $userProfilePic;
                            $result->pic = UserUtils::changeserProfilePic($user->id, $userProfilePic, TWITTER_TEXT);
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
