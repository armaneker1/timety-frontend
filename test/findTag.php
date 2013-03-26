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


$usr_name_id = "asdasda asdsad";
$usr_name_id = preg_replace('/\s+/', '', $usr_name_id);
var_dump($usr_name_id);
/*
  var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
  var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
  var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
  var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
  var_dump(Neo4jTimetyTagUtil::findExactTag("Tes"));
 * 
 */
?>
