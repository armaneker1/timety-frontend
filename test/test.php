<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';

$redis = new Predis\Client();

$ab = $redis->keys("user:friend*");

var_dump($redis->zrange("deneme:1:1",0,-1));
$redis->getProfile()->defineCommand('removeItemByIdReturnItem', 'RemoveItemByIdReturnItem');
$it = $redis->removeItemByIdReturnItem("deneme:1:1", 12);
var_dump($it);
var_dump($redis->zrange("deneme:1:1",0,-1));

?>