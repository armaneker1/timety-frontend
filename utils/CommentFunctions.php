<?php

class CommentUtil {

    public static function getCommentById($commentId) {
        if (!empty($commentId)) {
            $commentId = DBUtils::mysql_escape($commentId);
            $SQL = "SELECT * FROM " . TBL_COMMENT . " WHERE id = $commentId";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $comment = new Comment();
                $comment->createFromSQL($result);
                return $comment;
            }
        } else {
            return null;
        }
    }

    public static function getCommentListByEvent($eventId, $lastCommentId, $count) {
        if (!empty($eventId)) {
            $eventId = DBUtils::mysql_escape($eventId);
            $lastCommentId = DBUtils::mysql_escape($lastCommentId);
            $count = DBUtils::mysql_escape($count);

            $SQL = "SELECT * from " . TBL_COMMENT . " WHERE event_id=$eventId";
            if (!empty($lastCommentId) && $lastCommentId != "*") {
                $SQL = $SQL . " AND  id<" . $lastCommentId . " ";
            }
            $SQL = $SQL . " ORDER BY datetime DESC ";
            if (!empty($count)) {
                $SQL = $SQL . " LIMIT 0," . $count;
            }
            //echo "<p/>".$SQL."<p/>";
            $query = mysql_query($SQL);
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $comment = new Comment();
                        $comment->createFromSQL($db_field);
                        array_push($array, $comment);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $comment = new Comment();
                    $comment->createFromSQL($db_field);
                    array_push($array, $comment);
                }
                return $array;
            }
        } else {
            return null;
        }
    }

    public static function getCommentListSizeByEvent($eventId, $lastCommentId) {
        if (!empty($eventId)) {
            $eventId = DBUtils::mysql_escape($eventId);
            $SQL = "SELECT count(id) as count_comment from " . TBL_COMMENT . " WHERE event_id=$eventId ";
            if (isset($lastCommentId) && !empty($lastCommentId) && $lastCommentId != "*") {
                $SQL = $SQL . " AND  id<" . $lastCommentId . " ";
            }
            //echo "<p/>".$SQL."<p/>";
            $query = mysql_query($SQL);
            if (!empty($query)) {
                mysql_num_rows($query);
                $db_field = mysql_fetch_assoc($query);
                if (!empty($db_field)) {
                    $count = $db_field['count_comment'];
                    if (!empty($count)) {
                        return $count;
                    }
                }
            }
        }
        return 0;
    }

    public static function insert(Comment $comment) {
        if (!empty($comment)) {
            $id = DBUtils::getNextId(CLM_COMMENTID);
            $SQL = "INSERT INTO " . TBL_COMMENT . " (id,user_id,datetime,event_id,comment) VALUES  " .
                    "(" . DBUtils::mysql_escape($id, 1) .
                    "," . DBUtils::mysql_escape($comment->userId, 1) .
                    ",'" . DBUtils::mysql_escape($comment->datetime, 1) .
                    "'," . DBUtils::mysql_escape($comment->eventId, 1) .
                    ",'" . DBUtils::mysql_escape($comment->comment) . "')";
            mysql_query($SQL);
            Neo4jEventUtils::increaseCommentCount($comment->eventId);
            $ev = new Event();
            $ev = EventUtil::getEventById($comment->eventId);
            NotificationUtils::insertNotification(NOTIFICATION_TYPE_COMMENT, $ev->creatorId, $comment->userId, $comment->eventId);
            Queue::updateEventInfo($comment->eventId,$comment->userId,REDIS_USER_INTERACTION_COMMENTTED);
            return CommentUtil::getCommentById($id);
        } else {
            return null;
        }
    }

}

?>
