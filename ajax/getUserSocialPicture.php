<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

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


if (!empty($userId) && !empty($type) && ($type == 'fb' || $type == 'tw')) {
    $user = UserUtils::getUserById($userId);
    if (!empty($user) && !empty($user->id)) {
        $socialProviders = $user->socialProviders;
        if (!empty($socialProviders)) {
            $provider = new SocialProvider();
            for ($i = 0; $i < sizeof($socialProviders); $i++) {
                $provider = $socialProviders[$i];
                if ($provider->oauth_provider == FACEBOOK_TEXT && $type == 'fb') {
                    $result->error = false;
                    $result->success = true;
                    $result->pic = "http://graph.facebook.com/" . $provider->oauth_uid . "/picture?type=large";
                    UserUtils::changeserProfilePic($user->id, "http://graph.facebook.com/" . $provider->oauth_uid . "/picture?type=large");
                } elseif ($provider->oauth_provider == TWITTER_TEXT && $type == 'tw') {
                    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                    $user_info = $twitteroauth->get('account/verify_credentials');
                    if (!isset($user_info->error)) {
                       // var_dump($user_info);
                        $userProfilePic = $user_info->profile_image_url;
                        $result->error = false;
                        $result->success = true;
                        $result->pic = $userProfilePic;
                        UserUtils::changeserProfilePic($user->id, $userProfilePic);
                    }
                }
            }
        }
    }
}

$json_response = json_encode($result);
echo $json_response;
?>
