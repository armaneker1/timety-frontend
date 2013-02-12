<?php

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
if (isset($_GET['eventId']) && !empty($_GET['eventId'])) {
    $id = $_GET['eventId'];

    try {
        Neo4jEventUtils::removeEventById($id);
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }

    $SQL = "DELETE  FROM " . TBL_EVENTS . " WHERE id=" . $id;
    mysql_query($SQL) or die(mysql_error());
    
    $SQL = "DELETE  FROM " . TBL_COMMENT. " WHERE event_id=" . $id;
    mysql_query($SQL) or die(mysql_error());
    
    $SQL = "DELETE  FROM " . TBL_IMAGES. " WHERE eventId=" . $id;
    mysql_query($SQL) or die(mysql_error());
}
?>
