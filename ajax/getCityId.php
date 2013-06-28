<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$cityName = null;
if (isset($_POST["cityName"]))
    $cityName = $_POST["cityName"];
if (isset($_GET["cityName"]))
    $cityName = $_GET["cityName"];

$city_id = LocationUtils::getCityId($cityName);
$res = new Result();

if (!empty($city_id)) {
    $res->error = false;
    $res->success = true;
    $res->param = $city_id;
} else {
    $res->error = true;
    $res->success = false;
}
echo json_encode($res);
?>
