<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
$msgs = array();

if (isset($_GET)) {

    if (isset($_GET['userId']) && !empty($_GET['userId'])) {
        if (!SessionUtil::isUser($_GET['userId'])) {
            $res = new stdClass();
            $res->error = "user not logged in";
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
    }

    $error = false;
    $event = new Event();

    /*
     * Upload Image
     */
    $rand = rand(10000, 9999999);
    $dest_url = __DIR__ . '/../uploads/' . $rand . "_logo_fb.jpeg";
    copy(__DIR__ . '/../images/nopic.png', $dest_url);
    $event->headerImage = $rand . "_logo_fb.jpeg";
    /*
     */
    $event->title = $_GET["event_description"];
    if (empty($event->title)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Title can not be empty";
        array_push($msgs, $m);
    }
    $event->description = $_GET["event_description"];
    if (empty($event->description)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Description can not be empty";
        array_push($msgs, $m);
    }
    $event->location = $_GET["event_loc"];
    if (!isset($_GET['userId']) && empty($_GET['userId'])) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "User not found";
        array_push($msgs, $m);
    }
    $event->creatorId = $_GET['userId'];
    $event->attendance = $_GET['event_peoples_list'];
    $loc = $_GET["event_cor"];
    if (!empty($loc)) {
        $arr = explode(",", $loc);
        if (!empty($arr) && sizeof($arr) == 2) {
            $event->loc_lat = $arr[0];
            $event->loc_lng = $arr[1];
        }
    }

    $startDate = $_GET["event_start_date"];
    $startTime = $_GET["event_start_time"];
    $startDate = UtilFunctions::checkDate($startDate);
    if (!$startDate) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Start Date not valid";
        array_push($msgs, $m);
    }
    $startTime = UtilFunctions::checkTime($startTime);
    if (!$startTime) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Start Time not valid";
        array_push($msgs, $m);
    }

    /*
     * empty datas
     */
    $event->privacy = "false";
    $endDate = $startDate;
    $endTime = $startTime;

    $event->startDateTime = $startDate . " " . $startTime . ":00";
    $event->endDateTime = $endDate . " " . $endTime . ":00";
    $timezone = "+02:00";
    if (isset($_GET['te_timezone'])) {
        $timezone = $_GET['te_timezone'];
    }
    $event->startDateTime = UtilFunctions::convertTimeZone($event->startDateTime, $timezone);
    $event->endDateTime = UtilFunctions::convertTimeZone($event->endDateTime, $timezone);
    $event->allday = 0;
    $event->repeat = 0;
    $event->addsocial_fb = 0;
    $event->addsocial_gg = 0;
    $event->addsocial_tw = 0;
    $event->addsocial_fq = 0;
    $event->reminderType = "";
    $event->reminderUnit = "";
    $event->reminderValue = 0;
    $event->categories = "";
    $event->attach_link = "";
    $event->tags = "";


    if (!$error) {
        try {
            $eventDB = EventUtil::createEvent($event, UserUtils::getUserById($_GET['userId']));
            if (!empty($eventDB) && !empty($eventDB->id)) {
                Queue::addEvent($eventDB->id, $_GET['userId']);
            }
            $result = new Result();
            $result->success = true;
            $result->error = false;
            $result->param = "Success";
            echo json_encode($result);
        } catch (Exception $e) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = $e->getMessage();
            array_push($msgs, $m);
            $result->success = false;
            $result->error = true;
            $result->param = $msgs;
            echo json_encode($result);
        }
    } else {
        $result = new Result();
        $result->success = false;
        $result->error = true;
        $result->param = $msgs;
        echo json_encode($result);
    }
}
?>
