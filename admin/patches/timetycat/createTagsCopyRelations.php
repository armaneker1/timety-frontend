<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../../../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();
$array = Neo4jTimetyCategoryUtil::getTimetyList("");

$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$timetyTagIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG);
$timetyTagIndexTR_TR = new Index($client, Index::TypeNode, IND_TIMETY_TAG . "_" . LANG_TR_TR);
$timetyTagIndexEN_US = new Index($client, Index::TypeNode, IND_TIMETY_TAG . "_" . LANG_EN_US);

foreach ($array as $tcat) {
    echo "<h2>$tcat->name($tcat->id)</h2>";
    $tags = Neo4jTimetyTagUtil::getTimetyTagsFromCat($tcat->id);
    foreach ($tags as $tag) {
        var_dump($tag->id . " " . $tag->name);
        $nodeTR_TR = $timetyTagIndexTR_TR->findOne(PROP_TIMETY_TAG_ID, $tag->id);
        $nodeEN_US = $timetyTagIndexEN_US->findOne(PROP_TIMETY_TAG_ID, $tag->id);

        //OUT RELATION
        $query = "g.idx('" . IND_TIMETY_TAG . "')[['" . PROP_TIMETY_TAG_ID . "':'" . $tcat->id . "']].outE.dedup";
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $r = $row[0];
            if (!empty($r)) {
                if (!empty($nodeTR_TR)) {
                    $nodeTR_TR->relateTo($r->getEndNode(), $r->getType())->save();
                }
                if (!empty($nodeEN_US)) {
                    $nodeEN_US->relateTo($r->getEndNode(), $r->getType())->save();
                }
            }
        }

        //IN RELATION
        $query = "g.idx('" . IND_TIMETY_TAG . "')[['" . PROP_TIMETY_TAG_ID . "':'" . $tcat->id . "']].inE.dedup";
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $r = $row[0];
            if (!empty($r) && $r->getType() != REL_TIMETY_OBJECTS) {
                if (!empty($nodeTR_TR)) {
                    $r->getEndNode()->relateTo($nodeTR_TR, $r->getType())->save();
                }
                if (!empty($nodeEN_US)) {
                    $r->getEndNode()->relateTo($nodeEN_US, $r->getType())->save();
                }
            }
        }
    }
    echo "Done<br/>";
}
?>
