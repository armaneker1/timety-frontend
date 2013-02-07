<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];
if(isset($_GET["eventId"]))
    $eventId=$_GET["eventId"];

    
$res=new Result();
$res->error=true;
$res->success=false;

if(!empty($eventId))
{
   $result=Neo4jEventUtils::getEventFromNode($eventId,TRUE);
   $result->getHeaderImage();
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
