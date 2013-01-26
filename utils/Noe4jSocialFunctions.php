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
                        " event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
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

    public static function revertReshareEvent($userId, $eventId) {
        $result = new Result();
        $result->success = false;
        $result->error = true;
        if (!empty($userId) && !empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "'), " .
                        " event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                        " MATCH (user) -[r:" . REL_EVENTS_RESHARE . "]- (event) " .
                        " DELETE  r";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
                $result->success = true;
                $result->error = false;
                SocialUtil::incReshareCountAsync($userId, $eventId);
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
                SocialUtil::checkUserInterestTag($userId, $tagId,$property,$type);
            }
        }
    }

    public static function checkUserInterestTag($userId, $tagId,$property,$type) {
        if (!empty($userId) && !empty($tagId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START tag=node:" . IND_OBJECT_INDEX . "('" . PROP_OBJECT_ID . ":" . $tagId . "'), " .
                    " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  user-[r:" . REL_INTERESTS . "]->tag" .
                    " RETURN r";
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            $relation=null;
            foreach ($nresult as $row) {
               $relation=$row[0];
               break;
            }
            if(empty($relation))
            {
                $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
                $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
                $usr=$userIndex->findOne(PROP_USER_ID, $userId);
                $obj=$objectIndex->findOne(PROP_OBJECT_ID, $tagId);
                if(!empty($usr) && !empty($obj))
                {
                    $weight=1;
                    $joinCount=0;
                    $likeCount=0;
                    $reshareCount=0;
                    if($type==1)
                    {
                        if($property==PROP_INTEREST_JOIN_COUNT)
                        {
                            $joinCount=1;
                        }else if($property==PROP_INTEREST_LIKE_COUNT)
                        {
                            $likeCount=1;
                        }else if($property==PROP_INTEREST_RESHARE_COUNT)
                        {
                            $reshareCount=1;
                        }
                    }
                    $usr->relateTo($obj, REL_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, $weight)->setProperty(PROP_INTEREST_JOIN_COUNT,$joinCount)->setProperty(PROP_INTEREST_LIKE_COUNT,$likeCount)->setProperty(PROP_INTEREST_RESHARE_COUNT,$reshareCount)->save();
                }
            }else
            {
                $prop=$relation->getProperty($property);
                if(!empty($prop))
                {
                    $prop=$prop+$type;
                    if($prop<0)
                    {
                        $prop=0;
                    }
                    $relation->setProperty($property,$prop)->save();
                }else
                {
                   $value=0;
                   if($type==1)
                   {
                       $value=1;
                   }
                   $relation->setProperty($property,$value)->save();
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

}

?>
