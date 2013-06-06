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
        $tr_tags = Neo4jTimetyTagUtil::searchTags("", LANG_TR_TR);
        $en_tags = Neo4jTimetyTagUtil::searchTags("", LANG_EN_US);


        foreach ($socialProviders as $provider) {
            $oauthId = $provider->getOauthUid();
            $userId = $provider->getUserId();
            if (!empty($provider) && !empty($oauthId) && !empty($userId)) {
                $timetyUser = UserUtils::getUserById($userId);
                $exm = new TimeteUserDefaults();
                $exm->setUserId($userId);
                $timetyUserDefaults = TimeteUserDefaults::findByExample(DBUtils::getConnection(), $exm);
                if (!empty($timetyUserDefaults)) {
                    $timetyUserDefaults = $timetyUserDefaults[0];
                }
                if (!empty($timetyUser)) {
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
                                            $check = EventUtil::getEventByFacebookId($event['id']);
                                            if (empty($check)) {
                                                try {
                                                    $evt = $facebook->api('/' . $event['id'], array('fields' => 'id,cover,venue,name,location,privacy,start_time,end_time,description,owner'));

                                                    $evt_creaor_id = null;
                                                    if (isset($evt['owner']) &&
                                                            !empty($evt['owner']) &&
                                                            is_array($evt['owner']) &&
                                                            isset($evt['owner']['id']) &&
                                                            !empty($evt['owner']['id'])) {
                                                        $evt_creaor_id = $evt['owner']['id'];
                                                    }

                                                    if ($evt_creaor_id == $oauthId) {
                                                        $evt_obj = new Event();

                                                        if (isset($evt['name']))
                                                            $evt_obj->title = $evt['name'];
                                                        if (isset($evt['description']))
                                                            $evt_obj->description = $evt['description'];
                                                        else
                                                            $evt_obj->description = $evt['name'];

                                                        if (!empty($evt_obj->description) && strlen($evt_obj->description) > 255) {
                                                            $evt_obj->description = substr($evt_obj->description, 0, 255);
                                                        }

                                                        $evt_obj->privacy = 0;
                                                        if (isset($evt['privacy']) && $evt['privacy'] == "OPEN") {
                                                            $evt_obj->privacy = 1;
                                                        }
                                                        if (isset($evt['start_time']))
                                                            $evt_obj->startDateTime = date(DATETIME_DB_FORMAT, strtotime($evt['start_time']));
                                                        if (isset($evt['end_time']))
                                                            $evt_obj->endDateTime = date(DATETIME_DB_FORMAT, strtotime($evt['end_time']));
                                                        $evt_obj->addsocial_fb = 1;
                                                        if (isset($evt['id']))
                                                            $evt_obj->facebook_id = $evt['id'];
                                                        if (isset($evt['location']))
                                                            $evt_obj->location = $evt['location'];

                                                        if (isset($evt['venue'])) {
                                                            if (!empty($evt['venue']) && is_array($evt['venue']) && isset($evt['venue']['id'])) {
                                                                try {
                                                                    $ven = $facebook->api("/" . $evt['venue']['id']);
                                                                    if (isset($ven['location']) && !empty($ven['location']) && is_array($ven['location'])) {
                                                                        $l = $ven['location'];
                                                                        if (isset($l['latitude']) && !empty($l['latitude'])) {
                                                                            $evt_obj->loc_lat = $l['latitude'];
                                                                        }
                                                                        if (isset($l['longitude']) && !empty($l['longitude'])) {
                                                                            $evt_obj->loc_lng = $l['longitude'];
                                                                        }
                                                                        if (isset($l['country']) && !empty($l['country'])) {
                                                                            $evt_obj->loc_country = $l['country'];
                                                                        }
                                                                        if (empty($evt_obj->location) && isset($ven['name'])) {
                                                                            $evt_obj->location = $evt['venue']['name'];
                                                                        }
                                                                    }
                                                                } catch (Exception $exc) {
                                                                    echo "115 -><p/>";
                                                                    var_dump($exc);
                                                                    echo "115 -><p/>";
                                                                }
                                                            }
                                                        }

                                                        if (empty($evt_obj->location) && !empty($timetyUserDefaults)) {
                                                            $evt_obj->location = $timetyUserDefaults->getLocation();
                                                        }

                                                        if (empty($evt_obj->loc_city) && !empty($timetyUserDefaults)) {
                                                            $evt_obj->loc_city = $timetyUserDefaults->getLocationCity();
                                                        }

                                                        if (empty($evt_obj->loc_country) && !empty($timetyUserDefaults)) {
                                                            $evt_obj->loc_country = $timetyUserDefaults->getLocationCountry();
                                                        }

                                                        if ((empty($evt_obj->loc_lat) || empty($evt_obj->loc_lng)) && !empty($timetyUserDefaults)) {
                                                            $evt_obj->loc_lat = $timetyUserDefaults->getLocationCorX();
                                                            $evt_obj->loc_lng = $timetyUserDefaults->getLocationCorY();
                                                        }

                                                        $pic = null;
                                                        if (isset($evt['cover']) && !empty($evt['cover']) & is_array($evt['cover']) && isset($evt['cover']['source'])) {
                                                            $pic = $evt['cover']['source'];
                                                        }
                                                        if (!empty($pic)) {
                                                            try {
                                                                $filename = $userId . "_" . $oauthId . "_" . rand(10, 100) . ".png";
                                                                $dest_url = __DIR__ . '/../../uploads/' . $filename;
                                                                if (copy($pic, $dest_url)) {
                                                                    $evt_obj->headerImage = $filename;
                                                                }
                                                            } catch (Exception $exc) {
                                                                echo "116 -><p/>";
                                                                var_dump($exc);
                                                                echo "116 -><p/>";
                                                            }
                                                        }
                                                        $evt_obj->attach_link = "https://www.facebook.com/events/" . $event['id'];
                                                        $evt_obj->creatorId = $userId;

                                                        /*
                                                         * 
                                                         */
                                                        $evt_obj->allday = 0;
                                                        $evt_obj->repeat = 0;
                                                        $evt_obj->addsocial_fb = 1;
                                                        $evt_obj->addsocial_gg = 0;
                                                        $evt_obj->addsocial_tw = 0;
                                                        $evt_obj->addsocial_fq = 0;
                                                        $evt_obj->reminderType = "";
                                                        $evt_obj->reminderUnit = "";
                                                        $evt_obj->reminderValue = 0;
                                                        $evt_obj->attendance = null;
                                                        $evt_obj->worldwide = 0;

                                                        //Tags
                                                        $evt_obj->tags = null;
                                                        if (!empty($timetyUserDefaults)) {
                                                            $evt_obj->tags = $timetyUserDefaults->getEventTags();
                                                        }
                                                        $result = CreateEventUtil::createEvent($evt_obj);
                                                        var_dump($result);
                                                    }
                                                } catch (Exception $exc) {
                                                    echo "114 -><p/>";
                                                    var_dump($exc);
                                                    echo "114 -><p/>";
                                                }
                                            }else{
                                                var_dump("oley ".$event['id']);
                                            }
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
    }
} catch (Exception $exc) {
    echo "111 -><p/>";
    var_dump($exc);
    echo "111 -><p/>";
}
?>
