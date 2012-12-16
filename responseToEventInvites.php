<?php 
require 'utils/userFunctions.php';
$result=new Result();
if(isset($_POST["e"]) && isset($_POST["u"]) && isset($_POST["r"]))
{
	$userId=$_POST["u"];
	$eventId=$_POST["e"];
	$resp=$_POST["r"];
	try {
		if(!empty( $userId) && !empty( $eventId)  && (!empty( $resp) || $resp==0))
		{
			$userFunctions=new UserFuctions();
			$result=$userFunctions->responseToEventInvites($userId, $eventId,$resp);
		}
	} catch (Exception $e) { 
		$result->error=$e->getMessage();
	}
} else
{
	$result->error=true;
}
$json_response = json_encode($result);
echo $json_response;
?>
