<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';

$aaa="13123";
if(preg_match('/^[0-9]+$/',$aaa)){
    var_dump("ol");
}
?>