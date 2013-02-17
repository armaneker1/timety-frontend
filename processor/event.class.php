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
            $events = $redis->zrange("event:popular:worldwide", 0, -1, array('withscores' => true));
            foreach ($events as $item) {
                $evt = new Event();
                $evt = json_decode($item[0]);
                if ($evt->id == $this->eventID) {
                    $return = $redis->zrem("popular:worldwide", $item[0]);
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
