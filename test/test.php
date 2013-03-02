<?php
require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../utils/SettingFunctions.php';

$redis = new Predis\Client();
$keys=$redis->keys("*");
foreach ($keys as $key){
    
}
?>