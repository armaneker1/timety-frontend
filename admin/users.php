<?php
session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Dashboard - Admin Template</title>
        <link rel="stylesheet" type="text/css" href="css/theme.css?<?= JS_CONSTANT_PARAM ?>" />
        <link rel="stylesheet" type="text/css" href="css/style.css?<?= JS_CONSTANT_PARAM ?>" />
        <script>
            document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '?<?= JS_CONSTANT_PARAM ?>">');
        </script>
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="css/ie-sucks.css?<?= JS_CONSTANT_PARAM ?>" />
        <![endif]-->
    </head>

    <body>
        <div id="container">
            <div id="header">
                <h2>Timety Administrator Panel</h2>
                <div id="topmenu">
                    <ul>
                        <li><a href="index.php">Statistics</a></li>
                        <li><a href="timetyCategoryList.php">Category Lists</a></li>
                        <li class="current"><a href="users.php">Users</a></li>
                        <li><a href="timetyCategory.php">Categories</a></li>
                        <li><a href="menuCategory.php">Menu Categories</a></li>
                        <li><a href="addLikeCat.php">Add Interest</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div id="wrapper">
                <div id="content">
                    <?php
                    $error = null;
                    if (isset($_GET["action"]) && $_GET["action"] == "delete") {
                        if (isset($_GET["userId"]) && !empty($_GET["userId"])) {
                            try {
                                $userId = $_GET["userId"];
                                //get user events 
                                try {
                                    $events = Neo4jUserUtil::getUserCreatedEvents($userId);
                                    if (!empty($events) && sizeof($events) > 0) {
                                        foreach ($events as $evt) {
                                            if (!empty($evt)) {
                                                $id = $evt->getProperty(PROP_USER_ID);
                                                if (!empty($id)) {
                                                    try {
                                                        Neo4jEventUtils::removeEventById($id);
                                                    } catch (Exception $exc) {
                                                        echo $exc->getTraceAsString();
                                                    }

                                                    //delete from mysql
                                                    $SQL = "DELETE  FROM " . TBL_EVENTS . " WHERE id=" . $id;
                                                    mysql_query($SQL);

                                                    $SQL = "DELETE  FROM " . TBL_COMMENT . " WHERE event_id=" . $id;
                                                    mysql_query($SQL);

                                                    $SQL = "DELETE  FROM " . TBL_IMAGES . " WHERE eventId=" . $id;
                                                    mysql_query($SQL);

                                                    ElasticSearchUtils::deleteFromSBIById($id);

                                                    //delete from redis
                                                    $redis = new Predis\Client();
                                                    $keys = $redis->keys("*");
                                                    $friendsKeys = $redis->keys("user:friend*");
                                                    foreach ($keys as $key) {
                                                        if (!in_array($key, $friendsKeys)) {
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
                                                }
                                            }
                                        }
                                    }
                                } catch (Exception $exc) {
                                    echo $exc->getTraceAsString();
                                }


                                Neo4jUserUtil::removeUserById($userId);
                                UserUtils::deleteUserSocialProviders($userId);
                                UserUtils::deleteUser($userId);
                                ElasticSearchUtils::deleteFromSBIById("user_" . $userId);

                                try {
                                    $redis = new Predis\Client();
                                    $redis->del(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS);
                                    $redis->del(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING);
                                    $redis->del(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY);
                                    $redis->del(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING);
                                    $redis->del(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_FOLLOWING);
                                } catch (Exception $exc) {
                                    echo $exc->getTraceAsString();
                                }


                                try {
                                    $redis = new Predis\Client();
                                    $friendsKeys = $redis->keys("user:friend*");
                                    foreach ($friendsKeys as $key) {
                                        $friends = $redis->zrevrange($key, 0, -1);
                                        foreach ($friends as $item) {
                                            $user = json_decode($item);
                                            $user = UtilFunctions::cast('User', $user);
                                            if (!empty($user) && $user->id == $userId) {
                                                RedisUtils::removeItem($redis, $key, $item);
                                                break;
                                            }
                                        }
                                    }
                                } catch (Exception $exc) {
                                    echo $exc->getTraceAsString();
                                }
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
                    $limit = 30;
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
                                <!--<td><a href="http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=delete&userId=" . $user->id ?>">Delete</a></td>-->
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
                </div>
            </div>
        </div>
    </body>
</html>