<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';

$users = UserUtils::getUserList(0, 100000);
$user = new User();
$f = new User();
foreach ($users as $user) {

    echo "<h2>" . $user->getFullName() . "($user->id)";
    echo "<h3>followlist</h3>";
    $follows = Neo4jUserUtil::getUserFollowList($user->id);
    foreach ($follows as $f) {
        echo "<h4>->" . $f->getFullName() . "($f->id)";
        RedisUtils::addUserFollow($user->id, $f->id,true);
        echo " -> done </h4>";
    }
    echo "<h3>followers</h3>";
    $followers = Neo4jUserUtil::getUserFollowerList($user->id);
    foreach ($followers as $f) {
        echo "<h4>->" . $f->getFullName() . "($f->id)";
        RedisUtils::addUserFollower($user->id, $f->id,true);
        echo " -> done </h4>";
    }

    echo "<br/><p/>";
}
?>
