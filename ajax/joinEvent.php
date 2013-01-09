<?php 
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';

$userId=null;
if(isset($_POST["userId"]))
    $userId=$_POST["userId"];

$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];


$res=new Result();
$res->error=true;
$res->success=false;

try {
	if(!empty( $eventId) && !empty( $userId))
	{
            $result= InviteUtil::responseToEventInvites($userId, $eventId, 1);
            if(empty($result) || $result->error || !$result->success )
            {
                $res->error=true;
                $res->success=false;
                array_push($res->param,"An Error Occured");
            }  else {
                $res=new Result();
                $res->error=false;
                $res->success=true;
            }
	}else
        {
            array_push($res->param,"Parameters Invalid");
        }
} catch (Exception $e) {
      array_push($res->param,$e->getMessage());
}
$json_response = json_encode($res);
echo $json_response;
?>