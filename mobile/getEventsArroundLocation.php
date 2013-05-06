<?php

use ElasticSearch\Client;

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();

//lat
$lat = null;
if (isset($_POST['lat'])) {
    $lat = $_POST['lat'];
}
if (isset($_GET['lat'])) {
    $lat = $_GET['lat'];
}

//lng
$lng = null;
if (isset($_POST['lng'])) {
    $lng = $_POST['lng'];
}
if (isset($_GET['lng'])) {
    $lng = $_GET['lng'];
}

//radius
$radius = 5;
if (isset($_POST['radius'])) {
    $radius = $_POST['radius'];
}
if (isset($_GET['radius'])) {
    $radius = $_GET['radius'];
}

//userId
$userId = null;
if (isset($_POST["uid"]))
    $userId = $_POST["uid"];
if (isset($_GET["uid"]))
    $userId = $_GET["uid"];

//date 
$date = date(DATETIME_DB_FORMAT);
if (isset($_POST['date'])) {
    $date = $_POST['date'];
}
if (isset($_GET["date"])) {
    $date = $_GET["date"];
}



if (!empty($lat) && !empty($lng) && !empty($radius)) {
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
    $lat = doubleval($lat);
    $lng = doubleval($lng);
    $radius = $radius . "km";
    $es = Client::connection(array(
                'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                'index' => ELASTICSEACRH_TIMETY_INDEX,
                'type' => ELASTICSEACRH_TIMETY_DOCUMENT_EVENT
            ));
    $QUERY = array(
        'query' => array(
            'filtered' => array(
                'filter' => array(
                    'and' => array(
                        0 => array('geo_distance' => array(
                                'distance' => $radius,
                                ELASTICSEACRH_TIMETY_DOCUMENT_EVENT . '.location' => array(
                                    'lat' => $lat,
                                    'lon' => $lng
                                )
                        )),
                        1 => array('range' => array(
                                'startDateTimeLong' => array(
                                    'from' => $date
                                )
                        ))
                    )
                )
            )
        )
    );
    $res = $es->search($QUERY);
    if (empty($res) || isset($res['error'])) {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 101;
        $r->error = "Error : " . $res['error'];
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else if (!empty($res) && isset($res['hits'])) {
        $hits_array = $res['hits'];
        if (!empty($hits_array)) {
            if (isset($hits_array['hits'])) {
                $hits = $hits_array['hits'];
                if (!empty($hits)) {
                    $events = array();
                    foreach ($hits as $hit) {
                        $hit = $hit['_source'];
                        array_push($events, $hit);
                    }
                    $r = new stdClass();
                    $r->success = 0;
                    $r->code = 100;
                    $r->data = new stdClass();
                    $r->data->events = $events;
                    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                    echo $result;
                    exit(1);
                }
            }
        }
    }
    $r = new stdClass();
    $r->success = 0;
    $r->code = 100;
    $r->data = new stdClass();
    $r->data->events = array();
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 101;
    $r->error = "Location is empty";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
