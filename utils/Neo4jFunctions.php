<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Cypher;

error_reporting(-1);
ini_set('display_errors', 1);

function loader($sClass) {
    $sLibPath = __DIR__ . '/../apis/';
    $sClassFile = str_replace('\\', DIRECTORY_SEPARATOR, $sClass) . '.php';
    $sClassPath = $sLibPath . $sClassFile;
    if (file_exists($sClassPath)) {
        require($sClassPath);
    }
}

spl_autoload_register('loader');

/*
 * Neo4j Function Files
 */
require_once __DIR__ . '/Neo4jUserSettings.php';

class Neo4jFuctions {

    function getEventInvitesByUserId($userId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    "MATCH (user) <-[r:" . REL_EVENTS_INVITES . "]- (event) " .
                    "RETURN  event,count(*)";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();

            $array = array();
            foreach ($result as $row) {
                $event = new Event();
                $event->id = $row['event']->getProperty(PROP_EVENT_ID);
                $event->title = $row['event']->getProperty(PROP_EVENT_TITLE);
                array_push($array, $event);
            }
            return $array;
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
        }
    }

    function getGropInvitesByUserId($userId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    "MATCH (user) <-[r:" . REL_INVITES . "]- (group) " .
                    "RETURN  group,count(*)";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();

            $array = array();
            foreach ($result as $row) {
                $group = new Group();
                $group->id = $row['group']->getProperty(PROP_GROUP_ID);
                $group->name = $row['group']->getProperty(PROP_GROUP_NAME);
                array_push($array, $group);
            }
            return $array;
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
        }
    }

    function responseToEventInvites($userId, $eventId, $resp) {
        $this->removeEventInvite($userId, $eventId);
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
        $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
        $usr = $userIndex->findOne(PROP_USER_ID, $userId);
        $event = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
        $result = new Result();
        $result->success = false;
        $result->error = true;

        if (!empty($event) && !empty($usr)) {
            try {
                if ($resp == 1) {
                    if (Neo4jEventUtils::relateUserToEvent($usr, $event, 0, TYPE_JOIN_YES)) {
                        NotificationUtils::insertNotification(NOTIFICATION_TYPE_JOIN, $event->getProperty(PROP_EVENT_CREATOR_ID), $userId, $eventId, null);
                    }
                    SocialUtil::incJoinCountAsync($userId, $eventId);
                    $result->success = true;
                    $result->error = false;
                    Neo4jEventUtils::increaseAttendanceCount($eventId);

                    Queue::socialInteraction($eventId, $userId, REDIS_USER_INTERACTION_JOIN);
                } else if ($resp == 0 || $resp == 5) {
                    Neo4jEventUtils::relateUserToEvent($usr, $event, 0, TYPE_JOIN_NO);
                    if ($resp == 5) {
                        SocialUtil::decJoinCountAsync($userId, $eventId);
                        Neo4jEventUtils::decreaseAttendanceCount($eventId);
                    }
                    $result->success = true;
                    $result->error = false;
                    Queue::socialInteraction($eventId, $userId, REDIS_USER_INTERACTION_DECLINE);
                } else if ($resp == 2) {
                    Neo4jEventUtils::relateUserToEvent($usr, $event, 0, TYPE_JOIN_MAYBE);
                    $result->success = true;
                    $result->error = false;
                    SocialUtil::incJoinCountAsync($userId, $eventId);
                    //NotificationUtils::insertNotification(NOTIFICATION_TYPE_MAYBE, $event->getProperty(PROP_EVENT_CREATOR_ID), $userId, $eventId, null);
                    Queue::socialInteraction($eventId, $userId, "maybe");
                } else if ($resp == 3 || $resp == 4) {
                    Neo4jEventUtils::relateUserToEvent($usr, $event, 0, TYPE_JOIN_IGNORE);
                    if ($resp == 4) {
                        SocialUtil::decJoinCountAsync($userId, $eventId);
                        Neo4jEventUtils::decreaseAttendanceCount($eventId);
                    }
                    $result->success = true;
                    $result->error = false;
                    Queue::socialInteraction($eventId, $userId, REDIS_USER_INTERACTION_IGNORE);
                } else {
                    $result->success = false;
                    $result->error = true;
                }
                UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_STATISTICS, array("userId" => $userId));
            } catch (Exception $e) {
                log("Error" + $e->getMessage());
                $result->error = $e->getMessage();
            }
        } else {
            $result->success = false;
            $result->error = true;
        }
        return $result;
    }

    function responseToGroupInvites($userId, $groupId, $resp) {
        $this->removeGroupInvite($userId, $groupId);
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
        $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
        $usr = $userIndex->findOne(PROP_USER_ID, $userId);
        $grp = $groupIndex->findOne(PROP_GROUP_ID, $groupId);
        $result = new Result();
        try {
            if ($resp == 1) {
                $usr->relateTo($grp, REL_JOINS)->setProperty(PROP_JOIN_CREATE, 0)->save();
                $result->success = true;
            } else if ($resp == 0) {
                $usr->relateTo($grp, REL_REJECTS)->setProperty(PROP_JOIN_CREATE, 0)->save();
                $result->success = true;
            } else {
                $result->success = false;
                $result->error = true;
            }
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
            $result->error = $e->getMessage();
        }
        return $result;
    }

    public static function removeEventInvite($uid, $eventId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                    "MATCH (user) <-[r:" . REL_EVENTS_INVITES . "]- (event) " .
                    "WHERE event." . PROP_EVENT_ID . "=" . $eventId . " OR  event." . PROP_EVENT_ID . "='" . $eventId . "'" .
                    "DELETE  r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
        } catch (Exception $e) {
            error_log("Error" . $e->getMessage());
        }
    }

    public static function sendInivitationToGroup($groupId, $eventId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START group=node:" . IND_GROUP_INDEX . "('" . PROP_GROUP_ID . ":" . $groupId . "'), event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                    "MATCH (group) <-[r:" . REL_JOINS . "]- (user) " .
                    "RELATE (event) -[r2:" . REL_EVENTS_INVITES . "]-> (user) ";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
            print_r($e);
        }
    }

    function makeVisibleToGroup($groupId, $eventId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START group=node:" . IND_GROUP_INDEX . "('" . PROP_GROUP_ID . ":" . $groupId . "'), event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                    "MATCH (group) <-[r:" . REL_JOINS . "]- (user) " .
                    "RELATE (user) -[r2:" . REL_EVENTS_USER_SEES . "]-> (event) ";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
            print_r($e);
        }
    }

    function createGroup($groupName, $userList, $userId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
            $root_grp = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_GROUP);
            if (empty($root_grp)) {
                return false;
            } else {
                $group = $client->makeNode();
                $grId = rand(10000, 99999);
                $group->setProperty(PROP_GROUP_ID, $grId);
                $group->setProperty(PROP_GROUP_NAME, $groupName);
                $group->save();

                $groupIndex->add($group, PROP_GROUP_ID, $grId);
                $groupIndex->add($group, PROP_GROUP_NAME, $groupName);
                $groupIndex->save();

                $root_grp->relateTo($group, REL_GROUPS)->save();

                $usr = $userIndex->findOne(PROP_USER_ID, $userId);

                $usr->relateTo($group, REL_JOINS)->setProperty(PROP_JOIN_CREATE, 1)->save();

                foreach ($userList as $user) {
                    $user = $userIndex->findOne(PROP_USER_ID, $user);
                    $group->relateTo($user, REL_INVITES)->save();
                }
                return true;
            }
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            return false;
        }
    }

    function checkGroupName($groupName, $userId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    "MATCH (user) -[r:" . REL_JOINS . "]-> (group) " .
                    "WHERE group." . PROP_GROUP_NAME . "=/.*(?!)'" . $groupName . ".*/  and r." . PROP_JOIN_CREATE . "=1" .
                    "RETURN  group,count(*)";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();

            foreach ($result as $row) {
                $group = new Group();
                $group->id = $row['group']->getProperty(PROP_GROUP_ID);
                $group->name = $row['group']->getProperty(PROP_GROUP_NAME);
                return $group;
            }
            return null;
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
        }
    }

    function searchGroupByName($userId, $groupName) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                    "MATCH (user) -[:" . REL_JOINS . "]-> (grp) " .
                    "WHERE grp." . PROP_GROUP_NAME . "=~ /.*(?i)" . $groupName . ".*/ " .
                    "RETURN  grp, count(*)";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            $array = array();
            foreach ($result as $row) {
                $group = new Group();
                $group->id = $row['group']->getProperty(PROP_GROUP_ID);
                $group->name = $row['group']->getProperty(PROP_GROUP_NAME);
                array_push($array, $group);
            }
            return $array;
        } catch (Exception $e) {
            log("Error" + $e->getMessage());
        }
    }

    function createUser($userId, $userName, $type = USER_TYPE_NORMAL) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $root_usr = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_USR);
            if (empty($root_usr)) {
                return false;
            } else {
                $usr = $userIndex->findOne(PROP_USER_ID, $userId);
                if (!empty($usr)) {
                    $userIndex->remove($usr, PROP_USER_USERNAME, $usr->getProperty(PROP_USER_USERNAME));
                    $usr->setProperty(PROP_USER_USERNAME, $userName);
                    $usr->setProperty(PROP_USER_TYPE, $type);
                    $usr->save();
                    $userIndex->add($usr, PROP_USER_USERNAME, $userName);

                    $userIndex->save();
                    return true;
                } else {
                    $usr = $client->makeNode();
                    $usr->setProperty(PROP_USER_ID, $userId);
                    $usr->setProperty(PROP_USER_USERNAME, $userName);
                    $usr->setProperty(PROP_USER_CM_INVITED, false);
                    if ($type == USER_TYPE_INVITED) {
                        $type = USER_TYPE_NORMAL;
                        $usr->setProperty(PROP_USER_CM_INVITED, true);
                    }
                    $usr->setProperty(PROP_USER_TYPE, $type);
                    $usr->save();


                    $userIndex->add($usr, PROP_USER_ID, $userId);
                    $userIndex->add($usr, PROP_USER_USERNAME, $userName);

                    $userIndex->save();
                    $root_usr->relateTo($usr, REL_USER)->save();
                    return true;
                }
            }
        } catch (Exception $e) {
            var_dump($e);
            //log("Error",$e->getMessage());
            return false;
        }
    }

    function addUserInfo($userId, $firstName, $lastName, $type = USER_TYPE_NORMAL, $userName = null) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            if (!empty($userIndex)) {
                $usr = $userIndex->findOne(PROP_USER_ID, $userId);
                $usr->setProperty(PROP_USER_LASTNAME, $lastName);
                $usr->setProperty(PROP_USER_FIRSTNAME, $firstName);
                $usr->setProperty(PROP_USER_TYPE, $type);
                if (!empty($userName)) {
                    $usr->setProperty(PROP_USER_USERNAME, $userName);
                }
                $usr->save();
                return true;
            }
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            return false;
        }
    }

    function getTag($tagName) {
        if (!empty($tagName)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
                $tag = $objectIndex->findOne(PROP_OBJECT_NAME, strtolower($tagName));
                return $tag;
            } catch (Exception $e) {
                error_log("Error" . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    function addTag($categoryId, $tagName, $socialType, $props = null) {
        $tag = $this->getTag($tagName);

        if (!empty($tag)) {
            return $tag->getProperty(PROP_OBJECT_ID);
        }
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

            $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
            $catIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
            $cat = null;
            if (empty($categoryId)) {
                $cat = $catIndex->findOne(PROP_CATEGORY_NAME, strtolower(CATEGORY_TAG_CONSTANT));
            } else {
                $cat = $catIndex->findOne(PROP_CATEGORY_ID, $categoryId);
            }
            if (!empty($cat)) {
                $object = $client->makeNode();
                $object->setProperty(PROP_OBJECT_ID, "custom_tag_" . rand(1000, 1000000));
                $object->setProperty(PROP_OBJECT_NAME, $tagName);
                $object->setProperty(PROP_OBJECT_SOCIALTYPE, $socialType);
                try {
                    if (isset($props) && !empty($props) && sizeof($props) > 0) {
                        foreach ($props as $prop) {
                            if (!empty($prop) && sizeof($prop) == 2) {
                                $pr_name = $prop[0];
                                $pr_value = $prop[1];
                                if (!empty($pr_name)) {
                                    $object->setProperty($pr_name, $pr_value);
                                }
                            }
                        }
                    }
                } catch (Exception $exc) {
                    error_log($exc->getTraceAsString());
                }
                $object->save();

                $objectIndex->add($object, PROP_OBJECT_ID, $object->getProperty(PROP_OBJECT_ID));
                $objectIndex->add($object, PROP_OBJECT_NAME, strtolower($tagName));
                $objectIndex->save();
                $cat->relateTo($object, REL_OBJECTS)->save();
                return $object->getProperty(PROP_OBJECT_ID);
            }
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            return null;
        }
    }

    function saveUserInterest($userId, $interestId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

            $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);

            $usr = $userIndex->findOne(PROP_USER_ID, $userId);
            $object = $objectIndex->findOne(PROP_OBJECT_ID, $interestId);

            if (!empty($usr) && !empty($object)) {
                $social = $object->getProperty(PROP_OBJECT_SOCIALTYPE);
                if (empty($social)) {
                    $social = 1;
                } else if ($social == "facebook") {
                    $social = 10;
                } else {
                    $social = 2;
                }
                $usr->relateTo($object, REL_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, $social)->save();
            }
        } catch (Exception $e) {
            log("Error", $e->getMessage());
        }
    }

    function removeInterest($uid, $interestId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                    "MATCH (user) -[r:" . REL_INTERESTS . "]- (object) " .
                    "WHERE object." . PROP_OBJECT_ID . "='" . $interestId . "' " .
                    "DELETE  r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
        } catch (Exception $e) {
            log("Error", $e->getMessage());
        }
    }

    function removeGroupInvite($uid, $groupId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                    "MATCH (user) <-[r:" . REL_INVITES . "]- (group) " .
                    "WHERE group." . PROP_GROUP_ID . "=" . $groupId . " " .
                    "DELETE  r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
        } catch (Exception $e) {
            log("Error", $e->getMessage());
        }
    }

    function searchCategoryList($query) {
        if ($query == "*") {
            $query = "";
        }
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START root=node:" . IND_ROOT_INDEX . "('" . PROP_ROOT_ID . ":" . PROP_ROOT_CAT . "') " .
                "MATCH (root) -[:" . REL_CATEGORY_LEVEL1 . "]-> (object) -[:" . REL_CATEGORY_LEVEL2 . "]-> (category) " .
                "WHERE category." . PROP_CATEGORY_NAME . "=~ /.*(?i)" . $query . ".*/ " .
                "RETURN  category, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();

        $array = array();
        $i = 0;
        //echo "Found ".count($result)." category:<p/>";
        foreach ($result as $row) {
            $cat = new CateforyRef();
            $cat->id = $row['cat']->getProperty(PROP_CATEGORY_ID);
            $cat->category = $row['cat']->getProperty(PROP_CATEGORY_NAME);
            array_push($array, $cat);
        }
        return $array;
    }

    function getInterestedCategoryList($uid, $limit) {
        if (empty($limit)) {
            $limit = 4;
        }
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                " MATCH (user) -[:" . REL_INTERESTS . "]- (object) -[:" . REL_OBJECTS . "]- (cat)" .
                " WHERE cat.name <> 'Tag' AND cat.name <> 'tag'" .
                " RETURN  cat, count(*)" .
                " ORDER BY count(*) DESC";
        //echo $query;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();

        $array = array();
        $i = 0;
        //echo "Found ".count($result)." category:<p/>";
        foreach ($result as $row) {
            if ($i < $limit) {
                $i++;
            } else {
                break;
            }
            $cat = new CateforyRef();
            $cat->id = $row['cat']->getProperty(PROP_CATEGORY_ID);
            $cat->category = $row['cat']->getProperty(PROP_CATEGORY_NAME);
            array_push($array, $cat);
        }

        if (!empty($limit) && $limit > 0 && empty($array) || sizeof($array) < $limit) {
            $array = $this->getUserExtraCategory($uid, $array, $limit - sizeof($array));
        }
        return $array;
    }

    function getUserExtraCategory($uid, $array, $limit) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*') " .
                " MATCH (user) -[:" . REL_INTERESTS . "]- (object) -[:" . REL_OBJECTS . "]- (cat)" .
                " WHERE cat.name <> 'Tag' AND cat.name <> 'tag'" .
                " RETURN  cat, count(*)" .
                " ORDER BY count(*) DESC";
        //echo $query;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();

        $i = 0;
        //echo "Found ".count($result)." category:<p/>";
        foreach ($result as $row) {
            $cat = new CateforyRef();
            $cat->id = $row['cat']->getProperty(PROP_CATEGORY_ID);
            $cat->category = $row['cat']->getProperty(PROP_CATEGORY_NAME);
            if (!in_array($cat, $array)) {
                if ($i < $limit) {
                    $i++;
                } else {
                    break;
                }
                array_push($array, $cat);
            }
        }
        return $array;
    }

    function getUserAllInterests($uid) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                "MATCH (user) -[:" . REL_INTERESTS . "]- (object)" .
                "RETURN object, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();

        echo "Found " . count($result) . " object:<p/>";
        foreach ($result as $row) {
            echo "  " . $row['object']->getProperty(PROP_OBJECT_ID) . " - ";
            echo "  " . $row['object']->getProperty(PROP_OBJECT_NAME) . "<p/>";
        }
    }

    function getUserInterestsByCategory($uid, $categoryId, $count) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                "MATCH (user) -[:" . REL_INTERESTS . "]- (object) -[:" . REL_OBJECTS . "]- (cat)" .
                "WHERE cat." . PROP_CATEGORY_ID . "=" . $categoryId .
                "RETURN object, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();
        foreach ($result as $row) {
            $int = new Interest();
            $int->id = $row['object']->getProperty(PROP_OBJECT_ID);
            $int->name = $row['object']->getProperty(PROP_OBJECT_NAME);
            $int->socialType = $row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
            $int->categoryRefId = $categoryId;
            array_push($array, $int);
        }
        if (!empty($count) && $count > 0 && empty($array) || sizeof($array) < $count) {
            $array = $this->getUserExtraInterestsByCategory($uid, $array, $categoryId, $count - sizeof($array));
        }
        return $array;
    }

    function getUserOtherInterestsByCategory($uid, $categoryId, $count) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START cat=node:" . IND_CATEGORY_LEVEL2 . "('" . PROP_CATEGORY_ID . ":" . $categoryId . "') " .
                "MATCH (cat) -[:" . REL_OBJECTS . "]- (object)" .
                "RETURN object, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $i = 0;
        $array = array();
        $likes = $this->getUserInterestsByCategory($uid, $categoryId, null);
        foreach ($result as $row) {
            $int = new Interest();
            $int->id = $row['object']->getProperty(PROP_OBJECT_ID);
            $int->name = $row['object']->getProperty(PROP_OBJECT_NAME);
            $int->socialType = $row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
            $int->categoryRefId = $categoryId;
            if (!in_array($int, $likes)) {
                if ($i < $count) {
                    $i++;
                } else {
                    break;
                }
                array_push($array, $int);
            }
        }
        if (empty($array) || sizeof($array) < $count) {
            $size = $count - sizeof($array);
            if ($size > sizeof($likes)) {
                $size = sizeof($likes);
            }
            for ($i = 0; $i < $size; $i++) {
                array_push($array, $likes[$i]);
            }
        }
        return $array;
    }

    function getUserExtraInterestsByCategory($uid, $array, $categoryId, $limit) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START cat=node:" . IND_CATEGORY_LEVEL2 . "('" . PROP_CATEGORY_ID . ":" . $categoryId . "') " .
                "MATCH (cat) -[:" . REL_OBJECTS . "]- (object)" .
                "RETURN object, count(*)";
        $query = new Cypher\Query($client, $query, 1);
        $result = $query->getResultSet();
        $i = 0;
        foreach ($result as $row) {
            $int = new Interest();
            $int->id = $row['object']->getProperty(PROP_OBJECT_ID);
            $int->name = $row['object']->getProperty(PROP_OBJECT_NAME);
            $int->socialType = $row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
            $int->categoryRefId = $categoryId;
            if (!in_array($int, $array)) {
                if ($i < $limit) {
                    $i++;
                } else {
                    break;
                }
                array_push($array, $int);
            }
        }
        return $array;
    }

    function getUserInterestsIdsByCategory($uid, $categoryId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "') " .
                "MATCH (user) -[:" . REL_INTERESTS . "]- (object) -[:" . REL_OBJECTS . "]- (cat)" .
                "WHERE cat." . PROP_CATEGORY_ID . "=" . $categoryId .
                "RETURN object, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();
        foreach ($result as $row) {
            array_push($array, $row['object']->getProperty(PROP_OBJECT_ID));
        }
        return $array;
    }

    function searchInterestsByCategory($categoryId, $query) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

        $query = "START object=node:" . IND_OBJECT_INDEX . "('" . PROP_OBJECT_NAME . ":*" . strtolower($query) . "*') " .
                "MATCH (object) -[:" . REL_OBJECTS . "]- (cat) " .
                "WHERE cat." . PROP_CATEGORY_ID . "=" . $categoryId . " " .
                "RETURN object, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();

        //echo "Found ".count($result)." object:<p/>";
        foreach ($result as $row) {
            //echo "  ".$row['object']->getProperty('id')." - ";
            //echo "  ".$row['object']->getProperty('name')."<p/>";
            $int = new Interest();
            $int->id = $row['object']->getProperty(PROP_OBJECT_ID);
            $int->name = $row['object']->getProperty(PROP_OBJECT_NAME);
            $int->socialType = $row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
            $int->categoryRefId = $categoryId;
            array_push($array, $int);
        }
        return $array;
    }

    function searchInterests($query) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

        $query = "START object=node:" . IND_OBJECT_INDEX . "('" . PROP_OBJECT_NAME . ":*') " .
                " WHERE object.name=~/.*(?i)" . strtolower($query) . ".*/ " .
                " RETURN object, count(*)";

        //echo $query;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();
        foreach ($result as $row) {
            $int = new Interest();
            $int->id = $row['object']->getProperty(PROP_OBJECT_ID);
            $int->name = $row['object']->getProperty(PROP_OBJECT_NAME);
            $int->socialType = $row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
            array_push($array, $int);
        }
        return $array;
    }

    // deprecated
    function getUserFollowList($userId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user) -[:" . REL_FOLLOWS . "]-> (follow) " .
                "RETURN follow, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();
        foreach ($result as $row) {
            $int = $row['follow']->getProperty(PROP_USER_ID);
            array_push($array, $int);
        }
        return $array;
    }

    function getFriendList($userId, $query, $followers) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $rel = "-[:" . REL_FOLLOWS . "]->";
        if ($followers == 1 || $followers == "1") {
            $rel = "<-[:" . REL_FOLLOWS . "]-";
        }
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                " MATCH (user) " . $rel . " (follow) " .
                " WHERE ( HAS (follow." . PROP_USER_FIRSTNAME . ") AND follow." . PROP_USER_FIRSTNAME . "=~ /.*(?i)" . $query . ".*/ ) " .
                " OR ( HAS (follow." . PROP_USER_LASTNAME . ") AND  follow." . PROP_USER_LASTNAME . "=~ /.*(?i)" . $query . ".*/ ) " .
                " OR ( HAS (follow." . PROP_USER_FIRSTNAME . ") AND HAS (follow." . PROP_USER_LASTNAME . ") AND  follow." . PROP_USER_FIRSTNAME . "+' '+follow." . PROP_USER_LASTNAME . "=~ /.*(?i)" . $query . ".*/ ) " .
                " RETURN follow, count(*)";
        //echo $query."<p/>";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();
        foreach ($result as $row) {
            $uid = $row['follow']->getProperty(PROP_USER_ID);
            $userFunction = new UserUtils();
            $user = $userFunction->getUserById($uid);
            array_push($array, $user);
        }
        return $array;
    }

    function followUser($fromUserId, $toUserId) {
        $result = new Result();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $fromUsr = $userIndex->findOne(PROP_USER_ID, $fromUserId);
            $toUsr = $userIndex->findOne(PROP_USER_ID, $toUserId);
            if (!empty($fromUsr) && !empty($toUsr)) {
                $fromUsr->relateTo($toUsr, REL_FOLLOWS)->save();
                $result->success = true;
            } else {
                $result->error = "Userlar bulunamadı";
            }
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            $result->error = $e->getMessage();
        }
        return $result;
    }

    function unfollowUser($fromUserId, $toUserId) {
        $result = new Result();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START fuser=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $fromUserId . "') " .
                    "MATCH (fuser) -[r:" . REL_FOLLOWS . "]-> (tuser) " .
                    "WHERE tuser." . PROP_USER_ID . "=" . $toUserId . " " .
                    "DELETE  r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            $result->success = true;
        } catch (Exception $e) {
            log("Error", $e->getMessage());
            $result->error = $e->getMessage();
        }
        return $result;
    }

    /*
     * Create Event
     */

    public static function getCategoryListByIdList($list) {
        if (!empty($list)) {
            $cats = explode(",", $list);
            if (is_array($cats) && sizeof($cats) > 0) {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $categoryIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
                $result = array();
                foreach ($cats as $cat) {
                    if (!empty($cat)) {
                        $catObj = $categoryIndex->findOne(PROP_CATEGORY_ID, $cat);
                        if (!empty($catObj)) {
                            $obj = array('id' => $catObj->getProperty(PROP_CATEGORY_ID), 'label' => $catObj->getProperty(PROP_CATEGORY_NAME));
                            array_push($result, $obj);
                        } else {
                            $cats_ = explode(";", $cat);
                            $obj = array('id' => $cat, 'label' => $cats_[1]);
                            array_push($result, $obj);
                        }
                    }
                }
                $json_response = json_encode($result);
                return $json_response;
            }
        }
        return "[]";
    }

    public static function getUserGroupListByIdList($list) {
        if (!empty($list)) {
            $attendances = explode(",", $list);
            if (is_array($attendances) && sizeof($attendances) > 0) {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
                $groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
                $result = array();
                foreach ($attendances as $att) {
                    if (!empty($att)) {
                        $att_ = explode(";", $att);
                        if (sizeof($att_) == 2) {
                            $obj = array('id' => $att, 'label' => $att_[1]);
                            array_push($result, $obj);
                        } else {
                            $parts = explode('_', $att);
                            $type = $parts[0];
                            $id = $parts[1];
                            if ($type == 'u') {
                                $usr = $userIndex->findOne(PROP_USER_ID, $id);
                                if (!empty($usr)) {
                                    $obj = array('id' => $att, 'label' => ($usr->getProperty(PROP_USER_FIRSTNAME) . " " . $usr->getProperty(PROP_USER_LASTNAME)));
                                    array_push($result, $obj);
                                }
                            } else if ($type == 'g') {
                                $grp = $groupIndex->findOne(PROP_GROUP_ID, $id);
                                if (!empty($grp)) {
                                    $obj = array('id' => $att, 'label' => $grp->getProperty(PROP_GROUP_NAME));
                                    array_push($result, $obj);
                                }
                            }
                        }
                    }
                }
                $json_response = json_encode($result);
                return $json_response;
            }
        }
        return "[]";
    }

    public static function getTagListListByIdList($list) {
        if (!empty($list)) {
            $tags = explode(",", $list);
            if (is_array($tags) && sizeof($tags) > 0) {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
                $result = array();
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $tagObj = null;
                        try {
                            $tagObj = $objectIndex->findOne(PROP_OBJECT_ID, $tag);
                        } catch (Exception $exc) {
                            $tagObj = null;
                        }
                        if (!empty($tagObj)) {
                            $obj = array('id' => $tagObj->getProperty(PROP_OBJECT_ID), 'label' => $tagObj->getProperty(PROP_OBJECT_NAME));
                            array_push($result, $obj);
                        } else {
                            $tags_ = explode(";", $tag);
                            $obj = array('id' => $tag, 'label' => $tags_[1]);
                            array_push($result, $obj);
                        }
                    }
                }
                $json_response = json_encode($result);
                return $json_response;
            }
        }
        return "[]";
    }

    /*
     * Home Page
     */

    /*
     * $userId= user id that logged in -1 default guest
     * list events after given date dafault current date
     * $type = events type 1=Popular,2=Mytimete,3=following,4=an other user's public events default 1
     * 5=i created
     * 6=i liked
     * 7=i reshared
     * 8=i joined
     * 9= categories
     * $query search paramaters deeafult "" all
     * $pageNumber deafult 0
     * $pageItemCount default 15
     */

    public static function getEvents($userId = -1, $pageNumber = 0, $pageItemCount = 15, $date = "0000-00-00 00:00", $query = "", $type = 1, $all = 1, $categoryId = -1) {
        /*
          $teg="<p/>getEvents-   ";
          echo  $teg."Started<p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        $array = array();
        if ($userId == -1) {
            $userId = "*";
            $type = 1;
        }

        if (empty($query)) {
            $query = "";
        }

        if ($type == 9 && $categoryId < 0) {
            $type = 1;
        }

        /*
          echo  $teg."Date calculating <p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        if (empty($date) || substr($date, 0, 1) == "0") {
            $date = strtotime("now");
        } else {
            $datestr = $date . ":00";
            $datestr = date_parse_from_format(DATETIME_DB_FORMAT, $datestr);
            if (checkdate($datestr['month'], $datestr['day'], $datestr['year'])) {
                $result = $datestr['year'] . "-";
                if (strlen($datestr['month']) == 1) {
                    $result = $result . "0" . $datestr['month'] . "-";
                } else {
                    $result = $result . $datestr['month'] . "-";
                }
                if (strlen($datestr['day']) == 1) {
                    $result = $result . "0" . $datestr['day'];
                } else {
                    $result = $result . $datestr['day'];
                }

                $result = $result . " ";
                if (strlen($datestr['hour']) == 1) {
                    $result = $result . "0" . $datestr['hour'];
                } else {
                    $result = $result . $datestr['hour'];
                }
                $result = $result . ":";
                if (strlen($datestr['minute']) == 1) {
                    $result = $result . "0" . $datestr['minute'];
                } else {
                    $result = $result . $datestr['minute'];
                }
                $result = $result . ":00";
                $date = $result;
            } else {
                $date = date(DATE_FORMAT);
                $date = $date . " 00:00:00";
            }
            $date = strtotime($date);
        }
        /*
          echo  $teg."Date calculated<p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        $eventIds = "";
        if ($type == 4) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query_ = $query;
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                    "MATCH (user)-[r:" . REL_EVENTS_JOINS . "]->(event)  " .
                    "WHERE (HAS (r." . PROP_JOIN_TYPE . ") AND (r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_YES . " OR r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_MAYBE . ")) AND (event." . PROP_EVENT_PRIVACY . "='true') AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") ";
            if (!empty($query_)) {
                $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                        "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
            }
            $query = $query . "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $evt = new Event();
                $evt->createNeo4j($row['event'], TRUE, $userId);
                $eventIds = $eventIds . $evt->id . ",";
                array_push($array, $evt);
            }
        } else if ($type == 3) {
            /* $resultArray = Neo4jRecommendationUtils::getFollowingFriendsEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
              $array = $resultArray[0];
              $eventIds = $resultArray[1]; */
            return RedisUtils::getFollowingEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
        } else if ($type == 5) {
            return RedisUtils::getCreatedEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
        } else if ($type == 6) {
            return RedisUtils::getLikedEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
        } else if ($type == 7) {
            return RedisUtils::getResahredEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
        } else if ($type == 8) {
            return RedisUtils::getJoinedEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
        } else if ($type == 2) {
            return RedisUtils::getOwnerEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
            /* $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
              $query_ = $query;
              $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
              "MATCH (user)-[r:" . REL_EVENTS_JOINS . "]->(event)  " .
              "WHERE (HAS (r." . PROP_JOIN_TYPE . ") AND (r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_YES . " OR r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_MAYBE . ")) AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") ";
              if (!empty($query_)) {
              $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
              "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
              }
              $query = $query . "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
              $query = new Cypher\Query($client, $query, null);
              $result = $query->getResultSet();
              foreach ($result as $row) {
              $evt = new Event();
              $evt->createNeo4j($row['event'], TRUE, $userId);
              $eventIds = $eventIds . $evt->id . ",";
              array_push($array, $evt);
              } */
        } else if ($type == 9) {
            return RedisUtils::getCategoryEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all,$categoryId);
        } else {
            /*
              echo  $teg."getAllOtherEvents start<p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */
            //$array1 = Neo4jRecommendationUtils::getAllOtherEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
            $recommended = RedisUtils::getUpcomingEventsForUser($userId, $pageNumber, $pageItemCount, $date, $query, $all);
            $check = false;
            if ($pageNumber == 0 || $pageNumber == "0") {
                if ((empty($recommended) || strlen($recommended) < 3)) {
                    $check = true;
                    $_SESSION["recommendation_null"] = "TRUE";
                } else {
                    $_SESSION["recommendation_null"] = "FALSE";
                }
            } else {
                if (isset($_SESSION["recommendation_null"]) && $_SESSION["recommendation_null"] == "TRUE") {
                    $check = true;
                }
            }
            if ($check) {
                return RedisUtils::getUpcomingEvents($userId, $pageNumber, $pageItemCount, $date, $query, $all);
            } else {
                return $recommended;
            }

            /*
              echo  $teg."getAllOtherEvents end size : ".  sizeof($array1)."<p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */

            /*
              echo  $teg."getPopularEventsByLike start<p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */
            //$array2 = null; //Neo4jRecommendationUtils::getPopularEventsByLike($userId, $pageNumber, $count, $date, $query);
            /*
              echo  $teg."getPopularEventsByLike end size : ".  sizeof($array2)."<p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */

            /*
              echo  $teg."merge arrays start<p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */
            /* $dublicateKeys = array();
              if (!empty($array1)) {
              if (!empty($array2)) {
              foreach ($array2 as $evt) {
              if (!empty($evt) && !empty($evt->id) && !in_array($evt->id, $dublicateKeys)) {
              $evt->title = $evt->title . "(*)";
              array_push($array, $evt);
              array_push($dublicateKeys, $evt->id);
              }
              }
              foreach ($array1 as $evt) {
              if (!empty($evt) && !empty($evt->id) && !in_array($evt->id, $dublicateKeys)) {
              array_push($array, $evt);
              array_push($dublicateKeys, $evt->id);
              }
              }
              } else {
              $array = $array1;
              }
              } else if (!empty($array2)) {
              foreach ($array2 as $evt) {
              if (!empty($evt) && !empty($evt->id) && !in_array($evt->id, $dublicateKeys)) {
              $evt->title = $evt->title . "(*)";
              array_push($array, $evt);
              array_push($dublicateKeys, $evt->id);
              }
              }
              } */
            /*
              echo  $teg."merge arrays end <p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */

            /* if ($type == 1 && $pageNumber == 0 && false) {
              $evtAd = new Event();
              $evtAd->ad = true;
              $evtAd->id = -1;
              $evtAd->url = "http://www.thehobbit.com/";
              $evtAd->img = "/images/ads.jpeg";
              $evtAd->imgWidth = 186;
              $evtAd->imgHeight = 275;
              $evtAd->people = 2;
              $evtAd->comment = 0;
              $evtAd->time = "10d";
              array_unshift($tmparray, $evtAd);
              } */

            /*
              echo  $teg."sort arrays start <p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */
            //sort by date
            /* $dublicateKeys = array();
              $low = new Event();
              $evnt = new Event();
              if (!empty($array)) {
              $low = $array[0];
              $low_indx = 0;
              array_push($dublicateKeys, $low->id);
              $eventIds = $eventIds . $low->id . ",";
              for ($i = 1; $i < sizeof($array); $i++) {
              $evnt = $array[$i];
              if (!in_array($evnt->id, $dublicateKeys)) {
              array_push($dublicateKeys, $evnt->id);
              $eventIds = $eventIds . $evnt->id . ",";
              }
              if ($low->startDateTimeLong > $evnt->startDateTimeLong) {
              $array[$i] = $array[$low_indx];
              $array[$low_indx] = $evnt;
              $low = $evnt;
              $low_indx = $i;
              }
              }
              } */
            /*
              echo  $teg."sort arrays end <p/>";
              echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
             */
        }

        if (!empty($eventIds)) {
            $eventIds = substr($eventIds, 0, strlen($eventIds) - 1);
        }

        /*
          echo  $teg."get Event Images from mysql start <p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        $images = ImageUtil::getAllHeaderImageList($eventIds);
        $tmparray = array();
        $img = new Image();
        if (!empty($images)) {
            foreach ($array as $evt) {
                foreach ($images as $img) {
                    if ($evt->id + "" == $img->eventId + "") {
                        if (empty($evt->images)) {
                            $evt->images = array();
                        }
                        array_push($evt->images, $img);
                        $evt->headerImage = $img;
                        break;
                    }
                }
                array_push($tmparray, $evt);
            }
        }
        /*
          echo  $teg."get Event Images from mysql end <p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        return json_encode($tmparray);
    }

    public static function moveUser($fromUserId, $toUserId, User $tmpuser) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
        $user = $userIndex->findOne(PROP_USER_ID, $toUserId);

        $userIndex->remove($user, PROP_USER_USERNAME, $user->getProperty(PROP_USER_USERNAME));


        $user->setProperty(PROP_USER_CM_INVITED, 2);
        $user->setProperty(PROP_USER_FIRSTNAME, $tmpuser->firstName);
        $user->setProperty(PROP_USER_LASTNAME, $tmpuser->lastName);
        $user->setProperty(PROP_USER_USERNAME, $tmpuser->userName);
        $user->save();
        $userIndex->add($user, PROP_USER_USERNAME, $tmpuser->userName);
        $userIndex->save();

        Neo4jUserUtil::removeUserById($fromUserId);
    }

}
