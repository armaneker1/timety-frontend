<?php 
require 'utils/userFunctions.php'; 

$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];

$res=new Result();
$res->error=true;
$res->success=false;

try {
    if(!empty( $eventId))
    {
        $array =  CommentsFunctions::getCmmentListByEvent($eventId);
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