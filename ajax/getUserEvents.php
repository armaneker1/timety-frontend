<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();

$userId = null;
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

if (isset($_POST["userId"]))
    $userId = $_POST["userId"];

if (!empty($userId)) {

    if (!SessionUtil::isUser($userId)) {
        $res = new stdClass();
        $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
        $json_response = json_encode($res);
        echo $json_response;
        exit(1);
    }
    $events = array();
    try {
        $events_ = Neo4jUserUtil::getUserLikesEventsId($userId);
        foreach ($events_ as $evt) {
            if (!empty($evt) && is_string($evt)) {
                if (isset($events[$evt])) {
                    $obj = $events[$evt];
                } else {
                    $obj = new stdClass();
                }
                $obj->like = true;
                $events[$evt] = $obj;
            }
        }
        unset($events_);
        unset($evt);
    } catch (Exception $exc) {
        error_log($exc->getTraceAsString());
    }


    try {
        $events_ = Neo4jUserUtil::getUserResharesEventsId($userId);
        foreach ($events_ as $evt) {
            if (!empty($evt) && is_string($evt)) {
                if (isset($events[$evt])) {
                    $obj = $events[$evt];
                } else {
                    $obj = new stdClass();
                }
                $obj->reshare = true;
                $events[$evt] = $obj;
            }
        }
        unset($events_);
        unset($evt);
    } catch (Exception $exc) {
        error_log($exc->getTraceAsString());
    }


    try {
        $events_ = Neo4jUserUtil::getUserJoinsEventsId($userId, TYPE_JOIN_YES);
        foreach ($events_ as $evt) {
            if (!empty($evt) && is_string($evt)) {
                if (isset($events[$evt])) {
                    $obj = $events[$evt];
                } else {
                    $obj = new stdClass();
                }
                $obj->joinType = 1;
                $events[$evt] = $obj;
            }
        }
        unset($events_);
        unset($evt);
    } catch (Exception $exc) {
        error_log($exc->getTraceAsString());
    }

    try {
        $events_ = Neo4jUserUtil::getUserJoinsEventsId($userId, TYPE_JOIN_MAYBE);
        foreach ($events_ as $evt) {
            if (!empty($evt) && is_string($evt)) {
                if (isset($events[$evt])) {
                    $obj = $events[$evt];
                } else {
                    $obj = new stdClass();
                }
                $obj->joinType = 2;
                $events[$evt] = $obj;
            }
        }
        unset($events_);
        unset($evt);
    } catch (Exception $exc) {
        error_log($exc->getTraceAsString());
    }

    $result = new Result();
    $result->error = false;
    $result->success = true;
    $result->param = $events;
    echo json_encode($result);
    exit(1);
} else {
    $res = new stdClass();
    $res->error = LanguageUtils::getText("LANG_AJAX_USER_NOT_FOUND");
    $json_response = json_encode($res);
    echo $json_response;
    exit(1);
}
?>
