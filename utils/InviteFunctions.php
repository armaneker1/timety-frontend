<?php

class InviteUtil {

    function getGropInvitesByUserId($userId) {
        $n = new Neo4jFuctions();
        return $n->getGropInvitesByUserId($userId);
    }

    function getEventInvitesByUserId($userId) {
        $n = new Neo4jFuctions();
        return $n->getEventInvitesByUserId($userId);
    }

    function responseToGroupInvites($userId, $groupId, $resp) {
        $n = new Neo4jFuctions();
        return $n->responseToGroupInvites($userId, $groupId, $resp);
    }

    function responseToEventInvites($userId, $eventId, $resp) {
        $n = new Neo4jFuctions();
        return $n->responseToEventInvites($userId, $eventId, $resp);
    }

}

?>
