<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
session_write_close();
HttpAuthUtils::checkMobileHttpAuth();


//user_id
$uid = null;
if (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
}
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
}

//req user id
$reqUserId = null;
if (isset($_POST['reqUserId'])) {
    $reqUserId = $_POST['reqUserId'];
}
if (isset($_GET['reqUserId'])) {
    $reqUserId = $_GET['reqUserId'];
}

$pageNumber = null;
if (isset($_POST['pageNumber'])) {
    $pageNumber = $_POST['pageNumber'];
}
if (isset($_GET["pageNumber"])) {
    $pageNumber = $_GET["pageNumber"];
}


$pageItemCount = null;
if (isset($_POST['pageItemCount'])) {
    $pageItemCount = $_POST['pageItemCount'];
}
if (isset($_GET["pageItemCount"])) {
    $pageItemCount = $_GET["pageItemCount"];
}


$date = date(DATETIME_DB_FORMAT);
if (isset($_POST['date'])) {
    $date = $_POST['date'];
}
if (isset($_GET["date"])) {
    $date = $_GET["date"];
}


$dateCalc = false;
if (empty($date) || substr($date, 0, 1) == "0") {
    $dateCalc = true;
    $date = strtotime("now");
} else {
    $datestr = $date . ":00";
    $datestr = date_parse_from_format(DATETIME_DB_FORMAT, $datestr);
    if (checkdate($datestr['month'], $datestr['day'], $datestr['year'])) {
        $result = $datestr['year'] . "-";
        if (strlen($datestr['month']) == 1) {
            $result = $result . "0" . $datestr['month'] . "-";
        } else {
            $result = $result . $datestr['month'] . "-";
        }
        if (strlen($datestr['day']) == 1) {
            $result = $result . "0" . $datestr['day'];
        } else {
            $result = $result . $datestr['day'];
        }

        $result = $result . " ";
        if (strlen($datestr['hour']) == 1) {
            $result = $result . "0" . $datestr['hour'];
        } else {
            $result = $result . $datestr['hour'];
        }
        $result = $result . ":";
        if (strlen($datestr['minute']) == 1) {
            $result = $result . "0" . $datestr['minute'];
        } else {
            $result = $result . $datestr['minute'];
        }
        $result = $result . ":00";
        $date = $result;
    } else {
        $dateCalc = true;
        $date = date(DATE_FORMAT);
        $date = $date . " 00:00:00";
    }
    $date = strtotime($date);
}

if (!empty($uid)) {
    if ($pageNumber >= 0) {
        if ($pageItemCount <= 0) {
            $pageItemCount = 40;
        }
        $events = RedisUtils::getUserPublicEvents($uid, $pageNumber, $pageItemCount, $date, null, $reqUserId, null, $dateCalc);
        $events = json_decode($events);
        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->events = $events;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 106;
        $r->error = "page numner is wrong ";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "User Id is empty";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
