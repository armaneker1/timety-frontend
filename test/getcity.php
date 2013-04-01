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
HttpAuthUtils::checkHttpAuth();
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


$res = LocationUtils::getCityCountry($lat, $lng);
var_dump($res);

/*
  //country
  if(results[0]){
  if(results[0].address_components.length>0){
  for(var i = 0;
  i<results[0].address_components.length;
  i++){
  var obj = results[0].address_components[i];
  if(obj && obj.types && obj.types.length>0){
  if(jQuery.inArray("country", obj.types)>=0){
  te_loc_country = obj.short_name;
  break;
  }
  }
  }
  }
  }
  jQuery("#te_location_country").val(te_loc_country);

  //city
  if(results[0]){
  if(results[0].address_components.length>0){
  for(var i = 0;
  i<results[0].address_components.length;
  i++){
  var obj = results[0].address_components[i];
  if(obj && obj.types && obj.types.length>0){
  if(jQuery.inArray("administrative_area_level_1", obj.types)>=0){
  te_loc_city = obj.long_name;
  break;
  }
  }
  }
  }
  }
 */
?>
