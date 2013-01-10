<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jUserSettingsUtil {

    public static function getUserSubscribeCategories($userId) {
        if (!empty($userId)) {
            $array = array();
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_SUBSCRIBES . "').dedup";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $cat = new CateforyRef();
                $cat->createNeo4j($row[0]);
                array_push($array, $cat);
            }
            return $array;
        } else {
            return null;
        }
    }

    public static function getUserSubscribeCategory($userId, $categoryId) {
        if (!empty($userId) && !empty($categoryId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_USER_INDEX . "')[[" . PROP_USER_ID . ":'" . $userId . "']]" .
                    ".out('" . REL_SUBSCRIBES . "').dedup.has('" . PROP_CATEGORY_ID . "'," . $categoryId . ")";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $cat = new CateforyRef();
                $cat->createNeo4j($row[0]);
                return $cat;
            }
        }
        return null;
    }

    public static function subscribeUserCategory($userId, $categoryId) {
        if (!empty($userId) && !empty($categoryId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $categoryIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
            $user = $userIndex->findOne(PROP_USER_ID, $userId);
            $cat = $categoryIndex->findOne(PROP_CATEGORY_ID, $categoryId);

            if (!empty($user) && !empty($cat)) {
                $cat_tmp = Neo4jUserSettingsUtil::getUserSubscribeCategory($userId, $categoryId);
                if (empty($cat_tmp) || empty($cat_tmp->id)) {
                    $user->relateTo($cat, REL_SUBSCRIBES)->save();
                }
                return true;
            }
        }
        return false;
    }

    public static function unsubscribeUserCategory($userId, $categoryId) {
        if (!empty($userId) && !empty($categoryId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "'),cat=node:" . IND_CATEGORY_LEVEL2 . "('" . PROP_CATEGORY_ID . ":" . $categoryId . "') " .
                        " MATCH  user-[r]-cat" .
                        " DELETE  r";
                $query = new Cypher\Query($client, $query, null);
                $query->getResultSet();
                return true;
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
                return false;
            }
        }
        return false;
    }

}

?>
