<?php 
session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$array=  LocationUtils::getCityMaps();

echo json_encode($array);

?>
