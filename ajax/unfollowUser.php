<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$fromUserId=null;
if(isset($_POST["fuser"]))
    $fromUserId=$_POST["fuser"];

$toUserId=null;
if(isset($_POST["tuser"]))
    $toUserId=$_POST["tuser"];

$result=new Result();
try {
	if(!empty( $fromUserId) && !empty( $toUserId))
	{
		$result=SocialFriendUtil::unfollowUser($fromUserId, $toUserId);
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
