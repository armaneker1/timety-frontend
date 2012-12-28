<?php
require 'utils/userFunctions.php'; 

$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];

    
$res=new Result();
$res->error=true;
$res->success=false;

if(!empty($eventId))
{
   $userFunc=new UserFuctions();
   $result=$userFunc->getEventById($_POST["eventId"]);
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
