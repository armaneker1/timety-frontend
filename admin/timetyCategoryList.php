<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$array = Neo4jTimetyCategoryUtil::getTimetyList("");
echo "<table><tbody>";
foreach ($array as $ar) {
    $tags=  Neo4jTimetyTagUtil::getTimetyTagsFromCat($ar->id);
    
    foreach ($tags as $tag) {
          echo "<tr><td>".$ar->name."(".$ar->id.")</td><td>".$tag->name."(".$tag->id.")</td></tr>";
    }
}
echo "</tbody></table>";
?>
