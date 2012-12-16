<?php 
require 'utils/userFunctions.php'; 
$result=new Result();
try {
	$query=$_POST["e"];
	$result->success=false;
	$res=UserFuctions::sendEmail("to join timety please click <a href='http://fabelist.com/timete/web/signin.php'>here</a> ", "Timety invite",'{"email": "'.$query.'",  "name": "Hasan "}');
	if($res[0]->status=="sent")
	{
		$result->success=true;
	}
	$result->param=$res;
} catch (Exception $e) {
	$result->success=false;
	$result->error=$e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>