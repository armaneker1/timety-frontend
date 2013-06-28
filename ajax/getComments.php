<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$eventId = null;
if (isset($_POST["eventId"]))
    $eventId = $_POST["eventId"];

$lastCommentId = null;
if (isset($_POST["lastComment"]))
    $lastCommentId = $_POST["lastComment"];

$count = null;
if (isset($_POST["count"]))
    $count = $_POST["count"];

$res = new Result();
$res->error = true;
$res->success = false;

try {
    if (!empty($eventId)) {
        $array = CommentUtil::getCommentListByEvent($eventId, $lastCommentId, $count);
        if (!empty($array)) {
            $obj = new stdClass();
            $c = (int) CommentUtil::getCommentListSizeByEvent($eventId, $lastCommentId);
            //echo $c."pppp";
            $c = $c - sizeof($array);
            //echo $c."ppppp";
            $obj->count = $c;
            $obj->array = $array;
            $json_response = json_encode($obj);
            echo $json_response;
        } else {
            $json_response = json_encode($res);
            echo $json_response;
        }
    } else {
        $json_response = json_encode($res);
        echo $json_response;
    }
} catch (Exception $e) {
    $res->error = $e->getMessage();
    $json_response = json_encode($res);
    echo $json_response;
}
?>
