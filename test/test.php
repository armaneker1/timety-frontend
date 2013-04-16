<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$array = array();
for ($i = 0; $i < 100; $i++)
    array_push($array, $i);

var_dump(sizeof($array));
for ($i = sizeof($array) - 1; $i >= 0; $i--) {
    var_dump($i);
    $rel = $array[$i];
    if ($rel == 55) {
        var_dump("removed");
        unset($array[$i]);
    }
}

var_dump(sizeof($array));
?>