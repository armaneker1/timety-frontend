<?php 
require 'utils/userFunctions.php'; 
$result=new Result();
try {
	$query=$_POST["g"];
	$user=$_POST["u"];
	$result->success=false;
	try {
		if(!empty( $query))
		{
			$userFunctions=new UserFuctions();
			$result->success=$userFunctions->checkGroupName($query,$user);
		}else
		{
			$result->success=true;
		}
	} catch (Exception $e) {
		$result->success=false;
	}
} catch (Exception $e) {
	$result->success=false;
	$result->error=$e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>