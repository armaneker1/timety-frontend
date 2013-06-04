<?php

ini_set('max_execution_time', 300);
$error_handling = true;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
            'cookie' => false,
            'fileUpload' => true
        ));

$defaultToken = "CAAFJZBsWslzEBAMhvVZCIMvrZAUrHvFSGtFMwRYlw6LmjZC0XE0F4vTpZA0KKdn8XVxhl4V2TJGiPhqoGTpjwpdZB8rGiNcnnlrt3LanHq5DZAN1ZAS2OZAnOnrAZAl93taGer7W0p9jevelDxlOF0iMvP";

$exm = new TimeteUserSocialprovider();
$exm->setSocialEventSync(1);
$exm->setOauthProvider(FACEBOOK_TEXT);

try {
    $socialProviders = TimeteUserSocialprovider::findByExample(DBUtils::getConnection(), $exm);
    if (!empty($socialProviders)) {
        foreach ($socialProviders as $provider) {
            $oauthId = $provider->getOauthUid();
            $userId = $provider->getUserId();
            if (!empty($provider) && !empty($oauthId) && !empty($userId)) {
                try {
                    $oauthToken = $provider->getOauthToken();
                    echo "<p/><h2> Events : " . $userId . " - " . $oauthId . "</h2>";

                    $user = null;
                    try {
                        if (!empty($oauthToken)) {
                            $facebook->setAccessToken($oauthToken);
                        } else {
                            $facebook->setAccessToken($defaultToken);
                        }
                        $user = $facebook->api('/me');
                    } catch (Exception $exc) {
                        $facebook->setAccessToken($defaultToken);
                    }
                    $fbEvents = $facebook->api('/' . $oauthId . '/events');
                    $check_events = true;
                    while ($check_events) {
                        if (isset($fbEvents['data'])) {
                            $events = $fbEvents['data'];
                            if (!empty($events) && is_array($events)) {
                                foreach ($events as $event) {
                                    if (!empty($event) && isset($event['id'])) {
                                        $evt = $facebook->api('/' . $event['id']);
                                        
                                        
                                        
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
                                        $fbEvents = $facebook->api('/' . $oauthId . '/' . $next);
                                        $check_events = true;
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $exc) {
                    echo "113 -><p/>";
                    var_dump($exc);
                    echo "113 -><p/>";
                }
            }
        }
    }
} catch (Exception $exc) {
    echo "111 -><p/>";
    var_dump($exc);
    echo "111 -><p/>";
}
?>
