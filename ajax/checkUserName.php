<?php 
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';

$result=new Result();
try {
	$query=$_POST["u"];
	$result->success=false;
	try {
		if(!empty( $query))
		{
			$result->success=UserUtils::checkUserName($query);
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