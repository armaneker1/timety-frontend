<?php 
session_start();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$result=new Result();
try {
	$query="";
        if(isset($_POST["e"]))
        {
            $query=$_POST["e"];
        }else if(isset($_GET["e"]))
        {
             $query=$_GET["e"];
        }
	$result->success=false;
	try {
		if(!empty( $query))
		{
			$result->success=UserUtils::checkEmail($query);
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