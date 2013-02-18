<?php

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

class EventProcessor {

    public $eventID;

    public function addEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > addEvent > start");

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
             * Popular event list
             */
            if ($event->privacy . "" == "true") {
                EventProcessor::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
            }
            /*
             * Popular event list
             */


            /*
             * followers list
             */
            //TODO
            /*
             * followers list
             */

            /*
             * my timety
             */
            if (!empty($event->creatorId)) {
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $event->creatorId);
                EventProcessor::addItem($redis, REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
            }
            /*
             * my timety
             */
        } else {
            $log->logInfo("event > addEvent >  event empty");
        }
    }

    public function updateEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("event > updateEvent >  start");

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
             * Popular event list
             */
            $events = $redis->zrevrange(REDIS_LIST_UPCOMING_EVENTS, 0, -1);
            foreach ($events as $item) {
                $evt = new Event();
                $evt = json_decode($item);
                if ($evt->id == $this->eventID) {
                    $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > updateEvent >  Privacy - '" . $event->privacy . "'");
                    //remove item
                    EventProcessor::removeItem($redis,REDIS_LIST_UPCOMING_EVENTS, $item);
                    if ($event->privacy . "" == "true") {
                        //insert new item
                        EventProcessor::addItem($redis, REDIS_LIST_UPCOMING_EVENTS, json_encode($event), $event->startDateTimeLong);
                    }
                    break;
                }
            }
            /*
             * Popular event list
             */

            /*
             * followers list
             */
            //TODO
            /*
             * followers list
             */


            /*
             * my timety
             */
            if (!empty($event->creatorId)) {
                $events = $redis->zrevrange(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, 0, -1);
                foreach ($events as $item) {
                    $evt = new Event();
                    $evt = json_decode($item);
                    if ($evt->id == $this->eventID) {
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $event->creatorId);
                        //remove item
                        EventProcessor::removeItem($redis, REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, $item);
                        //insert new item
                        EventProcessor::addItem($redis, REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, json_encode($event), $event->startDateTimeLong);
                        break;
                    }
                }
            }
            /*
             * my timety
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

}

?>
