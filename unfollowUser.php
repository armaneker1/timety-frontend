<?php 
require 'utils/userFunctions.php';
$fromUserId=$_POST["fuser"];
$toUserId=$_POST["tuser"];

$result=new Result();
try {
	if(!empty( $fromUserId) && !empty( $toUserId))
	{
		$userFunctions=new UserFuctions();
		$result=$userFunctions->unfollowUser($fromUserId, $toUserId);
	}else 
	{
		$result->error="User not exists";
	}
} catch (Exception $e) {
	$result->error=$e->getMessage();
}

$json_response = json_encode($result);
echo $json_response;
?>
