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


$usr_name_id="12";
if(preg_match("/^[0-9]/", $usr_name_id)){
    var_dump("evet");
}else{
    var_dump("hayır");
}

var_dump(UserUtils::getUserByUserName("biletix"));
/*
var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
var_dump(Neo4jTimetyTagUtil::findExactTag("Deneme1"));
var_dump(Neo4jTimetyTagUtil::findExactTag("Tes"));
 * 
 */
?>
