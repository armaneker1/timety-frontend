<?php 
require 'utils/userFunctions.php'; 

$userId=null;
$datetime=  date(DATETIME_DB_FORMAT);

if(isset($_POST["userId"]))
    $userId=$_POST["userId"];
$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];
$comment=null;
if(isset($_POST["comment"]))
    $comment=$_POST["comment"];
$res=new Result();
$res->error=true;
$res->success=false;

try {
    if(!empty( $comment) && !empty( $eventId) && !empty($userId))
    {
	$comm=new Comment();
        $comm->comment=$comment;
        $comm->datetime=$datetime;
        $comm->eventId=$eventId;
        $comm->userId=$userId;
        $comm=  CommentUtil::insert($comm);
        if(!empty($comm))
	{ 
           $json_response = json_encode($comm);
           echo $json_response;
        }
        else
        {
            $json_response = json_encode($res);
            echo $json_response;
        }
     } else
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
