<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$array = Neo4jTimetyCategoryUtil::getTimetyList("");

foreach ($array as $ar) {
   // echo "<h1>".$ar->name."(".$ar->id.")</h1>";
    $tags=  Neo4jTimetyTagUtil::getTimetyTagsFromCat($ar->id);
    foreach ($tags as $tag) {
          echo "".$ar->name."(".$ar->id.")->".$tag->name."(".$tag->id.")<br/>";
    }
  //  echo "<p/><br/>";
}
?>
