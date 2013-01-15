<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jTimetyCategoryUtil {

    public static function checkTimetyCategoryExits() {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);

        $root_timety_cat = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_TIMETY_CAT);
        if (empty($root_timety_cat)) {
            $root_timety_cat = $client->makeNode();
            $root_timety_cat->setProperty(PROP_ROOT_ID, PROP_ROOT_TIMETY_CAT)->save();
            $client->getReferenceNode()->relateTo($root_timety_cat, REL_TIMETY_CATEGORY_ROOT)->save();
            $rootIndex->add($root_timety_cat, PROP_ROOT_ID, PROP_ROOT_TIMETY_CAT);
            $rootIndex->save();
        }
    }

    public static function getTimetyList($query_) {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_ROOT_INDEX . "')[[" . PROP_ROOT_ID . ":'" . PROP_ROOT_TIMETY_CAT . "']]" .
                ".out('" . REL_TIMETY_CATEGORY . "').dedup";
        if (!empty($query_) || $query_ == 0) {
            $query = $query . ".filter{it." . PROP_TIMETY_CAT_NAME . ".matches('.*(?i)" . $query_ . ".*')}.dedup";
        }
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $cat = new TimetyCategory();
            $cat->createNeo4j($row[0]);
            array_push($array, $cat);
        }
        return $array;
    }

    public static function insertTimetyCategory($catName) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
        $timetyCategoryRootNode = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_TIMETY_CAT);
        $timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
        try {
            $cat = $timetyCategoryIndex->find(PROP_TIMETY_CAT_NAME, $catName);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
        if (!empty($cat)) {
            //already exits
            return 1;
        } else {
            $id = Neo4jTimetyCategoryUtil::getLastId();
            $catNode = $client->makeNode();
            $catNode->setProperty(PROP_TIMETY_CAT_ID, $id);
            $catNode->setProperty(PROP_TIMETY_CAT_NAME, $catName);
            $catNode->save();
            $timetyCategoryRootNode->relateTo($catNode, REL_TIMETY_CATEGORY)->save();
            $timetyCategoryIndex->add($catNode, PROP_TIMETY_CAT_ID, $id);
            $timetyCategoryIndex->add($catNode, PROP_TIMETY_CAT_NAME, $catName);
            $timetyCategoryIndex->save();
            // ok
            return 3;
        }
        // error
        return 2;
    }

    public static function getLastId() {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_ROOT_INDEX . "')[[" . PROP_ROOT_ID . ":'" . PROP_ROOT_TIMETY_CAT . "']]" .
                ".out('" . REL_TIMETY_CATEGORY . "').sort{it." . PROP_TIMETY_CAT_ID . "}.reverse()._()." . PROP_TIMETY_CAT_ID;
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            return $row[0] + 1;
        }
        return 1;
    }

    public static function removeTimetyCategory($catId) {
        if (!empty($catId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
                $cat = $timetyCategoryIndex->findOne(PROP_TIMETY_CAT_ID, $catId);
                if (!empty($cat)) {
                    $query = "START cat=node:" . IND_TIMETY_CATEGORY . "('" . PROP_TIMETY_CAT_ID . ":" . $catId . "') " .
                            "MATCH  cat-[r]-() " .
                            "DELETE  cat,r";
                    $query = new Cypher\Query($client, $query, null);
                    $query->getResultSet();
                }
            } catch (Exception $e) {
                echo "Error" . $e->getMessage();
            }
        }
    }

}
?>
