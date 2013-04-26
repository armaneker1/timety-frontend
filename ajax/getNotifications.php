<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_POST["userId"]))
    $query = $_POST["userId"];
if (isset($_GET["userId"]))
    $query = $_GET["userId"];


$limit = null;
if (isset($_POST["limit"]))
    $limit = $_POST["limit"];
if (isset($_GET["limit"]))
    $limit = $_GET["limit"];

try {
    $result = new Result();
    $result->error = true;

    if (!empty($query)) {
        if (!SessionUtil::isUser($query)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        $user = UserUtils::getUserById($query);
        if (!empty($user)) {
            $result = $user->getUserNotifications(true);
            if (empty($result) || (!empty($result) && sizeof($result) < $limit)) {
                if (!empty($result)) {
                    $limit = $limit - sizeof($result);
                }
                $res = NotificationUtils::getReadNotificationList($query, $limit);
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
                    $addItem = true;
                    $item = "<li id='li_notf_" . $res->getId() . "' notf_id='" . $res->getId() . "'>";
                    if ($res->getType() == NOTIFICATION_TYPE_COMMENT) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {
                            $event = EventUtil::getEventById($res->getNotEventId());
                            if (!empty($event)) {
                                $read = "";
                                if ($res->getRead() == 0) {
                                    $read = "display:none;";
                                }
                                $item =$item. LanguageUtils::getText("LANG_AJAX_NOTIFICATION_COMMENTED", $read, HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title);
                            } else {
                                $addItem = false;
                            }
                        } else {
                            $addItem = false;
                        }
                    } else if ($res->getType() == NOTIFICATION_TYPE_LIKED) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {
                            $event = EventUtil::getEventById($res->getNotEventId());
                            if (!empty($event)) {
                                $read = "";
                                if ($res->getRead() == 0) {
                                    $read = "display:none;";
                                }
                                $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_LIKED", $read, HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title);
                            } else {
                                $addItem = false;
                            }
                        } else {
                            $addItem = false;
                        }
                    } else if ($res->getType() == NOTIFICATION_TYPE_JOIN) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {
                            $event = EventUtil::getEventById($res->getNotEventId());
                            if (!empty($event)) {
                                $read = "";
                                if ($res->getRead() == 0) {
                                    $read = "display:none;";
                                }
                                $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_JOIN", $read, HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title);
                            } else {
                                $addItem = false;
                            }
                        } else {
                            $addItem = false;
                        }
                    } else if ($res->getType() == NOTIFICATION_TYPE_MAYBE) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {
                            $event = EventUtil::getEventById($res->getNotEventId());
                            if (!empty($event)) {
                                $read = "";
                                if ($res->getRead() == 0) {
                                    $read = "display:none;";
                                }
                                $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_MAYBE", $read, HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title);
                            } else {
                                $addItem = false;
                            }
                        } else {
                            $addItem = false;
                        }
                    } else if ($res->getType() == NOTIFICATION_TYPE_SHARED) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {
                            $event = EventUtil::getEventById($res->getNotEventId());
                            if (!empty($event)) {
                                $read = "";
                                if ($res->getRead() == 0) {
                                    $read = "display:none;";
                                }
                                $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_RESHARED", $read, HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title);
                            } else {
                                $addItem = false;
                            }
                        } else {
                            $addItem = false;
                        }
                    } else if ($res->getType() == NOTIFICATION_TYPE_FOLLOWED) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {

                            $read = "";
                            if ($res->getRead() == 0) {
                                $read = "display:none;";
                            }
                            $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_FOLLOWED", $read, HOSTNAME . $usr->userName, $usr->getFullName());
                        } else {
                            $addItem = false;
                        }
                    } else if ($res->getType() == NOTIFICATION_TYPE_INVITE) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        if (!empty($usr)) {
                            $event = EventUtil::getEventById($res->getNotEventId());
                            if (!empty($event)) {
                                if ($res->getRead() == 0) {
                                    $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_INVITE_NEW_1", HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title);
                                    $item = $item . LanguageUtils::getText("LANG_AJAX_NOTIFICATION_INVITE_NEW_2", $res->getId(), $res->getUserId(), $res->getNotEventId());
                                } else {
                                    $tmp = Neo4jEventUtils::getUserEventJoinRelation($res->getUserId(), $res->getNotEventId());
                                    if (!empty($tmp)) {
                                        $tmp = $tmp->getProperty(PROP_JOIN_TYPE);
                                        if ($tmp == 1) {
                                            $tmp = LanguageUtils::getText("LANG_PROFILE_BACTH_JOINED");
                                        } else if ($tmp == 2) {
                                            $tmp = LanguageUtils::getText("LANG_PROFILE_BACTH_MAYBE");
                                        } else if ($tmp == 3) {
                                            $tmp = LanguageUtils::getText("LANG_PROFILE_BACTH_IGNORED");
                                        }
                                    }
                                    $item = $item.LanguageUtils::getText("LANG_AJAX_NOTIFICATION_INVITE_OLD", HOSTNAME . $usr->userName, $usr->getFullName(), HOSTNAME . "event/" . $res->getNotEventId(), $event->title, $tmp);
                                }
                            } else {
                                $addItem = false;
                            }
                        } else {
                            $addItem = false;
                        }
                    }
                    $item = $item . "</div></li>";
                    if ($addItem) {
                        array_push($array, $item);
                    }
                }
                $result = $array;
            }
        }
    }
} catch (Exception $e) {
    $result = new Result();
    $result->error = $e->getTraceAsString();
}
$json_response = json_encode($result);
echo $json_response;
?>
