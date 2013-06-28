<?php

$error_handling=true;
session_start();
session_write_close();
header("charset=utf8");

for($i=0;$i<100000000000;$i++){
    $a=$i*$i*$i;
}
require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

var_dump($_SESSION);
$_SESSION['sadsadsad']="asdasdasdsadasd";

?>