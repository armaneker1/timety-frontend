<?php

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

class EventProcessor {

    public $userID;
    public $eventID;
    public $type;
    public $time;

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
            } catch (Exception $exc) {
                $log->logError("event > addEvent Error" . $exc->getTraceAsString());
            }
            /*
             * my timety
             */
            if (!empty($this->userID)) {
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                $this->addUserEventLog(null, $event);

                EventProcessor::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
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
                EventProcessor::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
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
                                EventProcessor::addItem($redis, REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, json_encode($event), $event->startDateTimeLong);
                            }
                        }
                    }
                }
            }
            /*
             * followers list
             */
        } else {
            $log->logInfo("event > addEvent >  event empty");
        }
    }

    public function updateEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > updateEvent >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type);

        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                $event->getHeaderImage();
                $event->images = array();
            } catch (Exception $exc) {
                $log->logError("event > updateEvent Error" . $exc->getTraceAsString());
            }

            /*
             * my timety
             */
            if (!empty($this->userID)) {
                $events = $redis->zrevrange(REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, 0, -1);
                $it = null;
                foreach ($events as $item) {
                    $evt = new Event();
                    $evt = json_decode($item);
                    if ($evt->id == $this->eventID) {
                        $it = $item;
                        //remove item
                        EventProcessor::removeItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, $item);
                        break;
                    }
                }
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                //insert new item
                if ($this->addUserEventLog($it, $event)) {
                    EventProcessor::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
                }
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
            // if ($this->type == REDIS_USER_INTERACTION_UPDATED) {
            $events = $redis->zrevrange(REDIS_LIST_UPCOMING_EVENTS, 0, -1);
            foreach ($events as $item) {
                $evt = json_decode($item);
                if ($evt->id == $this->eventID) {
                    //remove item
                    EventProcessor::removeItem($redis, REDIS_LIST_UPCOMING_EVENTS, $item);
                    break;
                }
            }
            $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > updateEvent >  Privacy - '" . $event->privacy . "'");
            if ($event->privacy . "" == "true") {
                $event->userRelation = $userRelationEmpty;
                $event->userEventLog = null;
                //insert new item
                EventProcessor::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
            }
            // }
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
                            $events = $redis->zrevrange(REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, 0, -1);
                            $it = null;
                            foreach ($events as $item) {
                                $evt = json_decode($item);
                                if ($evt->id == $this->eventID) {
                                    $it = $item;
                                    //remove item
                                    EventProcessor::removeItem($redis, REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, $item);
                                    break;
                                }
                            }
                            $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $follower->id);
                            if ($this->addUserEventLog($it, $event) && $event->privacy . "" == "true") {
                                //insert new item
                                EventProcessor::addItem($redis, REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING, json_encode($event), $event->startDateTimeLong);
                            }
                        }
                    }
                }
            }
            /*
             * followers list
             */
        } else {
            $log->logInfo("event > updateEvent >  event empty");
        }
    }

    public static function addItem($redis, $key, $item, $score) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($redis) && !empty($key)) {
            $log->logInfo($key . " > addItem > inserting item");
            $return = $redis->zadd($key, $score, $item);
            $log->logInfo($key . " > addItem >  inserted item " . json_encode($return));
            return $return;
        }
        return null;
    }

    public static function removeItem($redis, $key, $item) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($redis) && !empty($key)) {
            $log->logInfo($key . " > removeItem > removing item");
            $return = $redis->zrem($key, $item);
            $log->logInfo($key . " > removeItem >  removed item " . json_encode($return));
            return $return;
        }
        return null;
    }

    public function addUserEventLog($item, &$event) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("addUserEventLog > Start ");
        if (!empty($event)) {
            // this user created that event
            $usrR = new UserEventLog();
            $usrR->action = $this->type;
            $usrR->eventId = $event->id;
            $usrR->userId = $this->userID;
            $usrR->time = $this->time;
            if ($this->type == REDIS_USER_INTERACTION_CREATED) {
                //new 
                $array = array();
                array_push($array, $usrR);
                $log->logInfo("addUserEventLog >  added array " . REDIS_USER_INTERACTION_CREATED);
                $event->userEventLog = $array;
                return true;
            } else {
                $rel = new UserEventLog();
                $evt = json_decode($item);
                $array = $evt->userEventLog;
                $array = EventProcessor::fixArray($array);
                if ($this->type == REDIS_USER_INTERACTION_UPDATED) {
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
                            $rel->time = $this->time;
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
                            $rel->time = $this->time;
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
                    for ($i = 0; $i < sizeof($array); $i++) {
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
                            $rel->time = $this->time;
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
                    for ($i = 0; $i < sizeof($array); $i++) {
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
                            $rel->time = $this->time;
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
                    for ($i = 0; $i < sizeof($array); $i++) {
                        $rel = $array[$i];
                        if ($rel->action == REDIS_USER_INTERACTION_RESHARE && $rel->userId == $this->userID) {
                            unset($array[$i]);
                            $log->logInfo("addUserEventLog >  removed array " . REDIS_USER_INTERACTION_UNSHARE);
                        }
                    }
                }
                $event->userEventLog = $array;
            }
        }
        return false;
    }

    public static function fixArray($array) {
        $result = array();
        if (empty($array)) {
            $array = array();
        }
        if (!is_array($array)) {
            $array = json_decode($array);
        }
        foreach ($array as $a) {
            if (!empty($a)) {
                array_push($result, $a);
            }
        }
        return $result;
    }

}

?>
