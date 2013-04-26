<?php 
session_start();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$array=  LocationUtils::getCityMaps();

echo json_encode($array);

?>
