<?php

require_once __DIR__ . '/../apis/Stomp/Stomp.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Queue
 *
 * @author mehmet
 */
class Queue {

    public static function addEventToPopular($eventId) {
        self::send("popular", "addEvent", array(
            "eventID" => $eventId
        ));
    }
    
    public static function updateEventToPopular($eventId) {
        self::send("popular", "updateEvent", array(
            "eventID" => $eventId
        ));
    }

    //--------------------------------------------------------------------------

    private static function send($method, $action, $obj) {
        $obj["method"] = $method;
        $obj["action"] = $action;
        $conn = self::getConnection();
        self::getConnection()->send("timety", json_encode($obj), array('persistent' => 'true'));
        $conn->disconnect();
    }

    private static function getConnection() {
        try {
            $conn = new Stomp("tcp://54.228.209.226:61613");
            $conn->connect();
            return $conn;
        } catch (StompException $e) {
            echo "Connection Error: " . $e->getDetails();
        }
        return null;
    }

}

?>
