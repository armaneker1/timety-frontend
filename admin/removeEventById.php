<?php

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
if (isset($_GET['eventId']) && !empty($_GET['eventId'])) {
    $id = $_GET['eventId'];

    //delete fromneo4j
    try {
        Neo4jEventUtils::removeEventById($id);
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }

    //delete from mysql
    $SQL = "DELETE  FROM " . TBL_EVENTS . " WHERE id=" . $id;
    mysql_query($SQL) or die(mysql_error());

    $SQL = "DELETE  FROM " . TBL_COMMENT . " WHERE event_id=" . $id;
    mysql_query($SQL) or die(mysql_error());

    $SQL = "DELETE  FROM " . TBL_IMAGES . " WHERE eventId=" . $id;
    mysql_query($SQL) or die(mysql_error());

    //delete from redis
    $redis = new Predis\Client();
    $keys = $redis->keys("*");
    foreach ($keys as $key) {
        $events = $redis->zrevrange($key, 0, -1);
        foreach ($events as $item) {
            $evt = json_decode($item);
            if ($evt->id == $id) {
                RedisUtils::removeItem($redis, $key, $item);
                break;
            }
        }
    }
}
?>
