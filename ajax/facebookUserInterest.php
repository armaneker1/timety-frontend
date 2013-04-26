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

$fb_treshhold = 3;

$ajax_guid = null;
if (isset($_GET["ajax_guid"]))
    $ajax_guid = $_GET["ajax_guid"];
if (isset($_POST["ajax_guid"]))
    $ajax_guid = $_POST["ajax_guid"];

if (!empty($userId)) {
    if (!SessionUtil::isUser($userId) && !SessionUtil::checkAjaxGUID($ajax_guid)) {
        $res = new stdClass();
        $res->error = "user not logged in";
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }
    $log->logInfo("Facebook User Id : " . $userId);
    $provider = new SocialProvider();
    $provider = UserUtils::getSocialProvider($userId, FACEBOOK_TEXT);
    if (!empty($provider) && sizeof($provider) > 0) {
        $provider = $provider[0];
        if (!empty($provider->oauth_token) && !empty($provider->oauth_uid)) {
            try {
                $facebook = new Facebook(array(
                            'appId' => FB_APP_ID,
                            'secret' => FB_APP_SECRET,
                            'cookie' => true
                        ));
                $facebook->setAccessToken($provider->oauth_token);
                $likes_data = $facebook->api('/me/likes');
                $cats = array();
                $count = array();
                $r_cats = array();
                if (!empty($likes_data)) {
                    if (isset($likes_data['data'])) {
                        $likes = $likes_data['data'];
                        if (!empty($likes)) {
                            foreach ($likes as $like) {
                                if (!empty($like)) {
                                    if (isset($like['category'])) {
                                        $name = strtolower($like['category']);
                                        if (!in_array($name, $cats)) {
                                            array_push($cats, $name);
                                            $count[$name] = 1;
                                        } else {
                                            $count[$name] = $count[$name] + 1;
                                            if ($count[$name] >= $fb_treshhold && !in_array($name, $r_cats)) {
                                                array_push($r_cats, $name);
                                            }
                                        }
                                    }
                                    if (isset($like['category_list']) && sizeof($like['category_list']) > 0) {
                                        $l_c = $like['category_list'];
                                        foreach ($l_c as $l) {
                                            if (!empty($l)) {
                                                $name = strtolower($l['name']);
                                                if (!empty($name)) {
                                                    if (!in_array($name, $cats)) {
                                                        array_push($cats, $name);
                                                        $count[$name] = 1;
                                                    } else {
                                                        $count[$name] = $count[$name] + 1;
                                                        if ($count[$name] >= $fb_treshhold && !in_array($name, $r_cats)) {
                                                            array_push($r_cats, $name);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $array = FacebookUtils::getTimetyTagsFacebook($r_cats);
                            if (!empty($array) && sizeof($array) > 0) {
                                foreach ($array as $tag) {
                                    $td_id = $tag->getTagId();
                                    if (!empty($td_id)) {
                                        $tags = explode(',', $td_id);
                                        foreach ($tags as $tag) {
                                            if (!empty($tag)) {
                                                Neo4jUserUtil::addUserTag($userId, $tag);
                                            }
                                        }
                                    }
                                }
                            } else {
                                $log->logError("User interest none");
                            }
                        } else {
                            $log->logError("User facebook like empty 2");
                        }
                    } else {
                        var_dump($likes_data);
                    }
                } else {
                    $log->logError("User facebook like empty ");
                }
            } catch (Exception $exc) {
                var_dump($exc);
                $log->logError("User facebook like error" . $exc->getTraceAsString());
            }
        } else {
            $log->logError("User facebook provider empty 2");
        }
    } else {
        $log->logError("User facebook provider empty ");
    }
} else {
    $log->logError("User Id empty ");
}
?>
