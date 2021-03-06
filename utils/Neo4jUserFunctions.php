<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jUserUtil {

    public static function getUserFollowList($userId) {
        $array = array();
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                    "MATCH (user) -[:" . REL_FOLLOWS . "]-> (follow) " .
                    "RETURN follow, count(*)";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $usr = new User();
                $usr->createFromNeo4j($row['follow']);
                array_push($array, $usr);
            }
        }
        return $array;
    }

    public static function getUserFollowerList($userId) {
        $array = array();
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                    "MATCH (user) <-[:" . REL_FOLLOWS . "]- (follow) " .
                    "RETURN follow, count(*)";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $usr = new User();
                $usr->createFromNeo4j($row['follow']);
                array_push($array, $usr);
            }
        }
        return $array;
    }

    public static function getPopularUserList($userId, $limit, $term = null) {
        if (empty($limit)) {
            $limit = 5;
        }
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*') ";
        if (!empty($userId)) {
            $query = $query . " ,usr=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') ";
        }
        $query = $query . " MATCH (user) <-[:" . REL_FOLLOWS . "]- (followers) ";
        if (!empty($userId)) {
            $query = $query . " WHERE NOT(usr -[:FOLLOWS]-> user) ";
        }
        if (!empty($term)) {
            if (empty($userId)) {
                $query = $query . " WHERE ";
            } else {
                $query = $query . " AND ";
            }
            $query = $query . " ( ( HAS (user." . PROP_USER_FIRSTNAME . ") AND user." . PROP_USER_FIRSTNAME . "=~ /.*(?i)" . $term . ".*/ ) " .
                    " OR ( HAS (user." . PROP_USER_LASTNAME . ") AND  user." . PROP_USER_LASTNAME . "=~ /.*(?i)" . $term . ".*/ ) " .
                    " OR ( HAS (user." . PROP_USER_FIRSTNAME . ") AND HAS (user." . PROP_USER_LASTNAME . ") AND  user." . PROP_USER_FIRSTNAME . "+' '+user." . PROP_USER_LASTNAME . "=~ /.*(?i)" . $term . ".*/ ) )";
        }
        $query = $query .
                " WITH user,count(followers) as numFollowers " .
                " RETURN  user, numFollowers" .
                " ORDER BY numFollowers DESC LIMIT " . $limit;
        //echo $query;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            if ($userId != $row['user']->getProperty(PROP_USER_ID)) {
                $usr = new User();
                $usr->createFromNeo4j($row['user']);
                array_push($array, $usr);
            }
        }
        if (empty($array) && empty($term)) {
            $array = SocialFriendUtil::getUserSuggestListFromIds(null, $limit, $userId);
        }
        return $array;
    }

    public static function getUserFollowingCount($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_FOLLOWS . "').dedup.count()";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
            return 0;
        }
    }

    public static function getUserFollowersCount($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".in('" . REL_FOLLOWS . "').dedup.count()";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
            return 0;
        }
    }

    public static function getUserLikesCount($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_EVENTS_LIKE . "').dedup.count()";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
            return 0;
        }
    }

    public static function getUserResharesCount($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_EVENTS_RESHARE . "').dedup.count()";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
            return 0;
        }
    }

    public static function getUserJoinsCount($userId, $type) {
        if (empty($type)) {
            $type = 0;
        }
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_TYPE . "==" . $type . "}.dedup.count()";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
            return 0;
        }
    }

    public static function getUserCreatedCount($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_CREATE . "==1}.dedup.count()";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
            return 0;
        }
    }

    public static function getAllUsersNode($text = "") {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $text . "*') " .
                " RETURN user, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            array_push($array, $row['user']);
        }
        return $array;
    }

    public static function getUserCreatedEventsNode($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_CREATE . "==1}.inV().dedup";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserJoinedEventsNode($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_TYPE . "==" . TYPE_JOIN_YES . "}.inV().dedup";
            //echo $query;
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserMaybeEventsNode($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_TYPE . "==" . TYPE_JOIN_MAYBE . "}.inV().dedup";
            //echo $query;
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserResharedEventsNode($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_EVENTS_RESHARE . "').dedup";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserLikedEventsNode($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_EVENTS_LIKE . "').dedup";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserCreatedEvents($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_CREATE . "==1}.dedup";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserNodeById($userId) {
        if (!empty($userId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        "RETURN user";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
                foreach ($result as $row) {
                    return $row[0];
                }
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
            }
        }
        return null;
    }

    public static function getUserTimetyTags($userId) {
        $array = array();
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_TIMETY_INTERESTS . "').dedup";
            //echo $query;
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $t = new TimetyTag();
                $t->createNeo4j($row[0]);
                array_push($array, $t);
            }
        }
        return $array;
    }

    public static function removeUserTag($userId, $tagId, $lang = null) {
        $tr = true;
        $en = true;
        if (empty($lang)) {
            
        } else if ($lang == LANG_TR_TR) {
            $en = FALSE;
        } else if ($lang == LANG_EN_US) {
            $tr = FALSE;
        }
        if (!empty($userId) && !empty($tagId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                if ($en) {
                    $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "'), " .
                            " tag=node:" . IND_TIMETY_TAG . "_" . LANG_EN_US . "('" . PROP_TIMETY_TAG_ID . ":" . $tagId . "')" .
                            " MATCH user-[r:" . REL_TIMETY_INTERESTS . "]->tag" .
                            " DELETE r";
                    $query = new Cypher\Query($client, $query, null);
                    $result = $query->getResultSet();
                }
                if ($tr) {
                    $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "'), " .
                            " tag=node:" . IND_TIMETY_TAG . "_" . LANG_TR_TR . "('" . PROP_TIMETY_TAG_ID . ":" . $tagId . "')" .
                            " MATCH user-[r:" . REL_TIMETY_INTERESTS . "]->tag" .
                            " DELETE r";
                    $query = new Cypher\Query($client, $query, null);
                    $result = $query->getResultSet();
                }
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
            }
        }
        return null;
    }

    public static function addUserTag($userId, $tagId, $lang = null) {
        $tr = true;
        $en = true;
        if (empty($lang)) {
            
        } else if ($lang == LANG_TR_TR) {
            $en = FALSE;
        } else if ($lang == LANG_EN_US) {
            $tr = FALSE;
        }
        if (!empty($userId) && !empty($tagId)) {
            Neo4jUserUtil::removeUserTag($userId, $tagId, $lang);
            try {
                $userNode = Neo4jUserUtil::getUserNodeById($userId);
                if ($tr) {
                    $tag = Neo4jTimetyTagUtil::getTimetyTagNodeById($tagId, LANG_TR_TR);
                    if (!empty($userNode) && !empty($tag)) {
                        $userNode->relateTo($tag, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
                    }
                }

                if ($en) {
                    $tag = Neo4jTimetyTagUtil::getTimetyTagNodeById($tagId, LANG_EN_US);
                    if (!empty($userNode) && !empty($tag)) {
                        $userNode->relateTo($tag, REL_TIMETY_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, "10")->save();
                    }
                }
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
            }
        }
        return null;
    }

    public static function removeUserById($userId) {
        if (!empty($userId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        "MATCH  user-[r]-()" .
                        "DELETE  r,user";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
            }
        }
    }

    public static function getUserTimetyTag($userId) {
        if (!empty($userId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        " MATCH  user-[r:" . REL_TIMETY_INTERESTS . "]-(tag)" .
                        " RETURN  tag";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
                $array = array();
                foreach ($result as $row) {
                    $tag = new TimetyTag();
                    $tag->createNeo4j($row[0]);
                    array_push($array, $tag);
                }
                return $array;
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
            }
        }
    }

    public static function updateUserStatistics($userId, $type = 0) {
        if (!empty($userId)) {
            try {
                $user = Neo4jUserUtil::getUserNodeById($userId);
                if ($type == 0) {
                    $following_count = Neo4jUserUtil::getUserFollowingCount($userId);
                    $user->setProperty(PROP_USER_STA_FOLLOWINGS_COUNT, $following_count);
                    $followers_count = Neo4jUserUtil::getUserFollowersCount($userId);
                    $user->setProperty(PROP_USER_STA_FOLLOWERS_COUNT, $followers_count);
                    $likes_count = Neo4jUserUtil::getUserLikesCount($userId);
                    $user->setProperty(PROP_USER_STA_LIKES_COUNT, $likes_count);
                    $reshares_count = Neo4jUserUtil::getUserResharesCount($userId);
                    $user->setProperty(PROP_USER_STA_RESHARES_COUNT, $reshares_count);
                    $joined_count = Neo4jUserUtil::getUserJoinsCount($userId, TYPE_JOIN_YES);
                    //maybe de sayılsın
                    $joined_count = $joined_count + Neo4jUserUtil::getUserJoinsCount($userId, TYPE_JOIN_MAYBE);
                    //maybe de sayılsın
                    $user->setProperty(PROP_USER_STA_JOINED_COUNT, $joined_count);
                    $created_count = Neo4jUserUtil::getUserCreatedCount($userId);
                    $user->setProperty(PROP_USER_STA_CREATED_COUNT, $created_count);
                    $user->save();
                    UserUtils::updateUserStatistic($userId, $following_count, $followers_count, $likes_count, $reshares_count, $joined_count, $created_count);
                    error_log("User Id : " . $userId . " " . $joined_count);
                } else if ($type == 1) {
                    $following_count = Neo4jUserUtil::getUserFollowingCount($userId);
                    $user->setProperty(PROP_USER_STA_FOLLOWINGS_COUNT, $following_count);
                    $user->save();
                    UserUtils::updateUserFollowingStat($userId, $following_count);
                } else if ($type == 2) {
                    $followers_count = Neo4jUserUtil::getUserFollowersCount($userId);
                    $user->setProperty(PROP_USER_STA_FOLLOWERS_COUNT, $followers_count);
                    $user->save();
                    UserUtils::updateUserFollowerStat($userId, $followers_count);
                } else if ($type == 3) {
                    $likes_count = Neo4jUserUtil::getUserLikesCount($userId);
                    $user->setProperty(PROP_USER_STA_LIKES_COUNT, $likes_count);
                    $user->save();
                    UserUtils::updateUserLikeStat($userId, $likes_count);
                } else if ($type == 4) {
                    $reshares_count = Neo4jUserUtil::getUserResharesCount($userId);
                    $user->setProperty(PROP_USER_STA_RESHARES_COUNT, $reshares_count);
                    $user->save();
                    UserUtils::updateUserReshareLikeStat($userId, $reshares_count);
                } else if ($type == 5) {
                    $joined_count = Neo4jUserUtil::getUserJoinsCount($userId, TYPE_JOIN_YES);
                    //maybe de sayılsın
                    $joined_count = $joined_count + Neo4jUserUtil::getUserJoinsCount($userId, TYPE_JOIN_MAYBE);
                    //maybe de sayılsın
                    $user->setProperty(PROP_USER_STA_JOINED_COUNT, $joined_count);
                    $user->save();
                    UserUtils::updateUserJoinLikeStat($userId, $joined_count);
                } else if ($type == 6) {
                    $created_count = Neo4jUserUtil::getUserCreatedCount($userId);
                    $user->setProperty(PROP_USER_STA_CREATED_COUNT, $created_count);
                    $joined_count = Neo4jUserUtil::getUserJoinsCount($userId, TYPE_JOIN_YES);
                    $user->setProperty(PROP_USER_STA_JOINED_COUNT, $joined_count);
                    $user->save();
                    UserUtils::updateUserCreatedLikeStat($userId, $created_count);
                    UserUtils::updateUserJoinLikeStat($userId, $joined_count);
                }
            } catch (Exception $e) {
                error_log("Error" . $e->getTraceAsString());
            }
        }
    }

    public static function getUserLikesEventsId($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_EVENTS_LIKE . "').dedup.event_id";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserResharesEventsId($userId) {
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_EVENTS_RESHARE . "').dedup.event_id";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }

    public static function getUserJoinsEventsId($userId, $type) {
        if (empty($type)) {
            $type = 0;
        }
        if (!empty($userId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".outE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_TYPE . "==" . $type . "}.inV.dedup.event_id";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                array_push($array, $row[0]);
            }
            return $array;
        }
    }
}

?>
