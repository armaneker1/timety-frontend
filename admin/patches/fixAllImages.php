<?php

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$SQL = "SELECT * from " . TBL_IMAGES;
$query = mysql_query($SQL) or die(mysql_error());
$array = array();
if (!empty($query)) {
    $num = mysql_num_rows($query);
    if ($num > 1) {
        while ($db_field = mysql_fetch_assoc($query)) {
            $image = new Image();
            $image->createFromSQL($db_field);
            array_push($array, $image);
        }
    } else if ($num > 0) {
        $db_field = mysql_fetch_assoc($query);
        $image = new Image();
        $image->createFromSQL($db_field);
        array_push($array, $image);
    }
}

if (!empty($array) && sizeof($array) > 0) {
    foreach ($array as $img) {
        echo "<h2>$img->id ($img->eventId)</h2>";
        try {
            $size = ImageUtil::getSize(__DIR__ . "/../../" . $img->url);
            var_dump($size);
            if (!empty($size) && sizeof($size) == 4 && !empty($size[2]) && !empty($size[3])) {
                $SQL = "UPDATE " . TBL_IMAGES . " SET org_width= $size[2] ,org_height= $size[3]  WHERE id=$img->id";
                $query = mysql_query($SQL) or die(mysql_error());
                echo "<h3>Done </h3>";
            } else {
                echo "<h3>Error 01 </h3>";
            }
        } catch (Exception $exc) {
            echo "<h3>Error 02 </h3>";
        }
    }
}
?>
