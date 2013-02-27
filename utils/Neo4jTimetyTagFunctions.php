<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jTimetyTagUtil {

    public static function getLastId() {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $timetyTagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG);
        $timetyTagIndex->save();
        $query = "START tag=node:" . IND_TIMETY_TAG . "('" . PROP_TIMETY_TAG_ID . ":**') RETURN tag.".PROP_TIMETY_TAG_ID.", count(*) ORDER BY tag." . PROP_TIMETY_TAG_ID . " DESC LIMIT 1";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            return $row[0] + 1;
        }
        return 1;
    }

    public static function getTimetyTagById($id) {
        if (empty($id)) {
            return null;
        }
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $timetyTagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG);
        $tag = $timetyTagIndex->findOne(PROP_TIMETY_TAG_ID, $id);
        if (!empty($tag)) {
            $ttag = new TimetyTag();
            $ttag->createNeo4j($tag);
            return $ttag;
        } else {
            return null;
        }
    }

    public static function insertTimetyTag($catId, $tagName) {
        $catId = (int) $catId;
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
        $timetyTagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG);
        $cat = $timetyCategoryIndex->findOne(PROP_TIMETY_CAT_ID, $catId);
        if (!empty($cat)) {
            try {
                $tag = $timetyTagIndex->find(PROP_TIMETY_TAG_NAME, $tagName);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
            if (!empty($tag)) {
                //already exits
                return 1;
            } else {
                $id = Neo4jTimetyTagUtil::getLastId();
                $tagNode = $client->makeNode();
                $tagNode->setProperty(PROP_TIMETY_TAG_ID, $id);
                $tagNode->setProperty(PROP_TIMETY_TAG_NAME, $tagName);
                $tagNode->save();
                $cat->relateTo($tagNode, REL_TIMETY_OBJECTS)->save();
                $timetyTagIndex->add($tagNode, PROP_TIMETY_TAG_ID, $id);
                $timetyTagIndex->add($tagNode, PROP_TIMETY_TAG_NAME, $tagName);
                $timetyTagIndex->save();
                // ok
                return 3;
            }
        }
        return 2;
    }

    public static function updateTimetyTag($tagId, $tagName) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $timetyTagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG);
        try {
            $tag = $timetyTagIndex->findOne(PROP_TIMETY_TAG_ID, $tagId);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
        if (!empty($tag)) {
            $tmp_Id = $tag->getProperty(PROP_TIMETY_TAG_ID);
            $tmp_Name = $tag->getProperty(PROP_TIMETY_TAG_NAME);
            $timetyTagIndex->remove($tag, PROP_TIMETY_TAG_ID, $tmp_Id);
            $timetyTagIndex->remove($tag, PROP_TIMETY_TAG_NAME, $tmp_Name);
            $timetyTagIndex->save();
            $tag->setProperty(PROP_TIMETY_CAT_ID, $tagId);
            $tag->setProperty(PROP_TIMETY_CAT_NAME, $tagName);
            $tag->save();
            $timetyTagIndex->add($tag, PROP_TIMETY_TAG_ID, $tagId);
            $timetyTagIndex->add($tag, PROP_TIMETY_TAG_NAME, $tagName);
            $timetyTagIndex->save();
            // ok
            return 1;
        }
        return 0;
    }

    public static function removeTimetyTag($tagId) {
        if (!empty($tagId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $timetyTagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG);
                $tag = $timetyTagIndex->findOne(PROP_TIMETY_TAG_ID, $tagId);
                if (!empty($tag)) {
                    $query = "START tag=node:" . IND_TIMETY_TAG . "('" . PROP_TIMETY_TAG_ID . ":" . $tagId . "') " .
                            "MATCH  tag-[r]-() " .
                            "DELETE  tag,r";
                    $query = new Cypher\Query($client, $query, null);
                    $query->getResultSet();
                }
            } catch (Exception $e) {
                echo "Error" . $e->getMessage();
            }
        }
    }

    public static function getTimetyTagsFromCat($catId) {
        if (!empty($catId)) {
            $array = array();
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_TIMETY_CATEGORY . "')[[" . PROP_TIMETY_CAT_ID . ":'" . $catId . "']]" .
                    ".out('" . REL_TIMETY_OBJECTS . "').dedup";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $tag = new TimetyTag();
                $tag->createNeo4j($row[0]);
                array_push($array, $tag);
            }
            return $array;
        }
        return null;
    }

}

?>
