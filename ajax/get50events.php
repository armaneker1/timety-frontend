<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$city_id = null;
if (isset($_GET["city"])) {
    $city_id = $_GET["city"];
}
if (isset($_POST["city"])) {
    $city_id = $_POST["city"];
}


$date = time();
$enddate = strtotime("+7 day");
$pageNumber = 0;
$pageItemCount = 50;


$redis = new Predis\Client();
$pgStart = $pageNumber * $pageItemCount;
$pgEnd = $pgStart + $pageItemCount - 1;
$key = "";
if ($city_id == -1) {
    $key = REDIS_PREFIX_CITY . "ww";
} else if ($city_id == -2) {
    $key = REDIS_PREFIX_CITY . "ww";
} else {
    $key = REDIS_PREFIX_CITY . $city_id;
}
$events = array();
$events = $redis->zrangebyscore($key, $date, $enddate);

$result = "[";
$ik = 0;
for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
    if ($ik <= $pgEnd) {
        try {
            $r = ",";
            if (strlen($result) < 2) {
                $r = "";
            }
            if ($ik >= $pgStart) {
                $result = $result . $r . $events[$i];
            }
            $ik++;
        } catch (Exception $exc) {
            var_dump("RedisUtils > getCategoryEvents > $i Error : " . $exc->getTraceAsString());
        }
    } else {
        break;
    }
}
$result = $result . "]";

echo $result;
?>
