<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();


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


$date = date(DATETIME_DB_FORMAT);
if (isset($_POST['date'])) {
    $date = $_POST['date'];
}
if (isset($_GET["date"])) {
    $date = $_GET["date"];
}

$city = LocationUtils::getCityIdNotAdd($city);



$categoryId = null;
if (isset($_POST['categoryId'])) {
    $categoryId = $_POST['categoryId'];
}
if (isset($_GET["categoryId"])) {
    $categoryId = $_GET["categoryId"];
}

$tagId = null;
if (isset($_POST['tagId'])) {
    $tagId = $_POST['tagId'];
}
if (isset($_GET["tagId"])) {
    $tagId = $_GET["tagId"];
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
        $events = RedisUtils::getCategoryEvents($uid, $pageNumber, $pageItemCount, $date, null, $categoryId, $city, $tagId);
        $events = json_decode($events);
        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->events = $events;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
        var_dump($recommended);
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