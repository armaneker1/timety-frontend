<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

if (isset($_POST['eventId']) && !empty($_POST['eventId'])) {
    $id = $_POST['eventId'];

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
    $friendsKeys = $redis->keys("user:friend*");
    $socialKeys = $redis->keys("user:social*");
    foreach ($keys as $key) {
        if (!in_array($key, $friendsKeys) && !in_array($key, $socialKeys)) {
            $events = $redis->zrevrange($key, 0, -1);
            foreach ($events as $item) {
                $evt = json_decode($item);
                if (!empty($evt) && $evt->id == $id) {
                    RedisUtils::removeItem($redis, $key, $item);
                    break;
                }
            }
        }
    }

    EventKeyListUtil::deleteAllRecordForEvent($id);
    echo "Event Deleted";
}
?>

<body>
    <form action="" method="POST">
        <input type="text" id="eventId" name="eventId" value="">
        <input type="submit" name="save" value="Delete">
    </form>
</body>
