<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();


$eventId = null;
if (isset($_POST["eventId"]))
    $eventId = $_POST["eventId"];
if (isset($_GET["eventId"]))
    $eventId = $_GET["eventId"];


$res = new Result();
$res->error = true;
$res->success = false;

if (!empty($eventId)) {
    $result = Neo4jEventUtils::getEventFromNode($eventId, TRUE);
    if (!empty($result)) {
        $result->getHeaderImage();
        $result->images = array();
        $result->getAttachLink();
        $result->getTags();
        $result->getLocCity();
        $result->getWorldWide();
        $result->attendancecount = Neo4jEventUtils::getEventAttendanceCount($eventId);
        $result->commentCount = CommentUtil::getCommentListSizeByEvent($eventId, null);


        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->event = $result;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "Event is not found";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "Event Id is empty";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
