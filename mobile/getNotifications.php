<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();


$userId = null;
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];


$limit = 10;
if (isset($_POST["limit"]))
    $limit = $_POST["limit"];
if (isset($_GET["limit"]))
    $limit = $_GET["limit"];

if (!empty($userId)) {
    $user = UserUtils::getUserById($userId);
    if (!empty($user)) {
        $result = $user->getUserNotifications(true);
        if (empty($result) || (!empty($result) && sizeof($result) < $limit)) {
            if (!empty($result)) {
                $limit = $limit - sizeof($result);
            }
            $res = NotificationUtils::getReadNotificationList($userId, $limit);
            if (empty($result)) {
                $result = $res;
            } else {
                if (!empty($res)) {
                    foreach ($res as $r) {
                        array_push($result, $r);
                    }

                    usort($result, function($a, $b) {
                                return $b->getId() - $a->getId();
                            });
                }
            }
        }

        if (!empty($result)) {
            $array = array();
            $res = new TimeteNotification();
            foreach ($result as $res) {
                if ($res->getType() == NOTIFICATION_TYPE_COMMENT ||
                        $res->getType() == NOTIFICATION_TYPE_SHARED ||
                        $res->getType() == NOTIFICATION_TYPE_MAYBE ||
                        $res->getType() == NOTIFICATION_TYPE_JOIN ||
                        $res->getType() == NOTIFICATION_TYPE_LIKED) {
                    $usr = UserUtils::getUserById($res->getNotUserId());
                    if (!empty($usr)) {
                        $event = EventUtil::getEventById($res->getNotEventId());
                        if (!empty($event)) {
                            $res->notUserName = $usr->userName;
                            $res->notUserFullName = $usr->getFullName();
                            $res->notEventTitle = $event->title;
                            array_push($array, $res);
                        }
                    }
                } else if ($res->getType() == NOTIFICATION_TYPE_FOLLOWED) {
                    $usr = UserUtils::getUserById($res->getNotUserId());
                    if (!empty($usr)) {
                        $res->notUserName = $usr->userName;
                        $res->notUserFullName = $usr->getFullName();
                        $res->notEventTitle = null;
                        array_push($array, $res);
                    }
                } else if ($res->getType() == NOTIFICATION_TYPE_INVITE) {
                    $usr = UserUtils::getUserById($res->getNotUserId());
                    if (!empty($usr)) {
                        $event = EventUtil::getEventById($res->getNotEventId());
                        if (!empty($event)) {
                            $res->notUserName = $usr->userName;
                            $res->notUserFullName = $usr->getFullName();
                            $res->notEventTitle = $event->title;
                            $tmp = Neo4jEventUtils::getUserEventJoinRelation($res->getUserId(), $res->getNotEventId());
                            if (!empty($tmp)) {
                                $tmp = $tmp->getProperty(PROP_JOIN_TYPE);
                                $res->setRead(1);
                            } else {
                                $res->setRead(0);
                                $tmp = null;
                            }
                            $res->notEventResponse = $tmp;
                            array_push($array, $res);
                        }
                    }
                }
            }
            $r = new stdClass();
            $r->success = 1;
            $r->code = 100;
            $r->data = new stdClass();
            $r->data->Notifications = $array;
            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
            echo $result;
            exit(1);
        } else {
            $r = new stdClass();
            $r->success = 1;
            $r->code = 100;
            $r->data = "No Notf";
            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
            echo $result;
            exit(1);
        }
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "User not found";
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
