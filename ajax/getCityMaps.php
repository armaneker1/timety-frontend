<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$array=  LocationUtils::getCityMaps();

echo json_encode($array);

?>
