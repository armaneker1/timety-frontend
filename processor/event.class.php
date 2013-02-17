<?php

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

class EventProcessor {

    public $eventID;

    public function addEvent() {
        $log = KLogger::instance('/home/ubuntu/log/', KLogger::DEBUG);

        $log->logInfo("event.popular.worldwide > addEvent > creating");

        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                $event->getHeaderImage();
                $event->images = array();
            } catch (Exception $exc) {
                $log->logError("vent.popular.worldwide > addEvent Error". $exc->getTraceAsString());
            }
            $log->logInfo("event.popular.worldwide > addEvent > ready to notify start - " . strtotime("now"));
            $return = $redis->zadd("popular:worldwide", $event->startDateTimeLong, json_encode($event));
            $log->logInfo("event.popular.worldwide > addEvent >  ready to notify end - " . json_encode($return));
        } else {
            $log->logInfo("event.popular.worldwide > addEvent >  event empty");
        }
    }

    public function updateEvent() {
        $log = KLogger::instance('/home/ubuntu/log/', KLogger::DEBUG);

        $log->logInfo("event.popular.worldwide > updateEvent >  creating");

        $redis = new Predis\Client();
        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                $event->getHeaderImage();
                $event->images = array();
            } catch (Exception $exc) {
                $log->logError("vent.popular.worldwide > updateEvent Error". $exc->getTraceAsString());
            }
            $events = $redis->zrevrange("event:popular:worldwide", 0, -1);
            foreach ($events as $item) {
                $evt = new Event();
                $evt = json_decode($item);
                if ($evt->id == $this->eventID) {
                    $return = $redis->zrem("popular:worldwide", $item);
                    $log->logInfo("event.popular.worldwide > updateEvent >  ready to notify end 1 - " . json_encode($return));
                    $return = $redis->zadd("popular:worldwide", $event->startDateTimeLong, json_encode($event));
                    $log->logInfo("event.popular.worldwide > updateEvent >  ready to notify end 2 - " . json_encode($return));
                    break;
                }
            }
        } else {
            $log->logInfo("event.popular.worldwide > updateEvent >  event empty");
        }
    }

}

?>
