<?php 
require 'utils/userFunctions.php'; 

$eventId=null;
if(isset($_GET["eventId"]))
    $eventId=$_GET["eventId"];

$res=new Result();
$res->error=true;
$res->success=false;

try {
    if(!empty( $eventId))
    {
        $array = Neo4jFuctions::getEventAttendances($eventId);
        if(!empty($array))
	{ 
           $json_response = json_encode($array);
           echo $json_response;
        }
        else
        {
            $json_response = json_encode($res);
            echo $json_response;
        }
     }
      else
        {
            $json_response = json_encode($res);
            echo $json_response;
        }
} catch (Exception $e) {
    $res->error=$e->getMessage();
    $json_response = json_encode($res);
    echo $json_response;
}
?>
