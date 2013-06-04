<?php

ini_set('max_execution_time', 300);
$error_handling = true;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$facebook = new Facebook(array(
            'appId' => "549746215053485",
            'secret' => "67617b67305a94d131ca378c0f71e541",
            'cookie' => false,
            'fileUpload' => true
        ));


//hafiz
$facebook->setAccessToken("CAAHzZCcPdaK0BABLbsMnz6ZCMOyePQhhtt4gw9nQ8SEXT3Ssi716WgsBqQA7wgoQI4Q8xsZA4OrCfC6TSyg179pkqHgFkFUN92rohARgD1PTCjpptJzxpB8KpCxZBvMM2MO5S3KpZCxExnOVb6oivOJt0BtfgWikZD");


//benim
//$facebook->setAccessToken("CAAHzZCcPdaK0BAGnqUuj1ARURzHQdMJBBryOL3IpTX716AahAnLd9yPIzjAfsaMmgAZC5GBgak6XZA1bBZB8zuFUNoOCJFnCHh47ZBA94O5MWIIZCXRT9nY3XBMTmot61iLWfawhE5WDHlHzbLymEZB");

$fbUser = $facebook->api('/me');
//$fbFriends = $facebook->api('/me/friends');
//var_dump($fbFriends);
//$fbEvents = $facebook->api('/me/events');
//var_dump($fbEvents);

try {
    $fbLikes = $facebook->api('/me/likes');

    $check = true;
    while ($check) {
        if (isset($fbLikes['data'])) {
            $likes = $fbLikes['data'];
            if (!empty($likes) && is_array($likes)) {
                foreach ($likes as $like) {
                    if (!empty($like) && isset($like['id']) && !empty($like['id'])) {
                        $like_id = $like['id'];
                        $like_name = "";
                        if (isset($like['name'])) {
                            $like_name = $like['name'];
                        }
                        echo "<p/><h2> Events : " . $like_name . " - " . $like_id . "</h2>";
                        $fbEvents = $facebook->api('/' . $like_id . '/events');
                        $check_events = true;
                        while ($check_events) {
                            if (isset($fbEvents['data'])) {
                                $events = $fbEvents['data'];
                                if (!empty($events) && is_array($events)) {
                                    foreach ($events as $event) {
                                        if (!empty($event) && isset($event['id'])) {
                                            $evt = $facebook->api('/' . $event['id']);
                                            var_dump($evt);
                                        }
                                    }
                                }
                            } else {
                                $check_events = false;
                            }

                            /*
                             * Next Page if exist
                             */
                            $check_events = false;
                            if (isset($fbEvents['paging'])) {
                                $paging = $fbEvents['paging'];
                                if (!empty($paging) && is_array($paging)) {
                                    if (isset($paging['next'])) {
                                        $next = $paging['next'];
                                        if (!empty($next)) {
                                            if (strrpos($next, "events")) {
                                                $next = substr($next, strrpos($next, "events"));
                                            }
                                            $fbEvents = $facebook->api('/' . $like_id . '/' . $next);
                                            $check_events = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            /*
             * Next Page if exist
             */
            $check = false;
            if (isset($fbLikes['paging'])) {
                $paging = $fbLikes['paging'];
                if (!empty($paging) && is_array($paging)) {
                    if (isset($paging['next'])) {
                        $next = $paging['next'];
                        if (!empty($next)) {
                            if (strrpos($next, "likes")) {
                                $next = substr($next, strrpos($next, "likes"));
                            }
                            $fbLikes = $facebook->api('/me/' . $next);
                            $check = true;
                        }
                    }
                }
            }
        } else {
            $check = false;
        }
    }
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
?>
