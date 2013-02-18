<?php

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

class EventProcessor {

    public $eventID;

    public function addEvent() {
        $log = KLogger::instance('/home/ubuntu/log/', KLogger::DEBUG);

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
                $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > addEvent > inserting item");
                $return = $redis->zadd(REDIS_LIST_UPCOMING_EVENTS, $event->startDateTimeLong, json_encode($event));
                $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > addEvent >  inserted item " . json_encode($return));
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
                $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($this->id, $event->creatorId);
                $log->logInfo(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY . " > addEvent > inserting item");
                $return = $redis->zadd(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, $event->startDateTimeLong, json_encode($event));
                $log->logInfo(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY . " > addEvent >  inserted item " . json_encode($return));
            }
            /*
             * my timety
             */
        } else {
            $log->logInfo("event > addEvent >  event empty");
        }
    }

    public function updateEvent() {
        $log = KLogger::instance('/home/ubuntu/log/', KLogger::DEBUG);

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
                    $return = $redis->zrem(REDIS_LIST_UPCOMING_EVENTS, $item);
                    $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > updateEvent >  removed item - " . json_encode($return));
                    if ($event->privacy . "" == "true") {
                        //insert new item
                        $return = $redis->zadd(REDIS_LIST_UPCOMING_EVENTS, $event->startDateTimeLong, json_encode($event));
                        $log->logInfo(REDIS_LIST_UPCOMING_EVENTS . " > updateEvent >  insert item - " . json_encode($return));
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
                        $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($this->id, $event->creatorId);
                        $log->logInfo(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY . " > updateEvent >  Privacy - '" . $event->privacy . "'");
                        //remove item
                        $return = $redis->zrem(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, $item);
                        $log->logInfo(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY . " > updateEvent >  removed item - " . json_encode($return));
                        //insert new item
                        $return = $redis->zadd(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY, $event->startDateTimeLong, json_encode($event));
                        $log->logInfo(REDIS_PREFIX_USER . $event->creatorId . REDIS_SUFFIX_MY_TIMETY . " > updateEvent >  insert item - " . json_encode($return));
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

}

?>
