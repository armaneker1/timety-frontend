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
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
$nf = new Neo4jFuctions();

$list = $nf->getAllEvents();
$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
$groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
$objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
$cat1Index = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL1);
$cat2Index = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);


/*
 * Events
 */
 var_dump("Events");
$query = "START events=node:EVENT_INDEX('id:**') RETURN events";
$query = new Cypher\Query($client, $query, null);
$result = $query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        if (!empty($row) && !empty($row['events'])) {
            $id = $row['events']->getProperty("id");
            var_dump($id);
            if (!empty($id) || $id==0) {
                $evnt = $eventIndex->findOne("id", $id);
                if (!empty($evnt)) {
                    $eventIndex->remove($evnt, "id",$id);
                    $evnt->setProperty(PROP_EVENT_ID,$id)->save();
                    $evnt->removeProperty("id")->save();
                    $eventIndex->add($evnt, PROP_EVENT_ID,$id);
                    $eventIndex->save();
                }
            }
        }
    }
}


/*
 * Users
 */
 var_dump("Users");
$query = "START users=node:USER_INDEX('id:**') RETURN users";
$query = new Cypher\Query($client, $query, null);
$result = $query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        if (!empty($row) && !empty($row['users'])) {
            $id = $row['users']->getProperty("id");
             var_dump($id);
            if (!empty($id)|| $id==0) {
                $usr = $userIndex->findOne("id", $id);
                if (!empty($usr)) {
                    $userIndex->remove($usr, "id",$id);
                    $usr->setProperty(PROP_USER_ID,$id)->save();
                    $usr->removeProperty("id")->save();
                    $userIndex->add($usr, PROP_USER_ID,$id);
                    $userIndex->save();
                }
            }
        }
    }
}


/*
 * Groups
 */
 var_dump("Groups");
$query = "START groups=node:GROUP_INDEX('id:**') RETURN groups";
$query = new Cypher\Query($client, $query, null);
$result = $query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        if (!empty($row) && !empty($row['groups'])) {
            $id = $row['groups']->getProperty("id");
             var_dump($id);
            if (!empty($id)|| $id==0) {
                $grp = $groupIndex->findOne("id", $id);
                if (!empty($usr)) {
                    $groupIndex->remove($grp, "id",$id);
                    $grp->setProperty(PROP_GROUP_ID,$id)->save();
                    $grp->removeProperty("id")->save();
                    $groupIndex->add($grp, PROP_GROUP_ID,$id);
                    $groupIndex->save();
                }
            }
        }
    }
}


/*
 * Objects
 */
 var_dump("Objects");
$query = "START objects=node:OBJECT_INDEX('id:**') RETURN objects";
$query = new Cypher\Query($client, $query, null);
$result = $query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        if (!empty($row) && !empty($row['objects'])) {
            $id = $row['objects']->getProperty("id");
             var_dump($id);
            if (!empty($id)|| $id==0) {
                $obj = $objectIndex->findOne("id", $id);
                if (!empty($obj)) {
                    $objectIndex->remove($obj, "id",$id);
                    $obj->setProperty(PROP_OBJECT_ID,$id)->save();
                    $obj->removeProperty("id")->save();
                    $objectIndex->add($obj, PROP_OBJECT_ID,$id);
                    $objectIndex->save();
                }
            }
        }
    }
}



/*
 * Category1
 */
 var_dump("Category1");
$query = "START cats=node:CATEGORY_LEVEL1('id:**') RETURN cats";
$query = new Cypher\Query($client, $query, null);
$result = $query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        if (!empty($row) && !empty($row['cats'])) {
            $id = $row['cats']->getProperty("id");
             var_dump($id);
            if (!empty($id)|| $id==0) {
                $cat = $cat1Index->findOne("id", $id);
                if (!empty($cat)) {
                    $cat1Index->remove($cat, "id",$id);
                    $cat->setProperty(PROP_CATEGORY_ID,$id)->save();
                    $cat->removeProperty("id")->save();
                    $cat1Index->add($cat, PROP_CATEGORY_ID,$id);
                    $cat1Index->save();
                }
            }
        }
    }
}


/*
 * Category2
 */
 var_dump("Category2");
$query = "START cats=node:CATEGORY_LEVEL2('id:**') RETURN cats";
$query = new Cypher\Query($client, $query, null);
$result = $query->getResultSet();
if (!empty($result)) {
    foreach ($result as $row) {
        if (!empty($row) && !empty($row['cats'])) {
            $id = $row['cats']->getProperty("id");
             var_dump($id);
            if (!empty($id)|| $id==0) {
                $cat = $cat2Index->findOne("id", $id);
                if (!empty($cat)) {
                    $cat2Index->remove($cat, "id",$id);
                    $cat->setProperty(PROP_CATEGORY_ID,$id)->save();
                    $cat->removeProperty("id")->save();
                    $cat2Index->add($cat, PROP_CATEGORY_ID,$id);
                    $cat2Index->save();
                }
            }
        }
    }
}
?>