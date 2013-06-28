<?php

session_start();
session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];

$ajax_guid = null;
if (isset($_GET["ajax_guid"]))
    $ajax_guid = $_GET["ajax_guid"];
if (isset($_POST["ajax_guid"]))
    $ajax_guid = $_POST["ajax_guid"];

if (!empty($userId)) {
    if (!SessionUtil::isUser($userId) && !SessionUtil::checkAjaxGUID($ajax_guid)) {
        $res = new stdClass();
        $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }

    $provider = UserUtils::getSocialProvider($userId, FACEBOOK_TEXT);
    if (!empty($provider) && sizeof($provider) > 0) {
        $provider = $provider[0];
        $facebook = new Facebook(array(
                    'appId' => FB_APP_ID,
                    'secret' => FB_APP_SECRET,
                    'cookie' => true
                ));
        $facebook->setAccessToken($provider->oauth_token);
        $result = $facebook->api('/me/events');
        if (!empty($result['data']) && sizeof($result['data']) > 0) {
            $events = $result['data'];
            foreach ($events as $event) {
                echo "<h1>" . $event['name'] . " (" . $event['id'] . ")</h1>";
                //var_dump($facebook->api('/'.$event['id']));

                $params = array(
                    'method' => 'fql.query',
                    'query' => 'select name, pic_big,host, description, start_time, end_time, location, venue,privacy,ticket_uri from event where eid="' . $event['id'] . '"'
                );

                var_dump($facebook->api($params));
            }
        }
    }
}
?>
