<?php

class InviteUtil {

    public static function getGropInvitesByUserId($userId) {
        $n = new Neo4jFuctions();
        return $n->getGropInvitesByUserId($userId);
    }

    public static function getEventInvitesByUserId($userId) {
        $n = new Neo4jFuctions();
        return $n->getEventInvitesByUserId($userId);
    }

    public static function responseToGroupInvites($userId, $groupId, $resp) {
        $n = new Neo4jFuctions();
        return $n->responseToGroupInvites($userId, $groupId, $resp);
    }

    public static function responseToEventInvites($userId, $eventId, $resp) {
        $n = new Neo4jFuctions();
        return $n->responseToEventInvites($userId, $eventId, $resp);
    }
    
}

?>
