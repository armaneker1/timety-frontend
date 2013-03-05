<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

$error = null;
if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    if (isset($_GET["userId"]) && !empty($_GET["userId"])) {
        try {
            $userId = $_GET["userId"];
            // get events user created from neo4j
            //$events=Neo4jUserUtil::getUserCreatedEvents($userId);
            //var_dump($events);
            // delete user node with relation from neo4j
            Neo4jUserUtil::removeUserById($userId);
            // delete user social providers
            UserUtils::deleteUserSocialProviders($userId);
            // delete user from tbale
            UserUtils::deleteUser($userId);
            echo "Silindi";
        } catch (Exception $exc) {
            $error = "Erro while deleting";
            echo $error;
            var_dump($exc);
        }
    }
}


$page = 0;
if (isset($_GET["page"])) {
    $page = $_GET["page"];
}
if ($page < 0) {
    $page = 0;
}
$limit = 0;
if (isset($_GET["limit"])) {
    $limit = $_GET["limit"];
}
if ($limit < 0) {
    $limit = 0;
}
$userList = UserUtils::getUserList($page, $limit);
?>
<h1>Users</h1>
<table>
    <tr>
        <td>User Id</td>
        <td>User Image</td>
        <td>User Name</td>
        <td>User First Name</td>
        <td>User Last Name</td>
        <td>User Email</td>
        <td>User Status</td>
        <td>User Lang</td>
        <td>Action</td>
    </tr>

    <?php
    $user = new User();
    foreach ($userList as $user) {
        ?>
        <tr>
            <td><?= $user->id ?></td>
            <td><img height="20" src="<?= $user->getUserPic() ?>"/></td>
            <td><?= $user->userName ?></td>
            <td><?= $user->firstName ?></td>
            <td><?= $user->lastName ?></td>
            <td><?= $user->email ?></td>
            <td><?= $user->status ?></td>
            <td><?= $user->getUserLang() ?></td>
            <td><a href="http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=delete&userId=" . $user->id ?>">Delete</a></td>
        </tr>
    <?php } ?>
    <tr >
        <td colspan="10" >
    <center>
        <a <?php
    if ($page < 0) {
        echo "onclick='return false;'";
    }
    ?> href="http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?page=" . ($page - 1) ?>">Prev</a>
        <a <?php
            if (empty($userList)) {
                echo "onclick='return false;'";
            }
    ?> href="http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?page=" . ($page + 1) ?>">Next</a>
    </center>
</td>
</tr>
</table>



