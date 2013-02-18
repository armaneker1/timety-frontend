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
            if ($event->privacy."" == "true") {
                $log->logInfo("event.popular.worldwide > addEvent > inserting item");
                $return = $redis->zadd(REDIS_LIST_UPCOMING_EVENTS, $event->startDateTimeLong, json_encode($event));
                $log->logInfo("event.popular.worldwide > addEvent >  inserted item " . json_encode($return));
            }
            /*
             * Popular event list
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
                    $log->logInfo("event.popular.worldwide > updateEvent >  Privacy - '" .$event->privacy."'");
                    //remove item
                    $return = $redis->zrem(REDIS_LIST_UPCOMING_EVENTS, $item);
                    $log->logInfo("event.popular.worldwide > updateEvent >  removed item - " . json_encode($return));
                    if ($event->privacy."" == "true") {
                        //insert new item
                        $return = $redis->zadd(REDIS_LIST_UPCOMING_EVENTS, $event->startDateTimeLong, json_encode($event));
                        $log->logInfo("event.popular.worldwide > updateEvent >  insert item - " . json_encode($return));
                    }
                    break;
                }
            }
            /*
             * Popular event list
             */
        } else {
            $log->logInfo("event > updateEvent >  event empty");
        }
    }

}

?>
