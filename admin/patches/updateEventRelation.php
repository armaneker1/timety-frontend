<?php

ini_set('max_execution_time', 600);
session_start();session_write_close();
header("charset=utf8");


$uuuuuId = 6618346;

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

function updateEvent($key, $userId) {
    /*if ($userId != 6618346) {
        var_dump("skiiped");
        return;
    }*/
    if (empty($userId)) {
        var_dump("User Id empty");
        return;
    }
    if (empty($key)) {
        var_dump("Key empty");
        return;
    }
    $usr = UserUtils::getUserById($userId);
    if (empty($usr)) {
        var_dump("User not found");
        return;
    }
    $redis = new Predis\Client();
    $events = $redis->zrange($key, 0, -1);
    var_dump("Size  : " . sizeof($events));
    $evt = new Event();
    foreach ($events as $evt) {
        try {
            $event = json_decode($evt);
            $event = UtilFunctions::cast('Event', $event);
            $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($event->id, $userId);
            if (empty($event->userRelation)) {
                var_dump("<Error2");
                var_dump($evt);
            } else {
                echo "<p/>event id : " . $event->id . "<p/>";
                var_dump($event->userRelation);
                $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                $result = $redis->removeItemById($key, $event->id);
                var_dump("result::::".$result);
                RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                var_dump("added");
            }
        } catch (Exception $exc) {
            var_dump("<Error");
            var_dump($evt);
            var_dump($exc);
            var_dump("<Error");
        }
    }
}

$redis = new Predis\Client();
$keys = $redis->keys("*");
foreach ($keys as $key) {
    if (preg_match("/^" . REDIS_PREFIX_USER . "(.*?)" . REDIS_SUFFIX_UPCOMING . "/", $key, $matches)) {
        echo "<h1>Upcoming</h1>";
        var_dump($key);
        $userId = "";
        if (!empty($matches)) {
            echo "<h3>Size " . sizeof($matches) . "</h3>";
            if (sizeof($matches) == 2) {
                var_dump($matches[1]);
                $userId = $matches[1];
            } else {
                var_dump($matches[0]);
                $userId = $matches[0];
            }
            updateEvent($key, $userId);
        } else {
            echo "<p>UserId not found</p>";
        }
    } else if (preg_match("/^" . REDIS_PREFIX_USER . "(.*?)" . REDIS_SUFFIX_MY_TIMETY . "/", $key, $matches)) {
        echo "<h1>My Timety</h1>";
        var_dump($key);
        $userId = "";
        if (!empty($matches)) {
            echo "<h3>Size " . sizeof($matches) . "</h3>";
            if (sizeof($matches) == 2) {
                var_dump($matches[1]);
                $userId = $matches[1];
            } else {
                var_dump($matches[0]);
                $userId = $matches[0];
            }
            updateEvent($key, $userId);
        } else {
            echo "<p>UserId not found</p>";
        }
    } else if (preg_match("/^" . REDIS_PREFIX_USER . "(.*?)" . REDIS_SUFFIX_FOLLOWING . "/", $key, $matches)) {
        echo "<h1>Following</h1>";
        var_dump($key);
        $userId = "";
        if (!empty($matches)) {
            echo "<h3>Size " . sizeof($matches) . "</h3>";
            if (sizeof($matches) == 2) {
                var_dump($matches[1]);
                $userId = $matches[1];
            } else {
                var_dump($matches[0]);
                $userId = $matches[0];
            }
            updateEvent($key, $userId);
        } else {
            echo "<p>UserId not found</p>";
        }
    }
}
//preg_match("/^" . REDIS_PREFIX_USER . "(.*?)" . REDIS_SUFFIX_UPCOMING . "/", $key)
?>
