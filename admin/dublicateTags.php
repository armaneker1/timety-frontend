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

$n=new Neo4jFuctions();
$array=$n->searchInterests("");

var_dump(sizeof($array));

$nameArray=array();
$tag=new Interest();
foreach ($array as $tag){
    $name=$tag->name;
    if(!empty($nameArray[$name]))
    {
        $tmp=$nameArray[$name];
        array_push($tmp, $tag);
        $nameArray[$name]=$tmp;
    }else{
        $tmp=array();
        array_push($tmp, $tag);
        $nameArray[$name]=$tmp;
    }
}

foreach ($nameArray as $tag)
{
    if(sizeof($tag)>1)
    {
        var_dump($tag);   
    }
}

?>
