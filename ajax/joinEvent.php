<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$userId=null;
if(isset($_POST["userId"]))
    $userId=$_POST["userId"];

$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];

$type=null;
if(isset($_POST["type"]))
    $type=$_POST["type"];


$res=new Result();
$res->error=true;
$res->success=false;

try {
	if(!empty( $eventId) && !empty( $userId))
	{
            if(empty($type) || $type<0 || $type>3)
            {
                $type=0;
            }
            $result= InviteUtil::responseToEventInvites($userId, $eventId, $type);
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
