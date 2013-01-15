<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jRecommendationUtils {

    public static function getAllOtherEvents($userId = -1, $pageNumber = 0, $pageItemCount = 15, $date = "0000-00-00 00:00", $query_ = "") {
         if ($userId == -1) {
            $userId = "*";
        }
        $array = array();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                ".out('" . REL_SUBSCRIBES . "').dedup.out('" . REL_EVENTS . "').dedup.has('" . PROP_EVENT_PRIVACY . "','true')";
        if (!empty($query_) || $query_ == 0) {
            $query = $query . ".filter{it.title.matches('.*(?i)" . $query_ . ".*')} ";
        }
        $query = $query . ".filter{it." . PROP_EVENT_START_DATE . ">=" . $date . "}.filter{it.inE('" . REL_EVENTS_JOINS . "').inV.dedup." . PROP_USER_ID . "!='" . $userId . "'}" .
                ".sort{it." . PROP_EVENT_START_DATE . "}._()[" . $pgStart . ".." . $pgEnd . "]";
        //echo $query;
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row[0]);
            array_push($array, $evt);
        }
        return $array;
    }

}

?>
