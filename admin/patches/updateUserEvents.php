<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$page = 0;
$page_count = 5;
if (isset($_GET['page']))
    $page = (int) $_GET['page'];

$u_id = null;
$users = array();
if (isset($_GET['uid']) && !empty($_GET['uid'])) {
    $u_id = $_GET['uid'];
    $usr = UserUtils::getUserById($u_id);
    if (!empty($usr)) {
        array_push($users, $usr);
    }
} else {
    $users = UserUtils::getUserList($page, $page_count);
}
$lastId = null;
if (!empty($users)) {
    foreach ($users as $user) {
        $lastId = $user->id;
        Queue::updateProfile($user->id);
    }
} else {
    $users = array();
}
?>

<html>
    <head></head>
    <body>
        <h3>Done (Page : <?= $page ?>, Size : <?= sizeof($users) ?>, Last Id : <?= $lastId ?>)</h3>
        <a href="<?= HOSTNAME ?>admin/patches/updateUserEvents.php?page=<?= ($page + 1) ?>">Next Page</a>
    </body>
</html>
