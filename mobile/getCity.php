<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
session_write_close();
HttpAuthUtils::checkMobileHttpAuth();

//cityName
$cityName = null;
if (isset($_POST['cityName'])) {
    $cityName = $_POST['cityName'];
}
if (isset($_GET['cityName'])) {
    $cityName = $_GET['cityName'];
}

//cityId
$cityId = null;
if (isset($_POST['cityId'])) {
    $cityId = $_POST['cityId'];
}
if (isset($_GET['cityId'])) {
    $cityId = $_GET['cityId'];
}


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

if (!empty($cityName)) {
    $cityId = LocationUtils::getCityId($cityName);
    if (!empty($cityId)) {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->cityId = $cityId;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "City not found";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else if (!empty($cityId)) {
    $cityName = LocationUtils::getCityName($cityId);
    if (!empty($cityName)) {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->cityName = $cityName;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "City not found";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else if (!empty($lat) && !empty($lng)) {
    $result = LocationUtils::getCityCountry($lat, $lng);
    if (!empty($result)) {
        $cityName = $result['city'];
        $cityId = LocationUtils::getCityId($cityName);
        $countryName = $result['country'];
        $r = new stdClass();
        $r->success = 0;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->cityName = $cityName;
        $r->data->cityId = $cityId;
        $r->data->countryName = $countryName;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "City not found";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "Parameters wrong";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
