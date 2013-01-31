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

    public static function getPopularUserList($userId, $limit) {
        if (empty($limit)) {
            $limit = 5;
        }
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*') " .
                " MATCH (user) <-[:" . REL_FOLLOWS . "]- (followers) " .
                " WITH user,count(followers) as numFollowers " .
                " RETURN  user, numFollowers" .
                " ORDER BY numFollowers DESC LIMIT " . $limit;
        //echo $query;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            array_push($array, $row['user']->getProperty(PROP_USER_ID));
        }
        return SocialFriendUtil::getUserSuggestListFromIds($array, $limit);
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

}

?>
