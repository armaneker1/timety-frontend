<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

if (isset($_GET['userId']))
    $userId = $_GET['userId'];
if (isset($_POST['userId']))
    $userId = $_POST['userId'];

$tw_treshhold = 5;

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
    $log->logInfo("Twiiter User Id : " . $userId);
    $provider = new SocialProvider();
    $provider = UserUtils::getSocialProvider($userId, TWITTER_TEXT);
    if (!empty($provider) && sizeof($provider) > 0) {
        $provider = $provider[0];
        if (!empty($provider->oauth_token) && !empty($provider->oauth_token_secret) && !empty($provider->oauth_uid)) {
            $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
            $response = $twitteroauth->get('friends/ids');
            if (isset($response->error)) {
                $log->logError("Error " . json_encode($response->error));
            } else {
                if (isset($response->ids) && !empty($response->ids) && is_array($response->ids) && sizeof($response->ids) > 0) {
                    $ids = $response->ids;
                    $array = TwiiterUtils::getTimetyTagsTwitter($ids);
                    if (!empty($array) && sizeof($array) > 0) {
                        foreach ($array as $tag) {
                            if (!empty($tag)) {
                                if ($tw_treshhold <= (int) $tag->getTwId()) {
                                    $td_id = $tag->getTagId();
                                    if (!empty($td_id)) {
                                        $tags = explode(',', $td_id);
                                        foreach ($tags as $tag) {
                                            if (!empty($tag)) {
                                                Neo4jUserUtil::addUserTag($userId, $tag);
                                            }
                                        }
                                    }
                                } else {
                                    $log->logError("tag " . $tag->getTagId() . " not interest  " . $tag->getTwId() . "<" . $tw_treshhold);
                                }
                            }
                        }
                    } else {
                        $log->logError("User interest none");
                    }
                } else {
                    $log->logError("User follow list empty");
                }
            }
        } else {
            $log->logError("User twitter provider empty 2");
        }
    } else {
        $log->logError("User twitter provider empty ");
    }
} else {
    $log->logError("User Id empty ");
}
?>
