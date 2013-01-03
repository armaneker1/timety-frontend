<?php 
require_once __DIR__.'/utils/Functions.php';
$result=new Result();
try {
	$query=$_POST["e"];
	$result->success=false;
	try {
		if(!empty( $query))
		{
			$userFunctions=new UserFuctions();
			$result->success=$userFunctions->checkEmail($query);
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