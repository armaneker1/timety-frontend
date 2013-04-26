<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$redis = new Predis\Client();

$array = $redis->zrange(REDIS_PREFIX_USER . "6618344" . REDIS_SUFFIX_FRIEND_FOLLOWING, 0, -1);
foreach ($array as $arr) {
    $ev = json_decode($arr);
    $ev = UtilFunctions::cast('Event', $ev);
    var_dump($ev->id);
    var_dump($ev->title);
    var_dump($ev->userEventLog);
}
?>