<?php

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

class UserProcessor {

    public $userID;
    public $type;
    public $time;

    public function updateUser() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("user > updateUser >  start userId : " . $this->userID . " type : " . $this->type . " time : " . $this->time);

        $redis = new Predis\Client();
        if (!empty($this->userID)) {
            $user = UserUtils::getUserById($this->userID);
            if (!empty($user)) {

                /*
                 * my timety
                 */
                $events = $redis->zrevrange(REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, 0, -1);
                foreach ($events as $item) {
                    $evt = json_decode($item);
                    if ($evt->creatorId == $this->userID) {
                        UserProcessor::removeItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, $item);
                        $event = Neo4jEventUtils::getNeo4jEventById($evt->id);
                        try {
                            $event->getHeaderImage();
                            $event->images = array();
                            $event->getAttachLink();
                        } catch (Exception $exc) {
                            $log->logError("event > addEvent Error" . $exc->getTraceAsString());
                        }
                        $event->userEventLog = $evt->userEventLog;
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($evt->id, $this->userID);
                        UserProcessor::addItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
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
                    if ($evt->creatorId == $this->userID) {
                        UserProcessor::removeItem($redis, REDIS_LIST_UPCOMING_EVENTS, $item);
                        $event = Neo4jEventUtils::getNeo4jEventById($evt->id);
                        try {
                            $event->getHeaderImage();
                            $event->images = array();
                            $event->getAttachLink();
                        } catch (Exception $exc) {
                            $log->logError("event > addEvent Error" . $exc->getTraceAsString());
                        }
                        $event->userRelation = $userRelationEmpty;
                        $event->userEventLog = null;
                        UserProcessor::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
                    }
                }
                // }
                /*
                 * upcoming event list
                 */

                /*
                 * general list
                 */
                if (!empty($this->userID)) {
                    $keys = $redis->keys("*");
                    if (!empty($keys)) {
                        foreach ($keys as $key) {
                            if (!empty($key)) {
                                $events = $redis->zrevrange($key, 0, -1);
                                foreach ($events as $item) {
                                    $evt = json_decode($item);
                                    if ($evt->creatorId == $this->userID) {
                                        UserProcessor::removeItem($redis, $key, $item);
                                        $event = Neo4jEventUtils::getNeo4jEventById($evt->id);
                                        try {
                                            $event->getHeaderImage();
                                            $event->images = array();
                                            $event->getAttachLink();
                                        } catch (Exception $exc) {
                                            $log->logError("event > addEvent Error" . $exc->getTraceAsString());
                                        }
                                        $uId = null;
                                        try {
                                            $arr = explode(":", $key);
                                            $uId = $arr[2];
                                        } catch (Exception $exc) {
                                            $log->logError("event > addEvent Error" . $exc->getTraceAsString());
                                        }
                                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $uId);
                                        $event->userEventLog = $evt->userEventLog;
                                        UserProcessor::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                                    }
                                }
                            }
                        }
                    }
                }
                /*
                 * general list
                 */
            } else {
                $log->logInfo("user > updateUser >  user empty");
            }
        } else {
            $log->logInfo("user > updateUser >  user empty");
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

    public static function fixArray($array = null) {
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
