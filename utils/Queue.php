<?php

require_once __DIR__ . '/../apis/Stomp/Stomp.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';
require_once __DIR__ . '/SettingFunctions.php';

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

    public static function addEvent($eventId, $userId) {
        self::send("event", "addEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => REDIS_USER_INTERACTION_CREATED,
            "time" => time()
        ));
    }

    public static function addEventForOthers($eventId, $userId) {
        self::send("event", "addEventForOthers", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => REDIS_USER_INTERACTION_CREATED_FOR_OTHER,
            "time" => time()
        ));
    }

    public static function updateEvent($eventId, $userId) {
        self::send("event", "updateEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => REDIS_USER_INTERACTION_UPDATED,
            "time" => time()
        ));
    }
    
    public static function addEventToFollowers($eventId, $userId, $type){
        self::send("event", "addEventToFollowers", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
        ));
    }

    public static function likeEvent($eventId, $userId, $type) {
        self::send("event", "likeEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
        ));
    }
    
    public static function reshareEvent($eventId, $userId, $type) {
        self::send("event", "reshareEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
        ));
    }

    public static function socialInteraction($eventId, $userId, $type) {
        self::send("event", "updateEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
        ));
    }

    public static function followUser($fromUserId, $toUserId) {
        self::send("event", "followUser", array(
            "userID" => $fromUserId,
            "followID" => $toUserId,
            "type" => REDIS_USER_INTERACTION_FOLLOW,
            "time" => time()
        ));
    }

    public static function unFollowUser($fromUserId, $toUserId) {
        self::send("event", "unFollowUser", array(
            "userID" => $fromUserId,
            "followID" => $toUserId,
            "type" => REDIS_USER_INTERACTION_UNFOLLOW,
            "time" => time()
        ));
    }

    public static function updateProfile($userId) {
        self::send("user", "updateUser", array(
            "userID" => $userId,
            "type" => REDIS_USER_UPDATE,
            "time" => time()
        ));
    }

    public static function addCategory($categoryID) {
        self::send("category", "addCategory", array(
            "categoryID" => $categoryID,
            "time" => time()
        ));
    }

    //--------------------------------------------------------------------------

    private static function send($method, $action, $obj) {
        if (SERVER_PROD) {
            $obj["method"] = $method;
            $obj["action"] = $action;
            $conn = self::getConnection();
            $conn->send("timety", json_encode($obj), array('persistent' => 'true'));
            $conn->disconnect();
        }
    }

    private static function getConnection() {
        try {
            $conn = new Stomp("tcp://" . MQ_IP . ":" . MQ_PORT);
            $conn->connect();
            return $conn;
        } catch (StompException $e) {
            echo "Connection Error: " . $e->getDetails();
        }
        return null;
    }

}

?>
