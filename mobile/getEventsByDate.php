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

$city = null;
if (isset($_POST['city'])) {
    $city = $_POST['city'];
}
if (isset($_GET["city"])) {
    $city = $_GET["city"];
}


$start_date = date(DATETIME_DB_FORMAT);
if (isset($_POST['start_date'])) {
    $start_date = $_POST['start_date'];
}
if (isset($_GET["start_date"])) {
    $start_date = $_GET["start_date"];
}

$end_date = date(DATETIME_DB_FORMAT);
if (isset($_POST['end_date'])) {
    $end_date = $_POST['end_date'];
}
if (isset($_GET["end_date"])) {
    $end_date = $_GET["end_date"];
}

$city = LocationUtils::getCityIdNotAdd($city);

if (empty($start_date) || substr($start_date, 0, 1) == "0") {
    $start_date = strtotime("now");
} else {
    $datestr = $start_date . ":00";
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
        $start_date = $result;
    } else {
        $start_date = date(DATE_FORMAT);
        $start_date = $start_date . " 00:00:00";
    }
    $start_date = strtotime($start_date);
}

if (empty($end_date) || substr($end_date, 0, 1) == "0") {
    $end_date = strtotime("now");
} else {
    $datestr = $end_date . ":00";
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
        $end_date = $result;
    } else {
        $end_date = date(DATE_FORMAT);
        $end_date = $end_date . " 00:00:00";
    }
    $end_date = strtotime($end_date);
}

if (!empty($uid)) {
    if ($pageNumber >= 0) {
        if ($pageItemCount <= 0) {
            $pageItemCount = 40;
        }
        $recommended = RedisUtils::getUpcomingEventsForUser($uid, $pageNumber, $pageItemCount, $start_date,$end_date, null, $city, null);
        $recommended = json_decode($recommended);
        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->events = $recommended;
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
