<?php

require_once __DIR__ . '/../utils/Functions.php';

class EventProcessor {

    public $userID;
    public $eventID;
    public $type;
    public $time;
    public $followID;

    public function addEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);
        $log->logInfo("event > addEvent > start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);
        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                //Event Information
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
                return;
            }
            /*
             * adding my timety
             */
            if (!empty($this->userID)) {
                $mytimetyKey = REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY;
                //add user relation 
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                //add event log clear one :D
                $this->addUserEventLog(null, $event);
                //add event to redis
                RedisUtils::addItem($redis, $mytimetyKey, json_encode($event), $event->startDateTimeLong);
                EventKeyListUtil::updateEventKey($event->id, $mytimetyKey);
            }


            //empty user relation
            $userRelationEmpty = new stdClass();
            $userRelationEmpty->joinType = 0;
            $userRelationEmpty->like = false;
            $userRelationEmpty->reshare = false;

            // event privacy true adding event 
            if ($event->privacy . "" == "true") {

                /*
                 * adding city list
                 */
                $event->userRelation = $userRelationEmpty;
                $event->userEventLog = array();
                $key = $event->loc_city;
                if (empty($key)) {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        $log->logError("event > addEvent Evet location empty but ww is true adding ww");
                        $key = REDIS_PREFIX_CITY . "ww";
                    } else {
                        $log->logError("event > addEvent Evet location empty and  ww is false adding empty list");
                        $key = REDIS_PREFIX_CITY . "epmty";
                    }
                    RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->id, $key);
                } else {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . "ww", json_encode($event), $event->startDateTimeLong);
                        EventKeyListUtil::updateEventKey($event->id, REDIS_PREFIX_CITY . "ww");
                    } else {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . $key, json_encode($event), $event->startDateTimeLong);
                        EventKeyListUtil::updateEventKey($event->id, REDIS_PREFIX_CITY . $key);
                    }
                }
                Queue::addEventForOthers($this->eventID, $this->userID);
            }
        } else {
            $log->logError("event > addEvent >  event empty");
        }
    }

    public function addEventForOthers() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);
        $log->logInfo("event > addEventForOthers > start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);
        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                //Event Information
                $event->getHeaderImage();
                $event->images = array();
                $event->getAttachLink();
                $event->getTags();
                $event->getLocCity();
                $event->getWorldWide();
                $event->attendancecount = Neo4jEventUtils::getEventAttendanceCount($event->id);
                $event->commentCount = CommentUtil::getCommentListSizeByEvent($event->id, null);
            } catch (Exception $exc) {
                $log->logError("event > addEventForOthers Error" . $exc->getTraceAsString());
                return;
            }

            // event privacy true adding event 
            if ($event->privacy . "" == "true") {

                // adding followers list
                if (!empty($this->userID)) {
                    $followers = Neo4jUserUtil::getUserFollowerList($this->userID);
                    if (!empty($followers)) {
                        foreach ($followers as $follower) {
                            if (!empty($follower) && !empty($follower->id)) {
                                // follower event relation
                                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $follower->id);
                                // add event log 
                                $followerFollowKey = REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING;
                                if ($this->addUserEventLog(null, $event)) {
                                    RedisUtils::addItem($redis, $followerFollowKey, json_encode($event), $event->startDateTimeLong);
                                    EventKeyListUtil::updateEventKey($event->id, $followerFollowKey);
                                } else {
                                    EventKeyListUtil::deleteRecordForEvent($event->id, $followerFollowKey);
                                }
                            }
                        }
                    }
                }
                // find users that might interest this event
                $effedctedkeys = $this->findUserForEvents(false);
                foreach ($effedctedkeys as $key) {
                    EventKeyListUtil::updateEventKey($event->id, $key);
                }
            }
        } else {
            $log->logError("event > addEventForOthers >  event empty");
        }
    }

    public function addEventToFollowers() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);
        $log->logInfo("event > addEventToFollowers >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);
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
                $log->logError("event > likeEvent Error" . $exc->getTraceAsString());
                return;
            }
            /*
             * adding followers list
             */
            if (!empty($this->userID)) {
                $followers = Neo4jUserUtil::getUserFollowerList($this->userID);
                if (!empty($followers)) {
                    foreach ($followers as $follower) {
                        if (!empty($follower) && !empty($follower->id)) {
                            $key = REDIS_PREFIX_USER . $follower->id . REDIS_SUFFIX_FOLLOWING;
                            $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                            $it = $redis->removeItemByIdReturnItem($key, $this->eventID);
                            $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $follower->id);
                            if ($this->addUserEventLog($it, $event) && $event->privacy . "" == "true") {
                                RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                                EventKeyListUtil::updateEventKey($this->eventID, $key);
                            } else {
                                EventKeyListUtil::deleteRecordForEvent($this->eventID, $key);
                            }
                        }
                    }
                }
            }
        }
    }

    public function likeEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);

        $log->logInfo("event > likeEvent >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

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
                $log->logError("event > likeEvent Error" . $exc->getTraceAsString());
                return;
            }

            /*
             *  adding my timety
             */
            if (!empty($this->userID)) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $key = REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY;
                $it = $redis->removeItemByIdReturnItem($key, $this->eventID);
                //get user rel to event
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                //add event log
                $result = $this->addUserEventLog($it, $event);
                if ($result && $event->privacy . "" == "true") {
                    RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->id, $key);
                } else {
                    EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                }
            }
            Queue::addEventToFollowers($this->eventID, $this->userID, $this->type);
        } else {
            $log->logInfo("event > likeEvent >  event empty");
        }
    }

    public function reshareEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);

        $log->logInfo("event > reshareEvent >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

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
                $log->logError("event > reshareEvent Error" . $exc->getTraceAsString());
                return;
            }

            /*
             *  adding my timety
             */
            if (!empty($this->userID)) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $key = REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY;
                $it = $redis->removeItemByIdReturnItem($key, $this->eventID);
                //get user rel to event
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                //add event log
                $result = $this->addUserEventLog($it, $event);
                if ($result && $event->privacy . "" == "true") {
                    RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->id, $key);
                } else {
                    EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                }
            }
            Queue::addEventToFollowers($this->eventID, $this->userID, $this->type);
        } else {
            $log->logInfo("event > reshareEvent >  event empty");
        }
    }

    public function joinEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);

        $log->logInfo("event > joinEvent >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

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
                $log->logError("event > joinEvent Error" . $exc->getTraceAsString());
                return;
            }

            /*
             *  adding my timety
             */
            if (!empty($this->userID)) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $key = REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY;
                $it = $redis->removeItemByIdReturnItem($key, $this->eventID);
                //get user rel to event
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                //add event log
                $result = $this->addUserEventLog($it, $event);
                if ($result && $event->privacy . "" == "true") {
                    RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->id, $key);
                } else {
                    EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                }
            }
            Queue::addEventToFollowers($this->eventID, $this->userID, $this->type);
            Queue::updateEventInfo($this->eventID);
        } else {
            $log->logInfo("event > joinEvent >  event empty");
        }
    }

    public function updateEventInfo() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);

        $log->logInfo("event > updateEventInfo >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

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
                $log->logError("event > updateEventInfo Error" . $exc->getTraceAsString());
                return;
            }

            /*
             *  adding creator my timety
             */
            if (!empty($event->creatorId)) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $key = REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY;
                $it = $redis->removeItemByIdReturnItem($key, $this->eventID);
                //get user rel to event
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $event->creatorId);
                //add event log
                $result = $this->addUserEventLog($it, $event);
                if ($result) {
                    RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->id, $key);
                } else {
                    EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                }
            }
            Queue::updateEventInfoForOthers($this->eventID);
        } else {
            $log->logInfo("event > updateEventInfo >  event empty");
        }
    }

    public function updateEventInfoForOthers() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > updateEventInfoForOthers >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);
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
                $log->logError("event > updateEventInfoForOthers Error" . $exc->getTraceAsString());
            }

            $keyList = EventKeyListUtil::getEventKeyList($event->id);
            if (!empty($keyList)) {
                foreach ($keyList as $key) {
                    $key = $key->getKey();
                    $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                    $it = $redis->removeItemByIdReturnItem($key, $event->id);
                    if (!empty($it)) {
                        $evt = json_decode($it);
                        $evt = UtilFunctions::cast('Event', $evt);
                        $event->userEventLog = $evt->userEventLog;
                        RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    } else {
                        $log->logError("event > updateEventInfoForOthers Event and empty");
                    }
                }
            }
        }
    }

    public function updateEventForOthers() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > updateEventForOthers >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);

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
                $log->logError("event > updateEventForOthers Error" . $exc->getTraceAsString());
            }

            $redisKeyList = EventKeyListUtil::getEventKeyList($event->id);

            //city remove except his own
            $city = $event->loc_city;
            $city_key = null;
            if ($event->privacy . "" == "true") {
                if (empty($city)) {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        $city_key = REDIS_PREFIX_CITY . "ww";
                    } else {
                        $city_key = REDIS_PREFIX_CITY . "epmty";
                    }
                } else {
                    $city_key = REDIS_PREFIX_CITY . $city;
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        $city_key = REDIS_PREFIX_CITY . "ww";
                    }
                }
            }
            //upcomming remove and city remove except his own
            if (!empty($redisKeyList)) {
                foreach ($redisKeyList as $key) {
                    if (UtilFunctions::startsWith($key, REDIS_PREFIX_CITY)) {
                        if ($city_key != $key) {
                            $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                            $redis->removeItemById($key, $event->id);
                            EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                        } else {
                            if (preg_match("/^" . REDIS_PREFIX_USER . "(.*?)" . REDIS_SUFFIX_UPCOMING . "/", $key)) {
                                $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                                $redis->removeItemById($key, $event->id);
                                EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                            }
                        }
                    }
                }
            }
            Queue::updateEventInfoForOthers($event->id);
            Queue::findInterestedPeopleForEvent($event->id);
        }
    }

    public function findInterestedPeopleForEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("event > findInterestedPeopleForEvent >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time);
        $this->findUserForEvents(true);
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

            /*
             * creator my timety
             */
            if (!empty($event->creatorId)) {
                $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                $key = REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY;
                $it = $redis->removeItemByIdReturnItem($key, $this->eventID);
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $event->creatorId);
                if ($this->addUserEventLog($it, $event)) {
                    RedisUtils::addItem($redis, REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->creatorId, $key);
                } else {
                    EventKeyListUtil::deleteRecordForEvent($event->creatorId, $key);
                }
            }
            /*
             * city list
             */
            $userRelationEmpty = new stdClass();
            $userRelationEmpty->joinType = 0;
            $userRelationEmpty->like = false;
            $userRelationEmpty->reshare = false;


            $event->userRelation = $userRelationEmpty;
            $event->userEventLog = array();
            $key = $event->loc_city;
            if (empty($key)) {
                if ($event->worldwide == 1 || $event->worldwide == "1") {
                    $key = REDIS_PREFIX_CITY . "ww";
                } else {
                    $key = REDIS_PREFIX_CITY . "epmty";
                }
                $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                $redis->removeItemById($key, $this->eventID);
                if ($event->privacy . "" == "true") {
                    RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                    EventKeyListUtil::updateEventKey($event->id, $key);
                } else {
                    EventKeyListUtil::deleteRecordForEvent($event->id, $key);
                }
            } else {
                $key = REDIS_PREFIX_CITY . $key;
                $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                $redis->removeItemById($key, $this->eventID);
                if ($event->privacy . "" == "true") {
                    if ($event->worldwide == 1 || $event->worldwide == "1") {
                        RedisUtils::addItem($redis, REDIS_PREFIX_CITY . "ww", json_encode($event), $event->startDateTimeLong);
                        EventKeyListUtil::updateEventKey($event->id, REDIS_PREFIX_CITY . "ww");
                    } else {
                        RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                        EventKeyListUtil::updateEventKey($event->id, $key);
                    }
                }
            }
            Queue::addEventToFollowers($event->id, $event->creatorId, $this->type);
            Queue::updateEventForOthers($event->id);
        } else {
            $log->logInfo("event > updateEvent >  event empty");
        }
    }

    public function findUserForEvents($rem = true) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::WARN);

        $log->logInfo("event > findUserForEvents >  start userId : " . $this->userID . " eventId : " . $this->eventID . " type : " . $this->type . " time : " . $this->time . " added : " . $rem);
        $keyArray = array();
        if (!empty($this->eventID)) {
            $event = new Event();
            $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
            if (!empty($event) && $event->privacy . "" == "true") {
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
                        $userKey = REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING;
                        $usr = UserUtils::getUserById($userId);
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($this->eventID, $userId);
                        $redis = new Predis\Client();
                        $log->logInfo("event > findUserForEvents >  remove ? : " . $rem);
                        if ($rem) {
                            $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                            $redis->removeItemById($userKey, $this->eventID);
                        }
                        if (!empty($usr) && $event->loc_city == $usr->location_city) {
                            if (SERVER_PROD) {
                                RedisUtils::addItem($redis, $userKey, json_encode($event), $event->startDateTimeLong);
                                EventKeyListUtil::updateEventKey($event->id, $userKey);
                            }
                        }
                        array_push($keyArray, $userKey);
                    }
                }
            }
        } else {
            $log->logError("event > findUserForEvents >  event is empty");
        }
        return $keyArray;
    }

    public function followUser() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("event > followUser >  start userId : " . $this->userID . " followId : " . $this->followID . " type : " . $this->type . " time : " . $this->time);

        if (!empty($this->userID) && !empty($this->followID)) {
            $redis = new Predis\Client();
            $key = REDIS_PREFIX_USER . $this->followID . REDIS_SUFFIX_MY_TIMETY;
            $events = $redis->zrevrange($key, 0, -1);
            if (!empty($events)) {
                foreach ($events as $evt) {
                    $event = new Event();
                    $event = json_decode($evt);
                    if (!empty($event) && $event->privacy . "" == "true") {
                        $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                        $it = $redis->removeItemByIdReturnItem($key, $event->id);
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                        if ($this->addUserEventLog($it, $event, $evt)) {
                            RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                            EventKeyListUtil::updateEventKey($event->id, $key);
                        } else {
                            EventKeyListUtil::deleteRecordForEvent($event->id, $key);
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
            $key = REDIS_PREFIX_USER . $this->followID . REDIS_SUFFIX_MY_TIMETY;
            $events = $redis->zrevrange($key, 0, -1);
            if (!empty($events)) {
                foreach ($events as $evt) {
                    $event = new Event();
                    $event = json_decode($evt);
                    if (!empty($event) && $event->privacy . "" == "true") {
                        $redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
                        $it = $redis->removeItemByIdReturnItem($key, $event->id);
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $this->userID);
                        if ($this->addUserEventLog($it, $event, $evt)) {
                            RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                            EventKeyListUtil::updateEventKey($event->id, $key);
                        } else {
                            EventKeyListUtil::deleteRecordForEvent($event->id, $key);
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
            $usrR->time = null;
            if ($this->type == REDIS_USER_INTERACTION_CREATED || $this->type == REDIS_USER_INTERACTION_CREATED_FOR_OTHER) {
                //new 
                $array = array();
                array_push($array, $usrR);
                $log->logInfo("addUserEventLog >  added array " . $this->type);
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
                    foreach ($secondArray as $r) {
                        if ($r->userId == $this->followID) {
                            $exits = true;
                            foreach ($array as $p) {
                                if ($r->userId == $p->userId && $r->action == $p->action) {
                                    $exits = false;
                                }
                            }
                            if ($exits) {
                                array_push($array, $r);
                            }
                        }
                    }
                    $event->userEventLog = $array;
                    return true;
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
