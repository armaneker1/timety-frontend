<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jRecommendationUtils {

    public static function getAllOtherEvents($userId = -1, $pageNumber = 0, $pageItemCount = 15, $date = null, $query_ = "", $all = 1) {
        if ($userId == -1) {
            $userId = "*";
        }
        $array = array();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "";
        if ($all == 0) {
            //subscribed catagory
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_SUBSCRIBES . "').dedup.out('" . REL_EVENTS . "').dedup.has('" . PROP_EVENT_PRIVACY . "','true')";
        } else {
            // all
            $query = "g.idx('" . IND_ROOT_INDEX . "')[[" . PROP_ROOT_ID . ":'" . PROP_ROOT_EVENT . "']]" .
                    ".out('" . REL_EVENT . "').dedup.has('" . PROP_EVENT_PRIVACY . "','true')";
        }
        if (!empty($query_)) {
            $query = $query . ".filter{it.title.matches('.*(?i)" . $query_ . ".*')} ";
        }
        $query = $query . ".filter{it." . PROP_EVENT_START_DATE . ">=" . $date . "}" .
                ".sort{it." . PROP_EVENT_START_DATE . "}._()[" . $pgStart . ".." . $pgEnd . "]";
        //filter{it.inE('" . REL_EVENTS_JOINS . "').filter{it.".PROP_JOIN_TYPE."==".TYPE_JOIN_YES." ||  it.".PROP_JOIN_TYPE."==".TYPE_JOIN_MAYBE."}.inV.dedup." . PROP_USER_ID . "!='" . $userId . "'}
        //echo "Other<p/> ".$query."<p/>";
        $queryRes = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $queryRes->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row[0], TRUE, $userId);
            array_push($array, $evt);
        }
        return $array;
    }

    public static function getPopularEventsByLike($userId, $pageNumber, $pageItemCount, $date, $query_) {
        $array = array();
        if (!empty($userId)) {
            $pgStart = $pageNumber * $pageItemCount;
            $pgEnd = $pgStart + $pageItemCount - 1;
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_INTERESTS . "').dedup.out('" . REL_TAGS . "').dedup.has('" . PROP_EVENT_PRIVACY . "','true')";
            if (!empty($query_)) {
                $query = $query . ".filter{it.title.matches('.*(?i)" . $query_ . ".*')} ";
            }
            $query = $query . ".filter{it." . PROP_EVENT_START_DATE . ">=" . $date . "}" .
                    ".sort{it." . PROP_EVENT_START_DATE . "}._()[" . $pgStart . ".." . $pgEnd . "]";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $evt = new Event();
                $evt->createNeo4j($row[0], TRUE, $userId);
                array_push($array, $evt);
            }
        }
        return $array;
    }

    public static function getFollowingFriendsEvents($userId = -1, $pageNumber = 0, $pageItemCount = 15, $date = "0000-00-00 00:00", $query_ = "", $all = 1) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $eventIds = "";
        $array = array();
        //default all following 
        $relationType = REL_FOLLOWS;
        if ($all == 0) {
            // just subscribes
            $relationType = REL_USER_SUBSCRIBES;
        }
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user)-[:" . $relationType . "]->(friend)-[r:" . REL_EVENTS_JOINS . "]->(event)  " .
                "WHERE (HAS (r." . PROP_JOIN_TYPE . ") AND (r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_YES . " OR r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_MAYBE . ")) AND (event." . PROP_EVENT_PRIVACY . "='true') AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") ";
        if (!empty($query_)) {
            $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                    "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
        }
        $query = $query . "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        //echo $query."<p/>";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        //echo sizeof($result)."<p/>";
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event'], TRUE, $userId);
            $eventIds = $eventIds . $evt->id . ",";
            array_push($array, $evt);
        }
        $tmpArray = array();
        array_push($tmpArray, $array);
        array_push($tmpArray, $eventIds);
        return $tmpArray;
    }

}

?>
