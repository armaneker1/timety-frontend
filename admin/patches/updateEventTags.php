<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$events = array();
$tagsss = array();
$tmp=array();

/*
 * Add events
 */


/*
 * Add events
 */
$i = 0;
if (!empty($events) && sizeof($events) > 0) {
    foreach ($events as $evtId) {
        if (!empty($evtId)) {
            $event = Neo4jEventUtils::getEventNode($evtId);
            if (!empty($event)) {
                $id = $event->getProperty(PROP_EVENT_ID);
                if (!empty($id)) {
                    $tags = $tagsss[$i];
                    if (!empty($tags) && sizeof($tags) > 0) {
                        foreach ($tags as $tagId) {
                            if (!empty($tagId)) {
                                $tag_tr = Neo4jTimetyTagUtil::getTimetyTagNodeById($tagId,LANG_TR_TR);
                                $tag_en =Neo4jTimetyTagUtil::getTimetyTagNodeById($tagId,LANG_EN_US);
                                if (!empty($tag_en)) {
                                    $tId = $tag_en->getProperty(PROP_TIMETY_TAG_ID);
                                    if (!empty($tId)) {
                                        $tag_en->relateTo($event, REL_TAGS)->save();
                                    }
                                }
                                if (!empty($tag_tr)) {
                                    $tId = $tag_tr->getProperty(PROP_TIMETY_TAG_ID);
                                    if (!empty($tId)) {
                                        $tag_tr->relateTo($event, REL_TAGS)->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $i++;
        }
    }
}
?>
