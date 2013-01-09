<?php 
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/../utils/Functions.php';

$query=null;
if(isset($_GET["term"]))
    $query=$_GET["term"];

$userId=null;
if(isset($_GET["u"]))
    $userId=$_GET["u"];

try {
	if(!empty($query) && !empty( $userId))
	{
		//noramlly get neo4j
		$array=array();
		$result=array();
		//methoddan interestleri getir
		$array=SocialFriendUtil::getFriendList($userId, $query);
		//id nin basına isaret cak ; ile ayır
		if(!empty($array) && sizeof($array)>0)
		{
			$val=new User();
			for ($i=0; $i< sizeof($array);$i++) {
				$val=$array[$i];
				$val->label=$val->firstName." ".$val->lastName;
				array_push($result, $val);
			}
		}
		$json_response = json_encode($result);
		echo $json_response;
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
?>