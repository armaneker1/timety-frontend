<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$result=new Result();
if(isset($_POST["g"]) && isset($_POST["u"]) && isset($_POST["r"]))
{
	$userId=$_POST["u"];
	$groupId=$_POST["g"];
	$resp=$_POST["r"];
	try {
		if(!empty( $userId) && !empty( $groupId)  && (!empty( $resp) || $resp==0))
		{
			$result=InviteUtil::responseToGroupInvites($userId, $groupId,$resp);
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
