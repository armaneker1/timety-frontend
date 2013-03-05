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

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../utils/Queue.php';



$users = UserUtils::getUserList(0, 100000);
foreach ($users as $user) {

    $tags = Neo4jUserUtil::getUserTimetyTag($user->id);
    if (!empty($tags)) {
        echo "<h1>".$user->getFullName()."</h1>";
        foreach ($tags as $t ){
            var_dump($t);
        }
    }
}
?>
