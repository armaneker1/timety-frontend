<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();


$eventList = EventUtil::getAllEvents();

foreach ($eventList as $event) {
    try {
        $id = $event->id;
        $evnt = Neo4jEventUtils::getEventNode($id);

        $commentCount = CommentUtil::getCommentListSizeByEvent($id, null);
        if (empty($commentCount)) {
            $commentCount = 0;
        }
        $evnt->setProperty(PROP_EVENT_COMMENT_COUNT, (int) $commentCount);


        $attendancecount = Neo4jEventUtils::getEventAttendanceCount($id);
        if (empty($attendancecount)) {
            $attendancecount = 0;
        }
        $evnt->setProperty(PROP_EVENT_ATTENDANCE_COUNT, (int) $attendancecount);


        $creator = Neo4jEventUtils::getEventCreator($id);
        if (!empty($creator)) {
            $evnt->setProperty(PROP_EVENT_CREATOR_ID, $creator->id);
            $evnt->setProperty(PROP_EVENT_CREATOR_F_NAME, $creator->firstName);
            $evnt->setProperty(PROP_EVENT_CREATOR_L_NAME, $creator->lastName);
            $evnt->setProperty(PROP_EVENT_CREATOR_USERNAME, $creator->userName);
            $evnt->setProperty(PROP_EVENT_CREATOR_IMAGE, $creator->userPicture);
            $evnt->save();
            Queue::updateEvent($id, $creator->id);
        }
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
}
?>
