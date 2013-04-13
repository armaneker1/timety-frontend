<?php

ini_set('max_execution_time', 300);


session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$fbProv = UserUtils::getSocialProviderWithOAUTHId(681274420, FACEBOOK_TEXT);
 

$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
            'cookie' => true
        ));

$facebook->setAccessToken($fbProv->oauth_token);

$attachment = array(
    'access_token' => $fbProv->oauth_token,
    'message' => "Test",
    'name' => "Test",
    'description' => "Test",
    'link' => "http://google.com",
    'actions' => array('name' => 'Try it now', 'link' => "http://google.com")
);

try {
    $post_id = $facebook->api("me/feed", "POST", $attachment);
    var_dump($post_id);
} catch (Exception $e) {
    var_dump($e);
}
?>
