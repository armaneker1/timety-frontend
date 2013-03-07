<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

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
                    $item = "<li id='li_notf_" . $res->getId() . "' notf_id='" . $res->getId() . "'>";
                    if ($res->getType() == NOTIFICATION_TYPE_COMMENT) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $event = EventUtil::getEventById($res->getNotEventId());
                        $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                        if($res->getRead()==0){
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        }
                        $item = $item . "<img src='".HOSTNAME."images/comment.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                        $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;commented on&nbsp;</span>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                    } else if ($res->getType() == NOTIFICATION_TYPE_LIKED) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $event = EventUtil::getEventById($res->getNotEventId());
                        $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                        if($res->getRead()==0){
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        }
                        $item = $item . "<img src='".HOSTNAME."images/plus.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                        $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;liked&nbsp;</span>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                    } else if ($res->getType() == NOTIFICATION_TYPE_JOIN) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $event = EventUtil::getEventById($res->getNotEventId());
                        $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                        if($res->getRead()==0){
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        }
                        $item = $item . "<img src='".HOSTNAME."images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                        $item = $item . "<span style='color:#C2C2C2;float:left;'>&nbsp;joined&nbsp;</span>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                    } else if ($res->getType() == NOTIFICATION_TYPE_MAYBE) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $event = EventUtil::getEventById($res->getNotEventId());
                        $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                        if($res->getRead()==0){
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        }
                        $item = $item . "<img src='".HOSTNAME."images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                        $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;might join&nbsp;</span>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                    } else if ($res->getType() == NOTIFICATION_TYPE_SHARED) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $event = EventUtil::getEventById($res->getNotEventId());
                        $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                        if($res->getRead()==0){
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        }
                        $item = $item . "<img src='".HOSTNAME."images/plus.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                        $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;reshared&nbsp;</span>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                    } else if ($res->getType() == NOTIFICATION_TYPE_FOLLOWED) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                        if($res->getRead()==0){
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        }
                        $item = $item . "<img src='".HOSTNAME."images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                        $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                        $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;started follow you &nbsp;</span>";
                    } else if ($res->getType() == NOTIFICATION_TYPE_INVITE) {
                        $usr = UserUtils::getUserById($res->getNotUserId());
                        $event = EventUtil::getEventById($res->getNotEventId());
                        if ($res->getRead() == 0) {
                            $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                            $item = $item . "<img class='new_not' src='".HOSTNAME."images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                            $item = $item . "<img src='".HOSTNAME."images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                            $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                            $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;invited you to join &nbsp;</span>";
                            $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                            $item = $item . "<br class='notf_answer_class'/><a class='notf_answer_class' style='color:#C2C2C2;float:left;cursor:pointer' onclick='return responseEvent(" . $res->getId() . "," . $res->getUserId() . "," . $res->getNotEventId() . ",1);'>Join |&nbsp;</a>";
                            $item = $item . "<a class='notf_answer_class' style='color:#C2C2C2;float:left;cursor:pointer' onclick='return responseEvent(" . $res->getId() . "," . $res->getUserId() . "," . $res->getNotEventId() . ",2);'>Maybe |&nbsp;</a>";
                            $item = $item . "<a class='notf_answer_class' style='color:#C2C2C2;left:right;cursor:pointer' onclick='return responseEvent(" . $res->getId() . "," . $res->getUserId() . "," . $res->getNotEventId() . ",3);'>Ignore &nbsp;</a>";
                        } else {
                            $tmp = Neo4jEventUtils::getUserEventJoinRelation($res->getUserId(), $res->getNotEventId());
                            if (!empty($tmp)) {
                                $tmp = $tmp->getProperty(PROP_JOIN_TYPE);
                                if ($tmp == 1) {
                                    $tmp = "Joined";
                                } else if ($tmp == 2) {
                                    $tmp = "Maybe";
                                } else if ($tmp == 3) {
                                    $tmp = "Ignored";
                                }
                            }
                            $item = $item . "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'>";
                            $item = $item . "<img src='".HOSTNAME."images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'>";
                            $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;'>" . $usr->getFullName() . "</a>&nbsp;";
                            $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;invited you to join &nbsp;</span>";
                            $item = $item . "<a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"" . HOSTNAME . "event/" . $res->getNotEventId() . "\"'>'" . $event->title . "'</a>";
                            if (!empty($tmp)) {
                                $item = $item . "<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;(" . $tmp . ")&nbsp;</span>";
                            }
                        }
                    }
                    $item = $item . "</div></li>";
                    array_push($array, $item);
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
