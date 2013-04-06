<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$date="2013-04-03 08:00:00";
$date=  strtotime($date);

//var_dump($date);
//var_dump(date(DATETIME_DB_FORMAT,$date));


//$now = getdate();
$dateEnd = mktime(0, 0, 0, 4, 4, 2013);


$redis = new Predis\Client();
$events = $redis->zrangebyscore(REDIS_PREFIX_USER . 6618414 . REDIS_SUFFIX_MY_TIMETY, $date, $dateEnd);

var_dump($events);
?>
