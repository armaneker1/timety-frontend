<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
session_write_close();
HttpAuthUtils::checkMobileHttpAuth();
$userId = null;
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$eventId = null;
if (isset($_POST["eventId"]))
    $eventId = $_POST["eventId"];
if (isset($_GET["eventId"]))
    $eventId = $_GET["eventId"];

$type = null;
if (isset($_POST["type"]))
    $type = $_POST["type"];
if (isset($_GET["type"]))
    $type = $_GET["type"];



if (!empty($eventId) && !empty($userId)) {
    if (empty($type) || $type < 0 || $type > 5) {
        $type = 0;
    }
    $result = InviteUtil::responseToEventInvites($userId, $eventId, $type);
    if (empty($result) || $result->error || !$result->success) {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 101;
        $r->error = "Error ";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = "Success";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "Parameters missing";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
