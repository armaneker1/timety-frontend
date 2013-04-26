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

require_once __DIR__ . '/../../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
$array = Neo4jTimetyCategoryUtil::getTimetyList("");

foreach ($array as $cat) {
    echo "<h2>$cat->name($cat->id)</h2>";
    $cat = Neo4jTimetyCategoryUtil::getTimetyCategoryNodeById($cat->id);
    $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
    $nodeTR_TR = $client->makeNode();
    $nodeTR_TR->setProperty(PROP_TIMETY_LANG_CODE, LANG_TR_TR);
    $nodeTR_TR->save();
    $cat->relateTo($nodeTR_TR, REL_TIMETY_LANG)->save();

    $nodeEN_US = $client->makeNode();
    $nodeEN_US->setProperty(PROP_TIMETY_LANG_CODE, LANG_EN_US);
    $nodeEN_US->save();
    $cat->relateTo($nodeEN_US, REL_TIMETY_LANG)->save();
    //REL_TIMETY_LANG
    echo "Done<br/>";
}
?>
