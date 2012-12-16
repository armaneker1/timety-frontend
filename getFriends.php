<?php 
require 'utils/userFunctions.php'; 
$query=$_GET["term"];
$userId=$_GET["u"];
try {
	if(!empty($query) && !empty( $userId))
	{
		$userFunctions=new UserFuctions();
		//noramlly get neo4j
		$array=array();
		$result=array();
		//methoddan interestleri getir
		$array=$userFunctions->getFriendList($userId, $query);
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
