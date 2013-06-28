<?php 
session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$result=new Result();
try {
	$query=$_POST["g"];
	$user=$_POST["u"];
	$result->success=false;
	try {
		if(!empty( $query))
		{
			$result->success=GroupUtil::checkGroupName($query,$user);
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