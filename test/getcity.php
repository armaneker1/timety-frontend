<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

$lat = 40.72815749999999;
$lng = -74.07764170000002;

if (isset($_GET['lat'])) {
    $lat = $_GET['lat'];
}

if (isset($_GET['lng'])) {
    $lng = $_GET['lng'];
}

if (isset($_GET['coor'])) {
    $coor = $_GET['coor'];
    $coors = explode(',', $coor);
    $lat = $coors[0];
    $lng = $coors[1];
}


//LocationUtils::getCityCounrty($lat, $lng);

var_dump(LocationUtils::getCityId("Ankara"));
var_dump(LocationUtils::getCountryId("Türkiye"));
?>
