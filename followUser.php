<?php 
require_once __DIR__.'/utils/Functions.php';
$fromUserId=$_POST["fuser"];
$toUserId=$_POST["tuser"]; 

$result=new Result();
try {
	if(!empty( $fromUserId) && !empty( $toUserId))
	{
		$userFunctions=new UserFuctions();
		$result=SocialFriendUtil::followUser($fromUserId, $toUserId);
	}
} catch (Exception $e) {
	$result->error=$e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>
