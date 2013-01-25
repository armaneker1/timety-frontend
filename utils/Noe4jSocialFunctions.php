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
                SocialUtil::incLikeCount($userId, $eventId);
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
                SocialUtil::incReshareCount($userId, $eventId);
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

    public static function incReshareCount($userId, $eventId) {
        
    }

    public static function incLikeCount($userId, $eventId) {
        
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
