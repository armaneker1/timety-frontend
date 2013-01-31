<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Cypher;

class SocialUtil {

    public static function likeEvent($userId, $eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
        $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
        $usr = $userIndex->findOne(PROP_USER_ID, $userId);
        $event = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
        $result = new Result();
        $result->success = false;
        $result->error = true;
        if (!empty($event) && !empty($usr)) {
            try {
                if (!SocialUtil::checkLike($userId, $eventId)) {
                    $usr->relateTo($event, REL_EVENTS_LIKE)->save();
                    $result->success = true;
                    $result->error = false;
                }
                SocialUtil::incLikeCountAsync($userId, $eventId);
            } catch (Exception $e) {
                log("Error" + $e->getMessage());
                $result->error = $e->getMessage();
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
                SocialUtil::decLikeCountAsync($userId, $eventId);
            } catch (Exception $e) {
                log("Error" + $e->getMessage());
                $result->error = $e->getMessage();
            }
        } else {
            $result->success = false;
            $result->error = true;
        }
        return $result;
    }

    public static function reshareEvent($userId, $eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
        $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
        $usr = $userIndex->findOne(PROP_USER_ID, $userId);
        $event = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
        $result = new Result();
        $result->success = false;
        $result->error = true;
        if (!empty($event) && !empty($usr)) {
            try {
                if (!SocialUtil::checkReshare($userId, $eventId)) {
                    $usr->relateTo($event, REL_EVENTS_RESHARE)->save();
                }
                SocialUtil::incReshareCountAsync($userId, $eventId);
                $result->success = true;
                $result->error = false;
            } catch (Exception $e) {
                log("Error" + $e->getMessage());
                $result->error = $e->getMessage();
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
                SocialUtil::decReshareCountAsync($userId, $eventId);
            } catch (Exception $e) {
                log("Error" + $e->getMessage());
                $result->error = $e->getMessage();
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
            $nresult = Neo4jEventUtils::getEventTags($eventId);
            foreach ($nresult as $row) {
                $tagId = $row->getProperty(PROP_OBJECT_ID);
                SocialUtil::checkUserInterestTag($userId, $tagId, $property, $type);
            }
        }
    }

    public static function checkUserInterestTag($userId, $tagId, $property, $type) {
        if (!empty($userId) && !empty($tagId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START tag=node:" . IND_OBJECT_INDEX . "('" . PROP_OBJECT_ID . ":" . $tagId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  user-[r:" . REL_INTERESTS . "]->tag" .
                    " RETURN r";
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            $relation = null;
            foreach ($nresult as $row) {
                $relation = $row[0];
                break;
            }
            if (empty($relation)) {
                $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
                $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
                $usr = $userIndex->findOne(PROP_USER_ID, $userId);
                $obj = $objectIndex->findOne(PROP_OBJECT_ID, $tagId);
                if (!empty($usr) && !empty($obj)) {
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
                    $usr->relateTo($obj, REL_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, $weight)->setProperty(PROP_INTEREST_JOIN_COUNT, $joinCount)->setProperty(PROP_INTEREST_LIKE_COUNT, $likeCount)->setProperty(PROP_INTEREST_RESHARE_COUNT, $reshareCount)->save();
                }
            } else {
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

    public static function checkReshare($userId, $eventId) {
        if (!empty($userId) && !empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  event-[r:" . REL_EVENTS_RESHARE . "]-user" .
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

    public static function checkLike($userId, $eventId) {
        if (!empty($userId) && !empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  event-[r:" . REL_EVENTS_LIKE . "]-user" .
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

    public static function followUser($fromUserId, $toUserId) {
        $result = new Result();
        try {
            if (!SocialUtil::checkFollowStatus($fromUserId, $toUserId)) {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
                $fromUsr = $userIndex->findOne(PROP_USER_ID, $fromUserId);
                $toUsr = $userIndex->findOne(PROP_USER_ID, $toUserId);
                if (!empty($fromUsr) && !empty($toUsr)) {
                    $fromUsr->relateTo($toUsr, REL_FOLLOWS)->save();
                    $result->success = true;
                } else {
                    $result->error = "Userlar bulunamadı";
                }
            } else {
                $result->success = true;
            }
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            $result->error = $e->getMessage();
        }
        return $result;
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
        $result = new Result();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START fuser=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $fromUserId . "') " .
                    "MATCH (fuser) -[r:" . REL_FOLLOWS . "]-> (tuser) " .
                    "WHERE tuser." . PROP_USER_ID . "=" . $toUserId . " " .
                    "DELETE  r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            $result->success = true;
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            $result->error = $e->getMessage();
        }
        return $result;
    }

    public static function getUserFriendRecommendation($userId) {
        $user = UserUtils::getUserById($_SESSION['id']);
        $friendList=array();
        $friendIdList=array();
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
                        foreach ($friends as $fr)
                        {
                            $key = array_search($fr->id, $friendIdList);
                            if (strlen($key) <= 0) {
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
