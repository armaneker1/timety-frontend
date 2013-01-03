<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';

$userId=null;
if(isset($_GET["userId"]))
    $userId=$_GET["userId"];

$pageNumber=null;
if(isset($_GET["pageNumber"]))
    $pageNumber=$_GET["pageNumber"];

$pageItemCount=null;
if(isset($_GET["pageItemCount"]))
    $pageItemCount=$_GET["pageItemCount"];

$date=date(DATETIME_DB_FORMAT);
if(isset($_GET["date"]))
    $date=$_GET["date"];

$query=null;
if(isset($_GET["query"]))
    $query=$_GET["query"];

$type=null;
if(isset($_GET["type"]))
    $type=$_GET["type"];
    
$res=new Result();
$res->error=true;
$res->success=false;

if($userId!=null  && $pageNumber!=""  && $pageItemCount!=null  && $type!=null )
{
    $result=Neo4jFuctions::getEvents($userId, $pageNumber, $pageItemCount, $date, $query, $type);
    if(!empty($result))
    {
        $json_response = json_encode($result);
        echo $json_response;
    }else
    { 
        $json_response = json_encode($res);
        echo $json_response;
    }
}else
{ 
    $json_response = json_encode($res);
    echo $json_response;
}

?>
