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
    /*
     * high priority
     */

    public static function addEvent($eventId, $userId) {
        self::send("event", "addEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => REDIS_USER_INTERACTION_CREATED,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    public static function updateEvent($eventId, $userId) {
        self::send("event", "updateEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => REDIS_USER_INTERACTION_UPDATED,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    public static function likeEvent($eventId, $userId, $type,$redisQueueExtraParam=false) {
        self::send("event", "likeEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "extra" => $redisQueueExtraParam,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    public static function reshareEvent($eventId, $userId, $type, $redisQueueExtraParam = false) {
        self::send("event", "reshareEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "extra" => $redisQueueExtraParam,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    public static function joinEvent($eventId, $userId, $type, $redisQueueExtraParam = false) {
        self::send("event", "joinEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "extra" => $redisQueueExtraParam,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    public static function updateEventInfo($eventId, $userId, $type) {
        self::send("event", "updateEventInfo", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    public static function updateProfile($userId) {
        self::send("user", "updateUser", array(
            "userID" => $userId,
            "type" => REDIS_USER_UPDATE,
            "time" => time()
                ), QUEUE_PRIORITY_HIGH);
    }

    /*
     * low priority
     */

    public static function addEventForOthers($eventId, $userId) {
        self::send("event", "addEventForOthers", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => REDIS_USER_INTERACTION_CREATED_FOR_OTHER,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    public static function updateEventForOthers($eventId, $userId, $type) {
        self::send("event", "updateEventForOthers", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    public static function updateEventInfoForOthers($eventId, $userId, $type) {
        self::send("event", "updateEventInfoForOthers", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    public static function findInterestedPeopleForEvent($eventId, $userId, $type) {
        self::send("event", "findInterestedPeopleForEvent", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    public static function addEventToFollowers($eventId, $userId, $type) {
        self::send("event", "addEventToFollowers", array(
            "eventID" => $eventId,
            "userID" => $userId,
            "type" => $type,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    public static function followUser($fromUserId, $toUserId) {
        self::send("event", "followUser", array(
            "userID" => $fromUserId,
            "followID" => $toUserId,
            "type" => REDIS_USER_INTERACTION_FOLLOW,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    public static function unFollowUser($fromUserId, $toUserId) {
        self::send("event", "unFollowUser", array(
            "userID" => $fromUserId,
            "followID" => $toUserId,
            "type" => REDIS_USER_INTERACTION_UNFOLLOW,
            "time" => time()
                ), QUEUE_PRIORITY_LOW);
    }

    //--------------------------------------------------------------------------

    private static function send($method, $action, $obj, $priority = QUEUE_PRIORITY_LOW) {
        $queue = self::getQueue($priority);
        if (SERVER_PROD) {
            $obj["method"] = $method;
            $obj["action"] = $action;
            $conn = self::getConnection();
            $conn->send($queue, json_encode($obj), array('persistent' => 'true'));
            $conn->disconnect();
        }
    }

    private static function getQueue($priority = QUEUE_PRIORITY_LOW) {
        $queue = "timety";
        if ($priority != QUEUE_PRIORITY_LOW && $priority != QUEUE_PRIORITY_NORMAL && $priority != QUEUE_PRIORITY_HIGH) {
            $priority = QUEUE_PRIORITY_LOW;
        }
        $queue = $queue . "." . $priority;
        return $queue;
    }

    private static function getConnection() {
        try {
            $conn = new Stomp("tcp://" . MQ_IP . ":" . MQ_PORT);
            $conn->connect();
            return $conn;
        } catch (StompException $e) {
            error_log("Connection Error: " . $e->getTraceAsString());
        }
        return null;
    }

}

?>
