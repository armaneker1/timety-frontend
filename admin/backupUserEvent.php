<?php
session_start();
session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$userId = null;
if (isset($_POST['userId']))
    $userId = $_POST['userId'];
if (isset($_GET['userId']))
    $userId = $_GET['userId'];

if (!empty($userId)) {
    $SQL = "SELECT * FROM `timete_events` WHERE `creator_id` = $userId";

    $result = mysql_query($SQL);
    $array = array();
    if (!empty($result)) {
        $num = mysql_num_rows($result);
        if ($num > 1) {
            while ($db_field = mysql_fetch_assoc($result)) {
                $event = new Event();
                $event->create($db_field);
                array_push($array, $event);
            }
        } else if ($num > 0) {
            $db_field = mysql_fetch_assoc($result);
            $event = new Event();
            $event->create($db_field);
            array_push($array, $event);
        }
    }

    $result = array();
    if (!empty($array)) {
        foreach ($array as $event) {
            $event->getHeaderImage();
            $event->getAttachLink();
            $event->getTags();
            $event->getLocCity();
            $event->getWorldWide();
            $event->hasVideo();
            $event->getHeaderVideo();
            $event->attendancecount = Neo4jEventUtils::getEventAttendanceCount($event->id);
            $event->commentCount = CommentUtil::getCommentListSizeByEvent($event->id, null);
            $event->likescount = Neo4jEventUtils::getEventLikesCount($event->id);
            $event->getCreatorType();
            array_push($result, $event);
        }
    }
    unset($array);
    echo json_encode($result);
    exit(1);
}
?>
<body>
    <form action="" method="POST">
        <input type="text" id="userId" name="userId" value="">
        <input type="submit" name="save" value="Delete">
    </form>
</body>
