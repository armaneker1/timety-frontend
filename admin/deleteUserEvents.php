<?php
session_start();
session_write_close();
header("charset=utf8");
ini_set('max_execution_time', 0);
require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$userId = null;
if (isset($_POST['userId']))
    $userId = $_POST['userId'];
if (isset($_GET['userId']))
    $userId = $_GET['userId'];


$eventCount = null;
if (isset($_POST['eventCount']))
    $eventCount = $_POST['eventCount'];
if (isset($_GET['eventCount']))
    $eventCount = $_GET['eventCount'];

if (empty($eventCount) || $eventCount > 10) {
    $eventCount = 10;
}

if (!empty($userId)) {
    $SQL = "SELECT * FROM `timete_events` WHERE `creator_id` = $userId";

    $result = mysql_query($SQL);
    $array = array();
    if (!empty($result)) {
        $num = mysql_num_rows($result);
        if ($num > 1) {
            while ($db_field = mysql_fetch_assoc($result)) {
                $event = new Event();
                $event->create($db_field);
                array_push($array, $event);
            }
        } else if ($num > 0) {
            $db_field = mysql_fetch_assoc($result);
            $event = new Event();
            $event->create($db_field);
            array_push($array, $event);
        }
    }

    if (!empty($array)) {
        $index = 0;
        foreach ($array as $event) {
            if ($index < $eventCount && !empty($event) && !empty($event->id)) {
                $index++;
                echo "<p/>";
                echo $event->id;
                echo "<p/>";
                try {
                    $return =EventUtil::removeEventById($event->id);
                    var_dump($return);
                } catch (Exception $exc) {
                    var_dump($exc);
                }
            }
        }
    }
    unset($array);
}
?>
<body>
    <form action="" method="POST">
        <input type="text" id="userId" name="userId" value="<?= $userId ?>">
        <input type="text" id="eventCount" name="eventCount" value="<?= $eventCount ?>">
        <input type="submit" name="save" value="Delete">
    </form>
</body>