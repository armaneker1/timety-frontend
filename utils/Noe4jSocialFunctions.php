<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Cypher;

class SocialUtil {

    public static function likeEvent($userId, $eventId) {
        $result = new Result();
        $result->success = false;
        $result->error = true;
        $res = SocialUtil::checkLike($userId, $eventId);
        if (!empty($res) && sizeof($res) == 3) {
            $rel = $res['rel'];
            $usr = $res['user'];
            $event = $res['event'];
        }
        if (!empty($event) && !empty($usr)) {
            try {
                $extra = false;
                if (empty($rel)) {
                    $usr->relateTo($event, REL_EVENTS_LIKE)->save();
                    $extra = true;
                }
                $result->success = true;
                $result->error = false;
                Queue::likeEvent($eventId, $userId, REDIS_USER_INTERACTION_LIKE, $extra);
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
                $result->error = $e->getTraceAsString();
            }
        } else {
            $result->success = false;
            $result->error = true;
        }
        return $result;
    }

    public static function revertLikeEvent($userId, $eventId) {
        $result = new Result();
        $result->success = false;
        $result->error = true;
        if (!empty($userId) && !empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "'), " .
                        " event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                        " MATCH (user) -[r:" . REL_EVENTS_LIKE . "]- (event) " .
                        " DELETE  r";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
                $result->success = true;
                $result->error = false;
                Queue::likeEvent($eventId, $userId, REDIS_USER_INTERACTION_UNLIKE);
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
                $result->error = $e->getTraceAsString();
            }
        } else {
            $result->success = false;
            $result->error = true;
        }
        return $result;
    }

    public static function reshareEvent($userId, $eventId) {
        $result = new Result();
        $result->success = false;
        $result->error = true;

        $res = SocialUtil::checkReshare($userId, $eventId);
        if (!empty($res) && sizeof($res) == 3) {
            $rel = $res['rel'];
            $usr = $res['user'];
            $event = $res['event'];
        }
        if (!empty($event) && !empty($usr)) {
            try {
                $extra = false;
                if (empty($rel)) {
                    $usr->relateTo($event, REL_EVENTS_RESHARE)->save();
                    $extra = true;
                }
                $result->success = true;
                $result->error = false;
                Queue::reshareEvent($eventId, $userId, REDIS_USER_INTERACTION_RESHARE);
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
                $result->error = $e->getTraceAsString();
            }
        } else {
            $result->success = false;
            $result->error = true;
        }
        return $result;
    }

    public static function revertReshareEvent($userId, $eventId) {
        $result = new Result();
        $result->success = false;
        $result->error = true;
        if (!empty($userId) && !empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "'), " .
                        " event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                        " MATCH (user) -[r:" . REL_EVENTS_RESHARE . "]- (event) " .
                        " DELETE  r";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
                $result->success = true;
                $result->error = false;
                Queue::reshareEvent($eventId, $userId, REDIS_USER_INTERACTION_UNSHARE);
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
                $result->error = $e->getTraceAsString();
            }
        } else {
            $result->success = false;
            $result->error = true;
        }
        return $result;
    }

    public static function incReshareCountAsync($userId, $eventId) {
        SocialUtil::calcEventCounter($userId, $eventId, PROP_INTEREST_RESHARE_COUNT, 1);
    }

    public static function decReshareCountAsync($userId, $eventId) {
        SocialUtil::calcEventCounter($userId, $eventId, PROP_INTEREST_RESHARE_COUNT, -1);
    }

    public static function incJoinCountAsync($userId, $eventId) {
        SocialUtil::calcEventCounter($userId, $eventId, PROP_INTEREST_JOIN_COUNT, 1);
    }

    public static function decJoinCountAsync($userId, $eventId) {
        SocialUtil::calcEventCounter($userId, $eventId, PROP_INTEREST_JOIN_COUNT, -1);
    }

    public static function incLikeCountAsync($userId, $eventId) {
        SocialUtil::calcEventCounter($userId, $eventId, PROP_INTEREST_LIKE_COUNT, 1);
    }

    public static function decLikeCountAsync($userId, $eventId) {
        SocialUtil::calcEventCounter($userId, $eventId, PROP_INTEREST_LIKE_COUNT, -1);
    }

    public static function calcEventCounter($userId, $eventId, $property, $type) {
        if (!empty($userId) && !empty($eventId)) {
            $nresult = Neo4jEventUtils::getEventTimetyTags($eventId);
            $array = array();
            foreach ($nresult as $row) {
                $tagId = $row->getProperty(PROP_TIMETY_TAG_ID);
                $lang = $row->getProperty(PROP_TIMETY_LANG);
                if (!in_array($tagId, $array)) {
                    array_push($array, $tagId);
                    SocialUtil::checkUserInterestTag($userId, $tagId, $property, $type, $lang);
                }
            }
        }
    }

    public static function checkUserInterestTag($userId, $tagId, $property, $type = null, $lang = null) {
        if (($lang != LANG_EN_US && $lang != LANG_TR_TR) || empty($lang)) {
            $lang = LANG_EN_US;
        }
        if (!empty($userId) && !empty($tagId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START tag=node:" . IND_TIMETY_TAG . "_" . $lang . "('" . PROP_TIMETY_TAG_ID . ":" . $tagId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  user-[r:" . REL_TIMETY_INTERESTS . "]->tag" .
                    " RETURN r";
            //echo $query;
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            $relation = null;
            foreach ($nresult as $row) {
                $relation = $row[0];
                break;
            }
            if (empty($relation)) {
                $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
                $tr_tagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG . "_" . LANG_TR_TR);
                $en_tagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG . "_" . LANG_EN_US);
                $usr = $userIndex->findOne(PROP_USER_ID, $userId);
                $tag_tr = $tr_tagIndex->findOne(PROP_OBJECT_ID, $tagId);
                $tag_en = $en_tagIndex->findOne(PROP_OBJECT_ID, $tagId);
                if (!empty($usr) && !empty($tag_tr)) {
                    $weight = 1;
                    $joinCount = 0;
                    $likeCount = 0;
                    $reshareCount = 0;
                    if ($type == 1) {
                        if ($property == PROP_INTEREST_JOIN_COUNT) {
                            $joinCount = 1;
                        } else if ($property == PROP_INTEREST_LIKE_COUNT) {
                            $likeCount = 1;
                        } else if ($property == PROP_INTEREST_RESHARE_COUNT) {
                            $reshareCount = 1;
                        }
                    }
                    $usr->relateTo($tag_tr, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, $weight)->setProperty(PROP_INTEREST_JOIN_COUNT, $joinCount)->setProperty(PROP_INTEREST_LIKE_COUNT, $likeCount)->setProperty(PROP_INTEREST_RESHARE_COUNT, $reshareCount)->save();
                }
                if (!empty($usr) && !empty($tag_en)) {
                    $weight = 1;
                    $joinCount = 0;
                    $likeCount = 0;
                    $reshareCount = 0;
                    if ($type == 1) {
                        if ($property == PROP_INTEREST_JOIN_COUNT) {
                            $joinCount = 1;
                        } else if ($property == PROP_INTEREST_LIKE_COUNT) {
                            $likeCount = 1;
                        } else if ($property == PROP_INTEREST_RESHARE_COUNT) {
                            $reshareCount = 1;
                        }
                    }
                    $usr->relateTo($tag_en, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, $weight)->setProperty(PROP_INTEREST_JOIN_COUNT, $joinCount)->setProperty(PROP_INTEREST_LIKE_COUNT, $likeCount)->setProperty(PROP_INTEREST_RESHARE_COUNT, $reshareCount)->save();
                }
            } else {
                $query = "START tag=node:" . IND_TIMETY_TAG . "_" . LANG_EN_US . "('" . PROP_TIMETY_TAG_ID . ":" . $tagId . "'), " .
                        " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        " MATCH  user-[r:" . REL_TIMETY_INTERESTS . "]->tag" .
                        " RETURN r";
                $query = new Cypher\Query($client, $query, null);
                $nresult = $query->getResultSet();
                $relation = null;
                foreach ($nresult as $row) {
                    $relation = $row[0];
                    break;
                }
                if (!empty($relation)) {
                    $prop = $relation->getProperty($property);
                    if (!empty($prop)) {
                        $prop = $prop + $type;
                        if ($prop < 0) {
                            $prop = 0;
                        }
                        $relation->setProperty($property, $prop)->save();
                    } else {
                        $value = 0;
                        if ($type == 1) {
                            $value = 1;
                        }
                        $relation->setProperty($property, $value)->save();
                    }
                }
                $query = "START tag=node:" . IND_TIMETY_TAG . "_" . LANG_TR_TR . "('" . PROP_TIMETY_TAG_ID . ":" . $tagId . "'), " .
                        " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        " MATCH  user-[r:" . REL_TIMETY_INTERESTS . "]->tag" .
                        " RETURN r";
                $query = new Cypher\Query($client, $query, null);
                $nresult = $query->getResultSet();
                $relation = null;
                foreach ($nresult as $row) {
                    $relation = $row[0];
                    break;
                }
                if (!empty($relation)) {
                    $prop = $relation->getProperty($property);
                    if (!empty($prop)) {
                        $prop = $prop + $type;
                        if ($prop < 0) {
                            $prop = 0;
                        }
                        $relation->setProperty($property, $prop)->save();
                    } else {
                        $value = 0;
                        if ($type == 1) {
                            $value = 1;
                        }
                        $relation->setProperty($property, $value)->save();
                    }
                }
            }
        }
    }

    public static function checkReshare($userId, $eventId) {
        if (!empty($userId) && !empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  event-[r?:" . REL_EVENTS_RESHARE . "]-user" .
                    " RETURN event,user,r";
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                $result = array();
                $result['rel'] = $row['r'];
                $result['user'] = $row['user'];
                $result['event'] = $row['event'];
                return $result;
            }
        }
        return false;
    }

    public static function checkLike($userId, $eventId) {
        if (!empty($userId) && !empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  event-[r?:" . REL_EVENTS_LIKE . "]-user" .
                    " RETURN event,user,r";
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                $result = array();
                $result['rel'] = $row['r'];
                $result['user'] = $row['user'];
                $result['event'] = $row['event'];
                return $result;
            }
        }
        return false;
    }

    public static function followUser($fromUserId, $toUserId) {
        $res = new Result();
        try {
            if (!SocialUtil::checkFollowStatus($fromUserId, $toUserId)) {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
                $fromUsr = $userIndex->findOne(PROP_USER_ID, $fromUserId);
                $toUsr = $userIndex->findOne(PROP_USER_ID, $toUserId);
                if (!empty($fromUsr) && !empty($toUsr)) {
                    $fromUsr->relateTo($toUsr, REL_FOLLOWS)->save();
                    $res->success = true;
                    NotificationUtils::insertNotification(NOTIFICATION_TYPE_FOLLOWED, $toUserId, $fromUserId, null, null);
                    RedisUtils::addUserFollow($fromUserId, $toUserId, true);
                    RedisUtils::addUserFollower($toUserId, $fromUserId, true);
                    Queue::followUser($fromUserId, $toUserId);
                    UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_STATISTICS, array("userId" => $toUserId, "type" => 2, "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
                    UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_STATISTICS, array("userId" => $fromUserId, "type" => 1, "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
                    $fu = new User();
                    $fu->createFromNeo4j($fromUsr);
                    $tu = new User();
                    $tu->createFromNeo4j($toUsr);

                    $follow_color = "#588cc8";
                    $following_color = "#84C449";
                    $style = $follow_color;
                    $flw_text = LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_FOLLOW");
                    if (RedisUtils::isUserInFollowings($fromUserId, $toUserId)) {
                        $style = $following_color;
                        $flw_text = LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_FOLLOWING");
                    }
                    $name_name = $tu->firstName;
                    if (!empty($tu->business_user)) {
                        $name_name = $tu->getFullName();
                    }

                    $followerNameR = $fu->firstName;
                    $followerSurnameR = $fu->lastName;
                    if (!empty($tu->business_user)) {
                        $followerNameR = $fu->getFullName();
                        $followerSurnameR = "";
                    }

                    $params = array(
                        array('folw_bg_color', $style),
                        array('folw_text', $flw_text),
                        array('name', $name_name),
                        array('followerName', $followerNameR),
                        array('followerSurname', $followerSurnameR),
                        array('followerUsername', $fu->userName),
                        array('bio', $fu->about),
                        array('img', PAGE_GET_IMAGEURL . urlencode($fu->getUserPic()) . "&h=90&w=90"),
                        array('$profileUrl', HOSTNAME . $fu->userName),
                        array('email_address', $tu->email));
                    //TODO
                    MailUtil::sendSESMailFromFile(LanguageUtils::getLocale() . "_followedBy.html", $params, "" . $tu->getFullName() . " <" . $tu->email . ">", LanguageUtils::getText("LANG_MAIL_FOLLOWED_BY_SUBJECT"));
                } else {
                    $res->error = LanguageUtils::getText("LANG_UTILS_NEO4J_SOCIAL_ERROR_USER_NOT_FOUND");
                }
            } else {
                $res->success = true;
            }
        } catch (Exception $e) {
            error_log("Error" . $e->getTraceAsString());
            $res->error = $e->getTraceAsString();
        }
        return $res;
    }

    public static function checkFollowStatus($fromUserId, $toUserId) {
        if (!empty($fromUserId) && !empty($toUserId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START fromUser=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $fromUserId . "'), " .
                    " toUser=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $toUserId . "') " .
                    " MATCH  fromUser-[r:" . REL_FOLLOWS . "]->toUser" .
                    " RETURN r";
            //echo $query;
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                return true;
            }
        }
        return false;
    }

    public static function unfollowUser($fromUserId, $toUserId) {
        $res = new Result();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START fuser=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $fromUserId . "'), " .
                    " tuser=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $toUserId . "') " .
                    "MATCH (fuser) -[r:" . REL_FOLLOWS . "]-> (tuser) " .
                    "DELETE  r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            $res->success = true;
            RedisUtils::addUserFollow($fromUserId, $toUserId, false);
            RedisUtils::addUserFollower($toUserId, $fromUserId, false);
            Queue::unFollowUser($fromUserId, $toUserId);
            UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_STATISTICS, array("userId" => $toUserId, "type" => 2, "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
            UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_STATISTICS, array("userId" => $fromUserId, "type" => 1, "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
        } catch (Exception $e) {
            error_log("Error " . $e->getTraceAsString());
            $res->error = $e->getTraceAsString();
        }
        return $res;
    }

    public static function in_array($array, $id) {
        if (!empty($array) && !empty($id)) {
            foreach ($array as $fr) {
                if (!empty($fr) && !empty($fr->id)) {
                    if ($fr->id == $id) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function getUserSocialFriend($userId) {
        $user = UserUtils::getUserById($userId);
        $friendList = array();
        $friendIdList = array();
        if (!empty($user)) {
            $socialProviders = $user->socialProviders;
            if (!empty($socialProviders)) {
                $provider = new SocialProvider();
                $friendList = array();
                foreach ($socialProviders as $provider) {
                    $friends = array();
                    if ($provider->oauth_provider == FACEBOOK_TEXT) {
                        $facebook = new Facebook(array(
                                    'appId' => FB_APP_ID,
                                    'secret' => FB_APP_SECRET,
                                    'cookie' => true
                                ));

                        $facebook->setAccessToken($provider->oauth_token);
                        $friends_fb = array();
                        $friends_fb = $facebook->api('/me/friends');
                        $friends_fb = $friends_fb['data'];
                        foreach ($friends_fb as $friend) {
                            $id = "";
                            $id = $friend['id'];
                            if ($id != $userId)
                                array_push($friends, $id);
                        }
                    } elseif ($provider->oauth_provider == TWITTER_TEXT) {
                        $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                        $friends_tw = $twitteroauth->get('statuses/followers');
                        if (isset($friends_tw->error)) {
                            $friends_tw = null;
                        } else {
                            foreach ($friends_tw as $friend) {
                                $id = "";
                                if (property_exists($friend, 'id')) {
                                    $id = $friend->id;
                                }
                                if ($id != $userId)
                                    array_push($friends, $id);
                            }
                        }
                    } elseif ($provider->oauth_provider == FOURSQUARE_TEXT) {
                        $foursquare = new FoursquareAPI(FQ_CLIENT_ID, FQ_CLIENT_SECRET);
                        $foursquare->SetAccessToken($provider->oauth_token);
                        $res = $foursquare->GetPrivate("users/self/friends");
                        $details = json_decode($res);
                        $res = $details->response;
                        $friends_fq = $res->friends->items;
                        foreach ($friends_fq as $friend) {
                            $id = "";
                            if (property_exists($friend, 'id')) {
                                $id = $friend->id;
                            }
                            if ($id != $userId)
                                array_push($friends, $id);
                        }
                    } elseif ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                        /* $google = new Google_Client();
                          $google->setApplicationName(GG_APP_NAME);
                          $google->setClientId(GG_CLIENT_ID);
                          $google->setClientSecret(GG_CLIENT_SECRET);
                          $google->setRedirectUri(HOSTNAME . GG_CALLBACK_URL);
                          $google->setDeveloperKey(GG_DEVELOPER_KEY);
                          $plus = new Google_PlusService($google);
                          $google->setAccessToken($provider->oauth_token);
                          $me = $plus->people->get('me');
                          var_dump($me);
                          foreach ($friends_fq as $friend) {
                          $id = "";
                          if (property_exists($friend, 'id')) {
                          $id = $friend->id;
                          }
                          array_push($friends, $id);
                          } */
                    }
                    if (!empty($friends)) {
                        $friends = SocialFriendUtil::getUserSuggestList($user->id, $friends, $provider->oauth_provider);
                        foreach ($friends as $fr) {
                            $key = array_search($fr->id, $friendIdList);
                            if (strlen($key) <= 0 && $fr->id != $userId) {
                                array_push($friendList, $fr);
                                array_push($friendIdList, $fr->id);
                            }
                        }
                    }
                }
            }
        }
        return $friendList;
    }

}

?>
