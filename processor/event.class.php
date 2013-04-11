<?php

require_once __DIR__ . '/../utils/Functions.php';

class EventProcessor {

    public $userID;
    public $eventID;
    public $type;
    public $time;
    public $followID;

    public function addEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > addEvent > start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                $event->getHeaderImage();
                $event->images = array();
                $event->getAttachLink();
                $event->getTags();
                $event->getLocCity();
                $event->getWorldWide();
                $event->attendancecount = Neo4jEventUtils::getEventAttendanceCount($event->id);
                $event->commentCount = CommentUtil::getCommentListSizeByEvent($event->id, null);
            } catch (Exception $exc) {
                $log->logError("event > addEvent Error" . $exc->getTraceAsString());
            }
            /*
             * my timety
             */
            if (!empty($this->userID)) {
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                $this->addUserEventLog(null, $event);

                RedisUtils::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
            }
            /*
             * my timety
             */
            $userRelationEmpty = new stdClass();
            $userRelationEmpty->joinType = 0;
            $userRelationEmpty->like = false;
            $userRelationEmpty->reshare = false;

            /*
             * upcoming event list
             */
            if ($event->privacy . "" == "true") {
                $event->userRelation = $userRelationEmpty;
                $event->userEventLog = array();
                RedisUtils::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
            }
            /*
             * upcoming event list
             */


            /*
             * followers list
             */
            if (!empty($this->userID)) {
                $followers = Neo4jUserUtil::getUserFollowerList($this->userID);
                if (!empty($followers)) {
                    foreach ($followers as $follower) {
                        if (!empty($follower) && !empty($follower->id)) {
                            $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $follower->id);
                            if ($this->addUserEventLog(null, $event) && $event->privacy . "" == "true") {
                                RedisUtils::addItem($redis, REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, json_encode($event), $event->startDateTimeLong);
                            }
                        }
                    }
                }
            }
            /*
             * followers list
             */

            /*
             * city list
             */
            if ($event->privacy . "" == "true") {
                $event->userRelation = $userRelationEmpty;
                $event->userEventLog = array();
                $key = $event->loc_city;
                if (empty($key)) {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        $key = "ww";
                    } else {
                        $key = "epmty";
                    }
                    RedisUtils::addItem($redis, REDIS_PREFIX_CITY . $key, json_encode($event), $event->startDateTimeLong);
                } else {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . "ww", json_encode($event), $event->startDateTimeLong);
                    } else {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . $key, json_encode($event), $event->startDateTimeLong);
                    }
                }
            }
            /*
             * city list
             */

            /*
             * find users that might interest this event
             */
            $this->findUserForEvents(false);
            /*
             * find users that might interest this event
             */
        } else {
            $log->logInfo("event > addEvent >  event empty");
        }
    }

    public function updateEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > updateEvent >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                $event->getHeaderImage();
                $event->images = array();
                $event->getAttachLink();
                $event->getTags();
                $event->getLocCity();
                $event->getWorldWide();
                $event->attendancecount = Neo4jEventUtils::getEventAttendanceCount($event->id);
                $event->commentCount = CommentUtil::getCommentListSizeByEvent($event->id, null);
            } catch (Exception $exc) {
                $log->logError("event > updateEvent Error" . $exc->getTraceAsString());
            }

            $effectedKeys = array();

            /*
             * my timety
             */
            if (!empty($this->userID)) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $it = $redis->removeItemByIdReturnItem(REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, $this->eventID);
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                if ($this->addUserEventLog($it, $event)) {
                    RedisUtils::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
                }
                array_push($effectedKeys, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY);
            }

            if (!empty($this->userID) && $this->userID != $event->creatorId) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $it = $redis->removeItemByIdReturnItem(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, $this->eventID);
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $event->creatorId);
                if ($this->addUserEventLog($it, $event)) {
                    RedisUtils::addItem($redis, REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
                }
                array_push($effectedKeys, REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY);
            }
            /*
             * my timety
             */


            $userRelationEmpty = new stdClass();
            $userRelationEmpty->joinType = 0;
            $userRelationEmpty->like = false;
            $userRelationEmpty->reshare = false;
            /*
             * upcoming event list
             */
            $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
            $redis->removeItemById(REDIS_LIST_UPCOMING_EVENTS, $this->eventID);
            $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > updateEvent >  Privacy - '" . $event->privacy . "'");
            if ($event->privacy . "" == "true") {
                $event->userRelation = $userRelationEmpty;
                $event->userEventLog = null;
                RedisUtils::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
                array_push($effectedKeys, REDIS_LIST_UPCOMING_EVENTS);
            }
            /*
             * upcoming event list
             */



            /*
             * followers list
             */
            if (!empty($this->userID)) {
                $followers = Neo4jUserUtil::getUserFollowerList($this->userID);
                if (!empty($followers)) {
                    foreach ($followers as $follower) {
                        if (!empty($follower) && !empty($follower->id)) {
                            $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                            $it = $redis->removeItemByIdReturnItem(REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, $this->eventID);
                            $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $follower->id);
                            if ($this->addUserEventLog($it, $event) && $event->privacy . "" == "true") {
                                RedisUtils::addItem($redis, REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, json_encode($event), $event->startDateTimeLong);
                            }
                            array_push($effectedKeys, REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING);
                        }
                    }
                }
            }
            /*
             * followers list
             */

            /*
             * find users that might interest this event
             */
            $keyArray = $this->findUserForEvents(true);
            /*
             * find users that might interest this event
             */



            /*
             * city list
             */

            $event->userRelation = $userRelationEmpty;
            $event->userEventLog = array();
            $key = $event->loc_city;
            if (empty($key)) {
                if ($event->worldwide == 1 || $event->worldwide == "1") {
                    $key = "ww";
                } else {
                    $key = "epmty";
                }
                $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                $redis->removeItemById(REDIS_PREFIX_CITY . $key, $this->eventID);
                if ($event->privacy . "" == "true") {
                    RedisUtils::addItem($redis, REDIS_PREFIX_CITY . $key, json_encode($event), $event->startDateTimeLong);
                }
                array_push($effectedKeys, REDIS_PREFIX_CITY . $key);
            } else {
                $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                $redis->removeItemById(REDIS_PREFIX_CITY . $key, $this->eventID);
                if ($event->privacy . "" == "true") {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . "ww", json_encode($event), $event->startDateTimeLong);
                    } else {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . $key, json_encode($event), $event->startDateTimeLong);
                    }
                }
                array_push($effectedKeys, REDIS_PREFIX_CITY . $key);
            }
            /*
             * city list
             */


            /*
             * delete unused placed
             */
            $effectedKeys = array_merge($keyArray, $effectedKeys);
            $friendsKeys = $redis->keys("user:friend*");
            $effectedKeys = array_merge($friendsKeys, $effectedKeys);
            $allkeys = $redis->keys("*");
            foreach ($allkeys as $key) {
                if (!in_array($key, $effectedKeys)) {
                    $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                    $redis->removeItemById($key, $this->eventID);
                }
            }
        } else {
            $log->logInfo("event > updateEvent >  event empty");
        }
    }

    public function findUserForEvents($rem = true) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > findUserForEvents >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time . " added : " . $rem);
        $keyArray = array();
        if (!empty($this->eventID)) {
            $event = new Event();
            $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
            $event->getHeaderImage();
            $event->images = array();
            $event->getAttachLink();
            $event->getTags();
            $event->getLocCity();
            $event->getWorldWide();
            $event->attendancecount = Neo4jEventUtils::getEventAttendanceCount($event->id);
            $event->commentCount = CommentUtil::getCommentListSizeByEvent($event->id, null);
            $log->logInfo("event > findUserForEvents >  event from neo4j : " . $event->id);
            $users = Neo4jRecommendationUtils::getUserForEvent($this->eventID);
            $log->logInfo("event > findUserForEvents >  recommened users : " . sizeof($users));
            foreach ($users as $user) {
                $userId = $user->getProperty(PROP_USER_ID);
                if (!empty($userId)) {
                    $usr = UserUtils::getUserById($userId);
                    $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($this->eventID, $userId);
                    $redis = new Predis\Client();
                    $log->logInfo("event > findUserForEvents >  remove ? : " . $rem);
                    if ($rem) {
                        $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                        $redis->removeItemById(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, $this->eventID);
                    }
                    if (!empty($usr) && $event->loc_city == $usr->location_city) {
                        //($event->worldwide == 1 || $event->worldwide == "1")
                        if (SERVER_PROD && $event->privacy . "" == "true") {
                            RedisUtils::addItem($redis, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, json_encode($event), $event->startDateTimeLong);
                        } else {
                            $log->logInfo("Redis addItem Item simulated");
                        }
                    }
                    array_push($keyArray, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING);
                }
            }
        }
        return $keyArray;
    }

    public function followUser() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("event > followUser >  start userId : " . $this->userID . " followId : " . $this->followID . " type : " . $this->type . " time : " . $this->time);

        if (!empty($this->userID) && !empty($this->followID)) {
            $redis = new Predis\Client();
            $events = $redis->zrevrange(REDIS_PREFIX_USER . $this->followID . REDIS_SUFFIX_MY_TIMETY, 0, -1);
            if (!empty($events)) {
                foreach ($events as $evt) {
                    $event = new Event();
                    $event = json_decode($evt);
                    if (!empty($event) && $event->privacy . "" == "true") {
                        $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                        $it = $redis->removeItemByIdReturnItem(REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_FOLLOWING, $event->id);
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                        if ($this->addUserEventLog($it, $event, $evt)) {
                            RedisUtils::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_FOLLOWING, json_encode($event), $event->startDateTimeLong);
                        }
                    }
                }
            }
        }
    }

    public function unFollowUser() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("event > unFollowUser >  start userId : " . $this->userID . " followId : " . $this->followID . " type : " . $this->type . " time : " . $this->time);

        if (!empty($this->userID) && !empty($this->followID)) {
            $redis = new Predis\Client();
            $events = $redis->zrevrange(REDIS_PREFIX_USER . $this->followID . REDIS_SUFFIX_MY_TIMETY, 0, -1);
            if (!empty($events)) {
                foreach ($events as $evt) {
                    $event = new Event();
                    $event = json_decode($evt);
                    if (!empty($event) && $event->privacy . "" == "true") {
                        $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                        $it = $redis->removeItemByIdReturnItem(REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_FOLLOWING, $event->id);
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                        if ($this->addUserEventLog($it, $event, $evt)) {
                            RedisUtils::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_FOLLOWING, json_encode($event), $event->startDateTimeLong);
                        }
                    }
                }
            }
        }
    }

    public function addUserEventLog($item, &$event, $seconditem = null) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("addUserEventLog > Start ");
        if (!empty($event)) {
// this user created that event
            $usrR = new UserEventLog();
            $usrR->action = $this->type;
            $usrR->eventId = $event->id;
            $usrR->userId = $this->userID;
            $usrR->time = null; //$this->time;
            if ($this->type == REDIS_USER_INTERACTION_CREATED) {
//new 
                $array = array();
                array_push($array, $usrR);
                $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_CREATED);
                $event->userEventLog = $array;
                return true;
            } else {
                $rel = new UserEventLog();
                $array = null;
                $secondArray = null;
                if (!empty($item)) {
                    try {
                        $evt = json_decode($item);
                        $array = $evt->userEventLog;
                    } catch (Exception $exc) {
                        $log->logError("addUserEventLog >  Error : " . $exc->getTraceAsString());
                        $array = null;
                    }
                }
                if (!empty($seconditem)) {
                    try {
                        $evt = json_decode($seconditem);
                        $secondArray = $evt->userEventLog;
                    } catch (Exception $exc) {
                        $log->logError("addUserEventLog >  Error : " . $exc->getTraceAsString());
                        $secondArray = null;
                    }
                }
                $array = RedisUtils::fixArray($array);
                $secondArray = RedisUtils::fixArray($secondArray);

                if ($this->type == REDIS_USER_INTERACTION_FOLLOW) {
                    $added = false;
                    foreach ($secondArray as $r) {
                        if ($r->userId == $this->followID) {
                            $exits = true;
                            foreach ($array as $p) {
                                if ($r->userId == $p->userId && $r->action == $p->action) {
                                    $exits = false;
                                    $added = true;
                                }
                            }
                            if ($exits) {
                                array_push($array, $r);
                                $added = true;
                            }
                        }
                    }
                    $event->userEventLog = $array;
                    return $added;
                } else if ($this->type == REDIS_USER_INTERACTION_UNFOLLOW) {
                    $log->logInfo("addUserEventLog >  array " . sizeof($array));
                    for ($i = sizeof($array) - 1; $i >= 0; $i--) {
                        $r = $array[$i];
                        $log->logInfo("addUserEventLog >  array " . $r->userId . " follow " . $this->followID);
                        if ($r->userId == $this->followID) {
                            unset($array[$i]);
                        }
                    }
                } else if ($this->type == REDIS_USER_INTERACTION_UPDATED) {
                    array_push($array, $usrR);
                    $event->userEventLog = $array;
                    $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_UPDATED);
                    $event->userEventLog = $array;
                    return true;
                } else if ($this->type == REDIS_USER_INTERACTION_JOIN) {
                    $added = true;
                    for ($i = 0; $i < sizeof($array); $i++) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_JOIN && $rel->userId == $this->userID) {
//$rel->time = $this->time;
                            $log->logInfo("addUserEventLog >  updated array " . REDIS_USER_INTERACTION_JOIN);
                            $array[$i] = $rel;
                            $added = false;
                            break;
                        }
                    }
                    if ($added) {
                        array_push($array, $usrR);
                        $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_JOIN);
                    }
                    $event->userEventLog = $array;
                    return true;
                } else if ($this->type == REDIS_USER_INTERACTION_MAYBE) {
                    $added = true;
                    for ($i = 0; $i < sizeof($array); $i++) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_MAYBE && $rel->userId == $this->userID) {
//$rel->time = $this->time;
                            $log->logInfo("addUserEventLog >  updated array " . REDIS_USER_INTERACTION_MAYBE);
                            $array[$i] = $rel;
                            $added = false;
                            break;
                        }
                    }
                    if ($added) {
                        array_push($array, $usrR);
                        $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_MAYBE);
                    }
                    $event->userEventLog = $array;
                    return true;
                } else if ($this->type == REDIS_USER_INTERACTION_DECLINE || $this->type == REDIS_USER_INTERACTION_IGNORE) {
                    for ($i = sizeof($array) - 1; $i >= 0; $i--) {
                        $rel = $array[$i];
                        if (($rel->action == REDIS_USER_INTERACTION_JOIN || $rel->action == REDIS_USER_INTERACTION_MAYBE) && $rel->userId == $this->userID) {
                            unset($array[$i]);
                            $log->logInfo("addUserEventLog >  removed array " . REDIS_USER_INTERACTION_DECLINE . " rel -> " . $rel->action);
                        }
                    }
                } else if ($this->type == REDIS_USER_INTERACTION_LIKE) {
                    $added = true;
                    for ($i = 0; $i < sizeof($array); $i++) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_LIKE && $rel->userId == $this->userID) {
//$rel->time = $this->time;
                            $array[$i] = $rel;
                            $added = false;
                            $log->logInfo("addUserEventLog >  updated array " . REDIS_USER_INTERACTION_LIKE);
                            break;
                        }
                    }
                    if ($added) {
                        array_push($array, $usrR);
                        $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_LIKE);
                    }
                    $event->userEventLog = $array;
                    return true;
                } else if ($this->type == REDIS_USER_INTERACTION_UNLIKE) {
                    for ($i = sizeof($array) - 1; $i >= 0; $i--) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_LIKE && $rel->userId == $this->userID) {
                            unset($array[$i]);
                            $log->logInfo("addUserEventLog >  removed array " . REDIS_USER_INTERACTION_UNLIKE);
                        }
                    }
                } else if ($this->type == REDIS_USER_INTERACTION_RESHARE) {
                    $added = true;
                    for ($i = 0; $i < sizeof($array); $i++) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_RESHARE && $rel->userId == $this->userID) {
//$rel->time = $this->time;
                            $array[$i] = $rel;
                            $added = false;
                            $log->logInfo("addUserEventLog >  updated array " . REDIS_USER_INTERACTION_RESHARE);
                            break;
                        }
                    }
                    if ($added) {
                        array_push($array, $usrR);
                        $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_RESHARE);
                    }
                    $event->userEventLog = $array;
                    return true;
                } else if ($this->type == REDIS_USER_INTERACTION_UNSHARE) {
                    for ($i = sizeof($array) - 1; $i >= 0; $i--) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_RESHARE && $rel->userId == $this->userID) {
                            unset($array[$i]);
                            $log->logInfo("addUserEventLog >  removed array " . REDIS_USER_INTERACTION_UNSHARE);
                        }
                    }
                }
                $event->userEventLog = $array;
                if (empty($array) || sizeof($array) < 1) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        return false;
    }

}

?>
