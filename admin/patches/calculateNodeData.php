<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
$array = Neo4jEventUtils::getAllEventsNode("");

foreach ($array as $evt) {
    $id = $evt->getProperty(PROP_EVENT_ID);
    if (!empty($evt) && ( $id == 0 || $id == "0" || !empty($id))) {
        $commentCount = CommentUtil::getCommentListSizeByEvent($id, null);
        $evt->setProperty(PROP_EVENT_COMMENT_COUNT, $commentCount);
        $evt->save();

        $attendancecount = Neo4jFuctions::getEventAttendanceCount($id);
        $evt->setProperty(PROP_EVENT_ATTENDANCE_COUNT, $attendancecount);
        $evt->save();


        $creatorId = Neo4jEventUtils::getEventCreatorId($id);
        //var_dump($creatorId);
        $user = UserUtils::getUserById($creatorId);
        //var_dump($user);
        if (!empty($user)) {
            $evt->setProperty(PROP_EVENT_CREATOR_ID, $user->id);
            $evt->setProperty(PROP_EVENT_CREATOR_F_NAME, $user->firstName);
            $evt->setProperty(PROP_EVENT_CREATOR_L_NAME, $user->lastName);
            $evt->setProperty(PROP_EVENT_CREATOR_USERNAME, $user->userName);
            $evt->setProperty(PROP_EVENT_CREATOR_IMAGE, $user->userPicture);
            $evt->setProperty(PROP_EVENT_CREATOR_BUSINESSUSER, $user->business_user);
            $evt->setProperty(PROP_EVENT_CREATOR_BUSINESSNAME, $user->business_name);
            $evt->setProperty(PROP_EVENT_CREATOR_DISPLAYNAME, $user->getFullName());
            $evt->save();
        }
    }
}
?>
