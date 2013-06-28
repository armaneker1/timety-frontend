<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

echo "<p><h1>All User Events</h1></p>";
$array = Neo4jUserUtil::getAllUsersNode("");

$i = 1;
foreach ($array as $usr) {
    $id = $usr->getProperty(PROP_USER_ID);
    if (!empty($id)) {
        $i++;
        echo "<p><h2>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " Started</h2></p>";

        echo "<p><h3>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " created </h3></p>";
        $events = Neo4jUserUtil::getUserCreatedEventsNode($usr->getProperty(PROP_USER_ID));
        $j = 0;
        foreach ($events as $evt) {
            echo "<p>$i -- $j Event Id :" . $evt->getProperty(PROP_EVENT_ID) . "</p>";
            $j++;
            Queue::addEvent($evt->getProperty(PROP_EVENT_ID), $id);
        }

        echo "<p><h2>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " Sended</h2></p>";
        echo "<p></p>";
        echo "<p></p>";
        echo "<br/>";
        echo "<br/>";
    }
}


echo "<h1>Events Created</h1>";

$i = 1;
foreach ($array as $usr) {
    $id = $usr->getProperty(PROP_USER_ID);
    if (!empty($id)) {
        $i++;
        echo "<p><h2>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " Started</h2></p>";

        echo "<p><h3>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " joined </h3></p>";
        $events = Neo4jUserUtil::getUserJoinedEventsNode($usr->getProperty(PROP_USER_ID));
        $j = 0;
        foreach ($events as $evt) {
            echo "<p>$i -- $j Event Id :" . $evt->getProperty(PROP_EVENT_ID) . "</p>";
            $j++;
            Queue::joinEvent($evt->getProperty(PROP_EVENT_ID), $id, REDIS_USER_INTERACTION_JOIN);
        }

        echo "<p><h3>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " said maybe </h3></p>";
        $events = Neo4jUserUtil::getUserMaybeEventsNode($usr->getProperty(PROP_USER_ID));
        $j = 0;
        foreach ($events as $evt) {
            echo "<p>$i -- $j Event Id :" . $evt->getProperty(PROP_EVENT_ID) . "</p>";
            $j++;
            Queue::joinEvent($evt->getProperty(PROP_EVENT_ID), $id, REDIS_USER_INTERACTION_MAYBE);
        }


        echo "<p><h3>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " liked </h3></p>";
        $events = Neo4jUserUtil::getUserLikedEventsNode($usr->getProperty(PROP_USER_ID));
        $j = 0;
        foreach ($events as $evt) {
            echo "<p>$i -- $j Event Id :" . $evt->getProperty(PROP_EVENT_ID) . "</p>";
            $j++;
            Queue::likeEvent($evt->getProperty(PROP_EVENT_ID), $id, REDIS_USER_INTERACTION_LIKE);
        }

        echo "<p><h3>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " reshared </h3></p>";
        $events = Neo4jUserUtil::getUserResharedEventsNode($usr->getProperty(PROP_USER_ID));
        $j = 0;
        foreach ($events as $evt) {
            echo "<p>$i -- $j Event Id :" . $evt->getProperty(PROP_EVENT_ID) . "</p>";
            $j++;
            Queue::reshareEvent($evt->getProperty(PROP_EVENT_ID), $id, REDIS_USER_INTERACTION_RESHARE);
        }

        echo "<p><h2>$i User Id :" . $usr->getProperty(PROP_USER_ID) . " Sended</h2></p>";
        echo "<p></p>";
        echo "<p></p>";
        echo "<br/>";
        echo "<br/>";
    }
}
?>
